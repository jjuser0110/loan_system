<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Expense;

/**
 * One-time fix for old expense logs.
 *
 * Before the fix, "Expense Updated" and "Expense Deleted" logs were saved against the bank
 * account (PaymentMethod) instead of the expense, so the cash book history could not tell
 * which expense an older edit belonged to ("Expense #- Updated Edit", wrong date).
 *
 * This command follows each expense's chain of amounts in log order and links those old
 * logs to their expense (content_type / content_id only, amounts are never changed).
 *
 *   php artisan expenses:link-edit-logs           -> dry run, shows what it would change
 *   php artisan expenses:link-edit-logs --apply   -> writes the changes (back up the DB first)
 */
class LinkExpenseEditLogs extends Command
{
    protected $signature   = 'expenses:link-edit-logs {--apply : Write the changes (default is a dry run)}';
    protected $description = 'Link old "Expense Updated/Deleted" logs to their expense (one-time fix)';

    private const EXPENSE_TYPE = 'App\\Models\\Expense';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');

        $logs = DB::table('payment_method_logs')
            ->where('type', 'expense')
            ->orderBy('id')
            ->get();

        $expenses = Expense::withTrashed()->get()->keyBy('id');

        // what each expense looks like at this point in the log: [amount, payment_method_id]
        $state   = [];
        $links   = [];   // log id => expense id
        $skipped = [];   // log id => reason
        $used    = [];   // log ids already handled as the 2nd half of an edit

        $isLinked = fn ($log) => $log->content_type === self::EXPENSE_TYPE;
        $same     = fn ($a, $b) => abs((float) $a - (float) $b) < 0.005;

        foreach ($logs as $i => $log) {
            if (isset($used[$log->id])) continue;

            // ---- created: start tracking the expense
            if ($log->description === 'Expense Created' && $isLinked($log)) {
                $state[$log->content_id] = [(float) $log->amount, $log->payment_method_id];
                continue;
            }

            // ---- edited: logs come in pairs (take old amount out, put new amount in)
            if ($log->description === 'Expense Updated') {
                $reverse = $log;
                $applyLog = null;
                for ($j = $i + 1; $j < count($logs); $j++) {
                    $next = $logs[$j];
                    if ($next->description === 'Expense Updated' && !isset($used[$next->id])
                        && abs(strtotime($next->created_at) - strtotime($reverse->created_at)) <= 2) {
                        $applyLog = $next;
                        break;
                    }
                }

                if ($isLinked($reverse)) {
                    $expenseId = $reverse->content_id;
                } else {
                    // the expense whose current amount (on this account) is what was taken out
                    $candidates = collect($state)->filter(function ($s, $id) use ($reverse, $same, $expenses) {
                        $e = $expenses->get($id);
                        return $s[1] == $reverse->payment_method_id
                            && $same($s[0], -1 * $reverse->amount)
                            && $e && $e->created_at <= $reverse->created_at
                            // an expense edited at this time must have been updated at or after it
                            && strtotime($e->updated_at) >= strtotime($reverse->created_at) - 2
                            && (!$e->deleted_at || $e->deleted_at >= $reverse->created_at);
                    })->keys();

                    if ($candidates->count() !== 1) {
                        $skipped[$reverse->id] = $candidates->count() === 0
                            ? 'no matching expense'
                            : 'more than one possible expense: ' . $candidates->implode(', ');
                        if ($applyLog) {
                            $used[$applyLog->id] = true;
                            $skipped[$applyLog->id] = 'other half of skipped log #' . $reverse->id;
                        }
                        continue;
                    }
                    $expenseId = $candidates->first();
                    $links[$reverse->id] = $expenseId;
                }

                if ($applyLog) {
                    $used[$applyLog->id] = true;
                    if (!$isLinked($applyLog)) {
                        $links[$applyLog->id] = $expenseId;
                    }
                    $state[$expenseId] = [(float) $applyLog->amount, $applyLog->payment_method_id];
                }
                continue;
            }

            // ---- deleted
            if (str_starts_with((string) $log->description, 'Expense Deleted')) {
                if ($isLinked($log)) {
                    unset($state[$log->content_id]);
                    continue;
                }
                $candidates = collect($state)->filter(function ($s, $id) use ($log, $same, $expenses) {
                    $e = $expenses->get($id);
                    return $s[1] == $log->payment_method_id
                        && $same($s[0], -1 * $log->amount)
                        && $e && $e->deleted_at
                        && abs(strtotime($e->deleted_at) - strtotime($log->created_at)) <= 5;
                })->keys();

                if ($candidates->count() !== 1) {
                    $skipped[$log->id] = $candidates->count() === 0
                        ? 'no matching deleted expense'
                        : 'more than one possible expense: ' . $candidates->implode(', ');
                    continue;
                }
                $links[$log->id] = $candidates->first();
                unset($state[$candidates->first()]);
            }
        }

        // ---- report
        $rows = [];
        foreach ($links as $logId => $expenseId) {
            $log = $logs->firstWhere('id', $logId);
            $rows[] = [$logId, $log->created_at, $log->description, $log->amount, $expenses[$expenseId]->expense_code ?? $expenseId];
        }
        $this->table(['Log ID', 'Logged at', 'Description', 'Amount', 'Linked to expense'], $rows);

        foreach ($skipped as $logId => $reason) {
            $this->warn("Skipped log #{$logId}: {$reason}");
        }

        // sanity check: tracked amounts should equal the expenses' real current amounts
        foreach ($state as $id => [$amount, $pm]) {
            $e = $expenses->get($id);
            if ($e && !$e->deleted_at && !$same($e->amount, $amount)) {
                $this->warn("Check expense {$e->expense_code}: log chain gives {$amount}, expense table has {$e->amount}");
            }
        }

        $this->info(count($links) . ' log(s) to link, ' . count($skipped) . ' skipped.');

        if (!$apply) {
            $this->info('Dry run only. Nothing was changed. Run again with --apply to save.');
            return 0;
        }

        // backup of the rows being changed
        $backup = storage_path('logs/expense_log_link_backup_' . date('Ymd_His') . '.csv');
        $fh = fopen($backup, 'w');
        fputcsv($fh, ['id', 'old_content_type', 'old_content_id', 'new_content_id']);
        foreach ($links as $logId => $expenseId) {
            $log = $logs->firstWhere('id', $logId);
            fputcsv($fh, [$logId, $log->content_type, $log->content_id, $expenseId]);
        }
        fclose($fh);

        DB::transaction(function () use ($links) {
            foreach ($links as $logId => $expenseId) {
                DB::table('payment_method_logs')->where('id', $logId)->update([
                    'content_type' => self::EXPENSE_TYPE,
                    'content_id'   => $expenseId,
                ]);
            }
        });

        $this->info('Done. Backup of the old values: ' . $backup);
        return 0;
    }
}

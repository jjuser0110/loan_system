<?php

namespace App\Traits;

/**
 * Shared schedule-code generator.
 *
 * Previously copied into PaymentController, RolloverSkimASchedule and
 * BackfillSkimASchedule (and called, but missing, in LoanController).
 */
trait IncrementsScheduleCode
{
    /**
     * Build the next schedule code after $code for the given loan.
     * e.g. LN001-S003 -> LN001-S004, LN001-SM009 -> LN001-SM010
     */
    protected function incrementScheduleCode(string $code, string $loanCode): string
    {
        $suffix = substr($code, strlen($loanCode));
        $suffix = preg_replace('/-\d+$/', '', $suffix);

        if (preg_match('/^(.*?)(\d+)$/', $suffix, $matches)) {
            $suffixPrefix = $matches[1]; // e.g. -SM or -S
            $number       = (int) $matches[2];
            $padded       = str_pad($number + 1, strlen($matches[2]), '0', STR_PAD_LEFT);
            return $loanCode . $suffixPrefix . $padded;
        }

        return $loanCode . '-S001';
    }
}

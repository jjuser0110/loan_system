<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    @page {
        margin: 130px 25px 60px 25px;
    }
    body { font-family: Arial, sans-serif; font-size: 10px; color: #222; }
    header {
        position: fixed; top: -110px; left: 0; right: 0; height: 100px;
    }
    footer {
        position: fixed; bottom: -40px; left: 0; right: 0; height: 30px;
        font-size: 9px; text-align: right; border-top: 1px solid #999; padding-top: 4px;
    }
    footer .page:after { content: counter(page) " of " counter(pages); }
    .report-title { font-size: 16px; font-weight: bold; margin-bottom: 2px; }
    .report-meta { font-size: 10px; color: #555; }
    table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    th, td { border: 1px solid #ccc; padding: 3px 5px; text-align: right; }
    th { background: #f0f0f0; }
    td.text-left, th.text-left { text-align: left; }
    tr.group-header td {
        background: #e8e8e8; font-weight: bold; text-align: left;
    }
    tr.subtotal-row td { background: #f7f7f7; font-weight: bold; }
    tr.grand-total-row td { background: #ddd; font-weight: bold; }
</style>
</head>
<body>

<header>
    <div class="report-title">{{ $company->company_code ?? 'ALL COMPANIES' }} — Loan Listing by Interest Total</div>
    <div class="report-meta">
        Generated: {{ $generatedAt->format('d-m-Y H:i') }}
        @if($fromDate || $toDate)
            &nbsp;|&nbsp; Period: {{ $fromDate ?: '-' }} to {{ $toDate ?: '-' }}
        @endif
    </div>
</header>

<footer>
    <span class="page"></span>
</footer>

<table>
    <thead>
        <tr>
            <th class="text-left">System Code</th>
            <th class="text-left">Customer Name</th>
            <th class="text-left">Loan Code</th>
            <th class="text-left">User</th>
            <th>Loan Date</th>
            <th>Next Date</th>
            <th>Last Pay</th>
            <th>Total to Collect</th>
            <th>Loan Amt</th>
            <th>Interest Collect</th>
            <th>Top Up</th>
            <th>Process Fee</th>
            <th>Capital</th>
            <th>Discount</th>
            <th>Loan Balance</th>
            <th class="text-left">Status</th>
        </tr>
    </thead>
    <tbody>
    @foreach($grouped as $groupName => $rows)
        <tr class="group-header">
            <td colspan="16">{{ $groupName }} ({{ $rows->count() }} loans)</td>
        </tr>
        @foreach($rows as $row)
        <tr>
            <td class="text-left">{{ $row['system_code'] }}</td>
            <td class="text-left">{{ $row['customer_name'] }}</td>
            <td class="text-left">{{ $row['loan_code'] }}</td>
            <td class="text-left">{{ $row['user'] }}</td>
            <td>{{ $row['loan_date'] }}</td>
            <td>{{ $row['next_due_date'] }}</td>
            <td>{{ $row['last_pay_date'] }}</td>
            <td>{{ number_format($row['total_to_collect'], 2) }}</td>
            <td>{{ number_format($row['loan_amount'], 2) }}</td>
            <td>{{ number_format($row['interest_collect'], 2) }}</td>
            <td>{{ number_format($row['top_up'], 2) }}</td>
            <td>{{ number_format($row['processing_fee'], 2) }}</td>
            <td>{{ number_format($row['capital'], 2) }}</td>
            <td>{{ number_format($row['discount'], 2) }}</td>
            <td>{{ number_format($row['loan_balance'], 2) }}</td>
            <td class="text-left">{{ $row['status'] }}</td>
        </tr>
        @endforeach
        @php $subtotal = $calculateTotals($rows); @endphp
        <tr class="subtotal-row">
            <td colspan="7" class="text-left">Subtotal — {{ $groupName }}</td>
            <td>{{ number_format($subtotal['total_to_collect'], 2) }}</td>
            <td>{{ number_format($subtotal['loan_amount'], 2) }}</td>
            <td>{{ number_format($subtotal['interest_collect'], 2) }}</td>
            <td>{{ number_format($subtotal['top_up'], 2) }}</td>
            <td>{{ number_format($subtotal['processing_fee'], 2) }}</td>
            <td>{{ number_format($subtotal['capital'], 2) }}</td>
            <td>{{ number_format($subtotal['discount'], 2) }}</td>
            <td>{{ number_format($subtotal['loan_balance'], 2) }}</td>
            <td></td>
        </tr>
    @endforeach

    <tr class="grand-total-row">
        <td colspan="7" class="text-left">GRAND TOTAL</td>
        <td>{{ number_format($grandTotals['total_to_collect'], 2) }}</td>
        <td>{{ number_format($grandTotals['loan_amount'], 2) }}</td>
        <td>{{ number_format($grandTotals['interest_collect'], 2) }}</td>
        <td>{{ number_format($grandTotals['top_up'], 2) }}</td>
        <td>{{ number_format($grandTotals['processing_fee'], 2) }}</td>
        <td>{{ number_format($grandTotals['capital'], 2) }}</td>
        <td>{{ number_format($grandTotals['discount'], 2) }}</td>
        <td>{{ number_format($grandTotals['loan_balance'], 2) }}</td>
        <td></td>
    </tr>
    </tbody>
</table>

</body>
</html>
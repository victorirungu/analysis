<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php-error.log');

session_start();
$title = "Transactions";
require_once 'database/dbconnect.php';

//---------------------------------------------------------
// 1) FETCH THE LATEST JSON FILE PATH FROM DATABASE
//---------------------------------------------------------
$latestFile = null;
$stmt = $conn->prepare("SELECT file_name FROM revenue ORDER BY created_at DESC LIMIT 1");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $latestFile = $result->fetch_assoc()['file_name'] ?? null;
}

//---------------------------------------------------------
// 2) READ THE JSON DATA
//---------------------------------------------------------
$jsonEntries = [];
if ($latestFile && file_exists($latestFile)) {
    $jsonContent = file_get_contents($latestFile);
    $jsonEntries = json_decode($jsonContent, true) ?: [];
}

//---------------------------------------------------------
// 3) PARSE DATES & SORT ALL TRANSACTIONS BY DATE/TIME ASC
//---------------------------------------------------------
/**
 * Parses "date" field like "1/1/2024 4:02:07 PM" into a DateTime.
 */
function parseDate(?string $dateStr): ?DateTime {
    if (!$dateStr) return null;
    // Try known format (month/day/year, single-digit possible, 12-hour time).
    $dt = DateTime::createFromFormat('n/j/Y g:i:s A', $dateStr);
    if (!$dt) {
        // Optionally: fallback attempt letting PHP guess the format
        try {
            $dt = new DateTime($dateStr);
        } catch (\Exception $e) {
            return null;
        }
    }
    return $dt;
}

// Add a parsed DateTime to each record; if invalid date, skip the record
$validRecords = [];
foreach ($jsonEntries as $entry) {
    $dt = parseDate($entry['date'] ?? '');
    if ($dt) {
        $entry['_dt'] = $dt;
        $validRecords[] = $entry;
    }
}

// Sort by that parsed DateTime ascending
usort($validRecords, function($a, $b) {
    /** @var DateTime $ad */
    $ad = $a['_dt'];
    /** @var DateTime $bd */
    $bd = $b['_dt'];
    return $ad <=> $bd;
});

//---------------------------------------------------------
// 4) GROUP BY MONTH (YYYY-MM), ACCUMULATE MONTHLY TOTALS
//---------------------------------------------------------
/*
   We'll build a structure like:
   $monthData = [
     '2024-01' => [
        'records' => [ ... all records in January 2024 ... ],
        'incomeAmount' => float,
        'incomeCommission' => float,
        'incomeNet' => float,
        'withdrawalAmount' => float,
        'withdrawalNet' => float
     ],
     '2024-02' => [ ... etc ... ]
   ];
*/

$monthData = [];
foreach ($validRecords as $entry) {
    /** @var DateTime $dt */
    $dt = $entry['_dt'];
    $yearMonth = $dt->format('Y-m'); // e.g. "2024-01"

    // If not exist, init
    if (!isset($monthData[$yearMonth])) {
        $monthData[$yearMonth] = [
            'records' => [],
            'incomeAmount' => 0.0,
            'incomeCommission' => 0.0,
            'incomeNet' => 0.0,
            'withdrawalAmount' => 0.0,
            'withdrawalNet' => 0.0,
        ];
    }

    // Push the record
    $monthData[$yearMonth]['records'][] = $entry;

    // Accumulate monthly sums
    $txType = strtolower($entry['transaction_type'] ?? 'income');
    $amount = floatval($entry['amount'] ?? 0);
    $comm   = floatval($entry['commission'] ?? 0);
    $net    = floatval($entry['net_amount'] ?? 0);

    if ($txType === 'income') {
        $monthData[$yearMonth]['incomeAmount']     += $amount;
        $monthData[$yearMonth]['incomeCommission'] += $comm;
        $monthData[$yearMonth]['incomeNet']        += $net;
    } else {
        // withdrawal
        $monthData[$yearMonth]['withdrawalAmount'] += $amount;
        $monthData[$yearMonth]['withdrawalNet']    += $net;
    }
}

// We'll also maintain grand totals
$grandIncome_Amount = 0.0;
$grandIncome_Commission = 0.0;
$grandIncome_Net = 0.0;
$grandWithdrawal_Amount = 0.0;
$grandWithdrawal_Net = 0.0;

/**
 * Format amounts:
 *  - If withdrawal, optionally wrap in parentheses
 *  - Always 2 decimals
 */
function fmtAmount(float $val, string $txType): string {
    $formatted = number_format($val, 2);
    return (strtolower($txType) === 'withdrawal') ? "({$formatted})" : $formatted;
}
/** Format commission: for withdrawal => "-" */
function fmtCommission(float $val, string $txType): string {
    return (strtolower($txType) === 'withdrawal')
        ? '-'
        : number_format($val, 2);
}
function fmtNet(float $val, string $txType): string {
    $formatted = number_format($val, 2);
    return (strtolower($txType) === 'withdrawal') ? "({$formatted})" : $formatted;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; margin: 20px; }
        table.dataTable thead th {
            background-color: #4CAF50; color: white;
        }
        .monthly-subtotal {
            background-color: #eef; font-weight: bold;
        }
        .grand-total {
            background-color: #cfc; font-weight: bold;
        }
        .dt-right { text-align: right; }
    </style>
</head>
<body>

<h2>Transactions</h2>
<table id="txnTable" class="display nowrap" style="width:100%">
    <thead>
        <tr>
            <th>Date</th>
            <th>Confirmation Code</th>
            <th class="dt-right">Amount</th>
            <th class="dt-right">Commission</th>
            <th class="dt-right">Net Amount</th>
            <th>Transaction Type</th>
        </tr>
    </thead>
    <tbody>

    <?php
    // Sort the $monthData keys in ascending order (they're "YYYY-MM")
    $sortedMonths = array_keys($monthData);
    sort($sortedMonths, SORT_STRING);

    foreach ($sortedMonths as $ym) {
        // $ym is "2024-01" etc.
        $info = $monthData[$ym];
        $records = $info['records'];

        // We can format a month label like "January 2024"
        // by adding "-01" to $ym and then format 'F Y'
        $monthLabelObj = DateTime::createFromFormat('Y-m-d', $ym . '-01');
        $monthLabel = $monthLabelObj ? $monthLabelObj->format('F Y') : $ym;

        // 1) Print each row in ascending date/time (already sorted globally)
        foreach ($records as $r) {
            $txType = strtolower($r['transaction_type'] ?? 'income');
            $amt  = floatval($r['amount'] ?? 0);
            $comm = floatval($r['commission'] ?? 0);
            $net  = floatval($r['net_amount'] ?? 0);
            ?>
            <tr>
                <td><?= htmlspecialchars($r['date'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['confirmation_code'] ?? '') ?></td>
                <td class="dt-right"><?= fmtAmount($amt, $txType) ?></td>
                <td class="dt-right"><?= fmtCommission($comm, $txType) ?></td>
                <td class="dt-right"><?= fmtNet($net, $txType) ?></td>
                <td><?= ucfirst($txType) ?></td>
            </tr>
            <?php
        }

        // 2) Print the monthly subtotals
        // Income
        ?>
        <tr class="monthly-subtotal">
            <td>Monthly Income Totals (<?= $monthLabel ?>)</td>
            <td></td>
            <td class="dt-right"><?= number_format($info['incomeAmount'], 2) ?></td>
            <td class="dt-right"><?= number_format($info['incomeCommission'], 2) ?></td>
            <td class="dt-right"><?= number_format($info['incomeNet'], 2) ?></td>
            <td>INCOME</td>
        </tr>
        <?php
        // Withdrawal
        ?>
        <tr class="monthly-subtotal">
            <td>Monthly Withdrawal Totals (<?= $monthLabel ?>)</td>
            <td></td>
            <td class="dt-right"><?= number_format($info['withdrawalAmount'], 2) ?></td>
            <td class="dt-right">-</td>
            <td class="dt-right"><?= number_format($info['withdrawalNet'], 2) ?></td>
            <td>WITHDRAWAL</td>
        </tr>
        <?php
        // 3) Accumulate to grand totals
        $grandIncome_Amount     += $info['incomeAmount'];
        $grandIncome_Commission += $info['incomeCommission'];
        $grandIncome_Net        += $info['incomeNet'];

        $grandWithdrawal_Amount += $info['withdrawalAmount'];
        $grandWithdrawal_Net    += $info['withdrawalNet'];
    }
    ?>
    </tbody>
    <tfoot>
        <tr class="grand-total">
            <td>Grand Total Income</td>
            <td></td>
            <td class="dt-right"><?= number_format($grandIncome_Amount, 2) ?></td>
            <td class="dt-right"><?= number_format($grandIncome_Commission, 2) ?></td>
            <td class="dt-right"><?= number_format($grandIncome_Net, 2) ?></td>
            <td>INCOME</td>
        </tr>
        <tr class="grand-total">
            <td>Grand Total Withdrawals</td>
            <td></td>
            <td class="dt-right"><?= number_format($grandWithdrawal_Amount, 2) ?></td>
            <td class="dt-right">-</td>
            <td class="dt-right"><?= number_format($grandWithdrawal_Net, 2) ?></td>
            <td>WITHDRAWAL</td>
        </tr>
    </tfoot>
</table>

<script>
$(document).ready(function(){
    $('#txnTable').DataTable({
        ordering: false,
        pageLength: 100000
    });
});
</script>
</body>
</html>

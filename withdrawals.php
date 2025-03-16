<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php-error.log');

session_start();
$title = "Withdrawal Summary";
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

$filename = __DIR__ . '/data_parsed' . date('Y-m-d_H-i-s') . '.txt';
$dataToSave = json_encode($jsonEntries, JSON_PRETTY_PRINT);
file_put_contents($filename, $dataToSave);



//---------------------------------------------------------
// 3) PARSE DATES & FILTER WITHDRAWALS ONLY
//---------------------------------------------------------
function parseDate(?string $dateStr): ?DateTime {
    if (!$dateStr) return null;
    // Try known format (month/day/year, 12-hour time)
    $dt = DateTime::createFromFormat('n/j/Y g:i:s A', $dateStr);
    if (!$dt) {
        // Fallback attempt letting PHP guess
        try {
            $dt = new DateTime($dateStr);
        } catch (\Exception $e) {
            return null;
        }
    }
    return $dt;
}

// We only keep records that are valid date & transaction_type == withdrawal
$validWithdrawals = [];
foreach ($jsonEntries as $entry) {
    $txType = strtolower($entry['transaction_type'] ?? '');
    if ($txType !== 'withdrawal') {
        continue; // ignore incomes or anything else
    }
    $dt = parseDate($entry['date'] ?? '');
    if ($dt) {
        // Store parsed DateTime for sorting & grouping
        $entry['_dt'] = $dt;
        // Make sure amount is positive for withdrawal
        $entry['_abs_amount'] = abs(floatval($entry['amount'] ?? 0));
        $validWithdrawals[] = $entry;
    }
}

// Sort by parsed date ascending
usort($validWithdrawals, function($a, $b) {
    return $a['_dt'] <=> $b['_dt'];
});

//---------------------------------------------------------
// 4) GROUP BY MONTH (YYYY-MM) AND SUM WITHDRAWALS
//---------------------------------------------------------
$monthData = []; // e.g. ['2024-01' => 123.45, '2024-02' => 67.89, ...]

foreach ($validWithdrawals as $entry) {
    /** @var DateTime $dt */
    $dt = $entry['_dt'];
    $yearMonth = $dt->format('Y-m'); // e.g. "2024-01"

    if (!isset($monthData[$yearMonth])) {
        $monthData[$yearMonth] = 0.0;
    }
    // Sum the absolute amount
    $monthData[$yearMonth] += $entry['_abs_amount'];
}

// Calculate grand total
$grandTotal = array_sum($monthData);

//---------------------------------------------------------
// 5) OUTPUT AS HTML
//---------------------------------------------------------
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
        .dt-right { text-align: right; }
        .grand-total { background-color: #cfc; font-weight: bold; }
    </style>
</head>
<body>

<h2>Monthly Withdrawal Summary</h2>
<table id="withdrawalsTable" class="display nowrap" style="width:100%">
    <thead>
        <tr>
            <th>Month</th>
            <th class="dt-right">Total Withdrawn</th>
        </tr>
    </thead>
    <tbody>
    <?php
    // Sort the months in ascending order "YYYY-MM"
    $sortedMonths = array_keys($monthData);
    sort($sortedMonths);

    foreach ($sortedMonths as $ym) {
        // Convert "YYYY-MM" into a readable "F Y"
        $monthLabelObj = DateTime::createFromFormat('Y-m-d', $ym . '-01');
        $monthLabel = $monthLabelObj ? $monthLabelObj->format('F Y') : $ym;

        // Monthly total
        $monthlyTotal = $monthData[$ym];
        ?>
        <tr>
            <td><?= htmlspecialchars($monthLabel) ?></td>
            <td class="dt-right"><?= number_format($monthlyTotal, 2) ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
    <tfoot>
        <tr class="grand-total">
            <td>Absolute Total Withdrawn</td>
            <td class="dt-right"><?= number_format($grandTotal, 2) ?></td>
        </tr>
    </tfoot>
</table>

<script>
$(document).ready(function(){
    $('#withdrawalsTable').DataTable({
        ordering: false,
        pageLength: 100000
    });
});
</script>
</body>
</html>

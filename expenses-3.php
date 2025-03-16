<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php-error.log');

session_start();
$title = "Expenses";
// require 'resources/includes/head.php'; 
require_once 'database/dbconnect.php';
define('ADMIN_BASE_URL', getenv('BASE_URL'));

//----------------------------------------
// FETCH THE LATEST FILE
//----------------------------------------
$latestFile = null;
$stmt = $conn->prepare("SELECT file_name FROM expenses ORDER BY created_at DESC LIMIT 1");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $latestFile = $result->fetch_assoc()['file_name'];
}

//----------------------------------------
// RETRIEVE THE JSON DATA
//----------------------------------------
$jsonEntries = [];
if ($latestFile) {
    $filePath = $latestFile;
    if (file_exists($filePath)) {
        $jsonContent = file_get_contents($filePath);
        $jsonEntries = json_decode($jsonContent, true) ?: [];
    }
}

//----------------------------------------
// GROUP THE DATA
//----------------------------------------
$groupedData = [];
$undefinedRows = [];

function validateDate($date, $format = 'd/m/Y')
{
    $dt = DateTime::createFromFormat($format, $date);
    if ($dt && $dt->format($format) === $date) {
        return true;
    }
    if ($format === 'd/m/Y') {
        $dt2 = DateTime::createFromFormat('j/n/Y', $date);
        if ($dt2 && $dt2->format('j/n/Y') === $date) {
            return true;
        }
    }
    if ($format === 'm/d/Y') {
        $dt2 = DateTime::createFromFormat('n/j/Y', $date);
        if ($dt2 && $dt2->format('n/j/Y') === $date) {
            return true;
        }
    }
    return false;
}

function getValidDate($entry) {
    $datePurchased = trim($entry['date_purchased'] ?? '');
    $dateToBePaid  = trim($entry['date_to_be_paid'] ?? '');
    $datePaid      = trim($entry['date_paid'] ?? '');

    if (!validateDate($datePurchased, 'd/m/Y')) {
        if (validateDate($datePurchased, 'm/d/Y')) {
            $dt = DateTime::createFromFormat('m/d/Y', $datePurchased);
            return $dt->format('F Y');
        } else {
            if (validateDate($dateToBePaid, 'd/m/Y')) {
                $dt = DateTime::createFromFormat('d/m/Y', $dateToBePaid);
                return $dt->format('F Y');
            } else if (validateDate($datePaid, 'd/m/Y')) {
                $dt = DateTime::createFromFormat('d/m/Y', $datePaid);
                return $dt->format('F Y');
            } else {
                return false;
            }
        }
    } else {
        $dt = DateTime::createFromFormat('d/m/Y', $datePurchased);
        if (!$dt) {
            $dt = DateTime::createFromFormat('j/n/Y', $datePurchased);
        }
        return $dt->format('F Y');
    }
}

function unifySupplierName($rawName) {
    $name = strtoupper($rawName);
    $name = preg_replace("/[^A-Z0-9\s]/", " ", $name);
    $name = preg_replace('/\s+/', ' ', $name);
    $name = trim($name);

    $dictionary = [
        "FARMERS CHOICE" => [
            "FARMETRS CHOICE LTD",
            "FARMERS CHOICE",       
            "FARMERSCHOICE",
            "FARMERS CHOICE LTD", 
            "FARMERS CHOICE LIMITED",
        ],
        "NAIVAS" => [
            "NAIVAS LTD", 
            "NAIVAS SUPERMARKET 1",
            "NAIVAS SUPERMARKET",
            "NAIVAS LIMITED",
            "NAIVAS SUPERMARKET LTD",
            "NAIVAS SUPERMAKERT",
            "NAIVAS SUPERAMRKET"
        ],
        "QUICKMART" => [
            "QUICKMART", 
            "QUICKMART KIAMBU ROAD",
            "QUICKMART SUPERMAKET",
            "QUICKMART SUPERMAKET",
            "QUICKMART SUPRMAKERT",
            "QUICKMART THOME BRANCH",
            "QUICKMART SUPERMALRKET",
            "QUCIKMART SUPERMARKET",
            "QUICKMART SUPERMARKET"
        ],
        "MBURU DISTRIBUTORS" => [
            "MBURU SODA DISTRIBUTORS",
            "MBURU DISTRIBUTORS",
            "PETER MBURU SODA DISTRIBUTORS",
            "PETER MBURU",
            "MBURU SODA"
        ],
        "NOVEL GREEN STORES" => [
            "NOVE L GREEN STORES",
            "NOVEL GREEN STORES",
            "NOVEL GREEN STORES LIMITED",
            "NOVEL GREEN STORE LTD",
            "NOVEL GREEN STOTRES",
            "NOVEL GREEN STORES LTD"
        ],
        "WILKEM ENTERPRISES" => [
            "WILKEM ENTERPRISES", 
            "WILKEM ENTERPRISES LTD",
            "WILKEM ENTERPROSES LTD"
        ],
        "ACUITY" => [
            "ACUITY", 
            "ACUITY VENTURES LTD",
            "ACUITY VENTURES LIMITED",
        ],
        "MASAFA QUENCH" => [
            "MASAFA QUENCH", 
            "MASAFA QYUCH",
            "MASAFA QOUENCH"
        ],
        "ADAMJI" => [
            "ADAMJI", 
            "ADAMI MULTI SUPPLIERS LTD",
            "ADAMJI MULTI SUPPLIES LTD",
            "ADAMJI MULTI SUPPLIERS LTD",
        ],
        "JWINES" => [
            "JWINES", 
            "JWINES KASARANI ENETRPRISES LTD",
        ],
        "NEMCHEM INTERNATIONAL" => [
            "NEMCHE INTERNATIONAL", 
            "NEMCHEM INTERNATIONAL LTD",
            "NEMCHEM INTERNATIONAL K LTD",
            "NEMCHEM KENYAM INTERNATIONAL",
            "NEMCHEM INTERNATIOL K LTD",
            "NEMCHEM",
            "NEMCHEM IN TERNATIONAL KENYA LTD",
        ],
        "MITANNA GASES" => [
            "MITANNA GASES", 
            "MITANNA GASES LTD",
            "MITANNA GASES LIMITED",
            "MITANNA GAS"
        ],
        "QUESED" => [
            "QUESED GLOBAL INVESTMENTS", 
            "QUESED",
        ],
        "MAHITAJI ENTERPRISES LTD" => [
            "MAHITAJI ENTERPRISES LTD", 
            "MAHITAJU ENTERPRISES LTD",
        ],
        "WOODVALE LIQOUR STORE" => [
            "WOODVALE LIQOUR STORE", 
            "WOODVALE LIQOUR HOUSE",
        ],
        "TEXFARM" => [
            "TEXFARM",
            "TEXFARM SUPPLIES LTD",
            "TEX-FARM SUPPLIES LTD",
            "TEX-FARM BUTCHERY",
        ],
        "HEAVES INTERNATIONAL" => [
            "HEAVES INTERNATIONAL",
            "HEAVES INTERNATIONAL LIMITED"
        ],
        "PRIME CUTS" => [
            "PRIME CUTS",
            "STARZ PRIME CUTS",
            "STARZ PRIME CYTS"
        ],
        "HUGESHARE" => [
            "HUGESHARE",
            "HUGESHARE LIOMITED",
            "HUGESHARE LIMITED",
            "HUGESHARE LTD"
        ],
        "TOP IT UP DISTRIBUTOR LTD" => [
            "TOP IT UP DISTRIBUTOR LTD",
            "TOP IT UP DISTRIBUTOR TD",
            "TOP IT UP DISTRIBUTOR KLTD"
        ]
    ];

    foreach ($dictionary as $masterName => $synonyms) {
        if (in_array($name, $synonyms, true)) {
            return $masterName;
        }
    }
    return $name;
}

foreach ($jsonEntries as $entry) {
    $supplier = unifySupplierName($entry['supplier_name'] ?? '');
    $datePurchased = trim($entry['date_purchased'] ?? '');
    $amount = (float) ($entry['amount'] ?? 0);

    if (empty($supplier) || empty($datePurchased)) {
        $undefinedRows[] = $entry;
        continue;
    }

    $monthYear = getValidDate($entry);
    if (!$monthYear) {
        $undefinedRows[] = $entry;
        continue;
    }

    if (!isset($groupedData[$monthYear])) $groupedData[$monthYear] = [];
    if (!isset($groupedData[$monthYear][$supplier])) $groupedData[$monthYear][$supplier] = [];
    $groupedData[$monthYear][$supplier][] = $entry;
}

//-------------------------------------------------------------------
// Build a supplier summary: overall and monthly totals for each supplier
//-------------------------------------------------------------------
$supplierSummary = [];
foreach ($groupedData as $monthYear => $suppliers) {
    foreach ($suppliers as $supplierName => $entries) {
        if (!isset($supplierSummary[$supplierName])) {
            $supplierSummary[$supplierName] = ['total' => 0, 'monthly' => []];
        }
        $monthTotal = 0;
        foreach ($entries as $entry) {
            $amount = (float)($entry['amount'] ?? 0);
            $monthTotal += $amount;
        }
        $supplierSummary[$supplierName]['total'] += $monthTotal;
        $supplierSummary[$supplierName]['monthly'][$monthYear] = $monthTotal;
    }
}

//-------------------------------------------------------------------
// Check if a supplier filter is set via GET parameter (e.g., ?supplier=NAIVAS)
//-------------------------------------------------------------------
$supplierFilter = '';
if (isset($_GET['supplier']) && !empty($_GET['supplier'])) {
    $supplierFilter = unifySupplierName($_GET['supplier']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        tr.subtotal-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        tr.total-row {
            background-color: #d0d0d0;
            font-weight: bold;
        }
        tr.undefined-row {
            background-color: #f8d7da;
            font-weight: bold;
        }
        tr.absolute-total-row {
            background-color: #c3e6cb;
            font-weight: bold;
        }
        .dt-right {
            text-align: right;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Expenses</h2>

    <!-- Supplier-Specific Detailed Analysis (if a supplier is filtered) -->
    <?php if ($supplierFilter): ?>
        <h3>Detailed Analysis for Supplier: <?php echo $supplierFilter; ?></h3>
        <table id="supplierDetailTable" class="display nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>Month-Year</th>
                    <th>Item Purchased</th>
                    <th>Invoice #</th>
                    <th>Amount</th>
                    <th>Date Purchased</th>
                    <th>Date To Be Paid</th>
                    <th>Date Paid</th>
                    <th>Paid</th>
                    <th>Long Overdue</th>
                </tr>
            </thead>
           <tbody>
    <?php
    $supplierOverallTotal = 0;
    foreach ($groupedData as $monthYear => $suppliers) {
        if (isset($suppliers[$supplierFilter])) {
            $entries = $suppliers[$supplierFilter];
            $monthTotal = 0;
            foreach ($entries as $entry) {
                $amount = (float)($entry['amount'] ?? 0);
                $monthTotal += $amount;
                echo "<tr>";
                echo "<td>{$monthYear}</td>";
                echo "<td>" . ($entry['item_purchased'] ?? '') . "</td>";
                echo "<td>" . ($entry['invoice_number'] ?? '') . "</td>";
                echo "<td class='dt-right'>" . number_format($amount, 2) . "</td>";
                echo "<td>" . ($entry['date_purchased'] ?? '') . "</td>";
                echo "<td>" . ($entry['date_to_be_paid'] ?? '') . "</td>";
                echo "<td>" . ($entry['date_paid'] ?? '') . "</td>";
                echo "<td>" . ($entry['paid'] ?? '') . "</td>";
                echo "<td>" . ($entry['long_overdue'] ?? '') . "</td>";
                echo "</tr>";
            }
            // Build the subtotal row without using colspan
            echo "<tr class='subtotal-row'>";
            // Instead of colspan='3', we output 3 cells: first cell has the label, the next 2 are empty.
            echo "<td class='dt-right'>Subtotal for {$monthYear}:</td>";
            echo "<td></td>";
            echo "<td></td>";
            // The fourth cell displays the subtotal amount.
            echo "<td class='dt-right'>" . number_format($monthTotal, 2) . "</td>";
            // Output 5 more empty cells (to complete the 9-column row)
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "</tr>";
            $supplierOverallTotal += $monthTotal;
        }
    }
    // Build the overall total row without using colspan
    echo "<tr class='absolute-total-row'>";
    echo "<td class='dt-right'>Overall Total for {$supplierFilter}:</td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "<td class='dt-right'>" . number_format($supplierOverallTotal, 2) . "</td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "</tr>";
    ?>
</tbody>

        </table>
    <?php endif; ?>

    <!-- Supplier Totals Summary -->
    <h3>Supplier Totals Summary</h3>
    <table id="supplierSummaryTable" class="display nowrap" style="width:100%">
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($supplierSummary as $supplierName => $data): ?>
                <tr>
                    <td>
                        <!-- Clicking the supplier name filters the detailed view -->
                        <a href="?supplier=<?php echo urlencode($supplierName); ?>"><?php echo $supplierName; ?></a>
                    </td>
                    <td class="dt-right"><?php echo number_format($data['total'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Original Expenses Table (Grouped by Month & Supplier) -->
    <table id="expensesTable" class="display nowrap" style="width:100%">
        <thead>
            <tr>
                <th>Month-Year</th>
                <th>Supplier</th>
                <th>Item Purchased</th>
                <th>Invoice #</th>
                <th>Amount</th>
                <th>Date Purchased</th>
                <th>Date To Be Paid</th>
                <th>Date Paid</th>
                <th>Paid</th>
                <th>Long Overdue</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $absoluteTotal = 0;
            foreach ($groupedData as $monthYear => $suppliers) {
                $monthTotal = 0;
                foreach ($suppliers as $supplierName => $rows) {
                    $supplierSubtotal = 0;
                    foreach ($rows as $row) {
                        $supplierSubtotal += (float)($row['amount'] ?? 0);
                        ?>
                        <tr>
                            <td><?= $monthYear ?></td>
                            <td><?= $row['supplier_name'] ?? '' ?></td>
                            <td><?= $row['item_purchased'] ?? '' ?></td>
                            <td><?= $row['invoice_number'] ?? '' ?></td>
                            <td class="dt-right"><?= number_format($row['amount'] ?? 0, 2) ?></td>
                            <td><?= $row['date_purchased'] ?? '' ?></td>
                            <td><?= $row['date_to_be_paid'] ?? '' ?></td>
                            <td><?= $row['date_paid'] ?? '' ?></td>
                            <td><?= $row['paid'] ?? '' ?></td>
                            <td><?= $row['long_overdue'] ?? '' ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr class="subtotal-row">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="dt-right">Subtotal for <?= $supplierName ?>:</td>
                        <td class="dt-right"><?= number_format($supplierSubtotal, 2) ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php
                    $monthTotal += $supplierSubtotal;
                }
                ?>
                <tr class="total-row">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="dt-right">Total for <?= $monthYear ?>:</td>
                    <td class="dt-right"><?= number_format($monthTotal, 2) ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php
                $absoluteTotal += $monthTotal;
            }

            $undefinedTotal = 0;
            if (!empty($undefinedRows)) {
                foreach ($undefinedRows as $urow) {
                    $undefinedTotal += (float)($urow['amount'] ?? 0);
                    ?>
                    <tr>
                        <td>Undefined Month/Year</td>
                        <td><?= !empty($urow['supplier_name']) ? $urow['supplier_name'] : "Undefined Supplier" ?></td>
                        <td><?= $urow['item_purchased'] ?? '' ?></td>
                        <td><?= $urow['invoice_number'] ?? '' ?></td>
                        <td class="dt-right"><?= number_format($urow['amount'] ?? 0, 2) ?></td>
                        <td><?= $urow['date_purchased'] ?? '' ?></td>
                        <td><?= $urow['date_to_be_paid'] ?? '' ?></td>
                        <td><?= $urow['date_paid'] ?? '' ?></td>
                        <td><?= $urow['paid'] ?? '' ?></td>
                        <td><?= $urow['long_overdue'] ?? '' ?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr class="undefined-row">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="dt-right">Total for Undefined:</td>
                    <td class="dt-right"><?= number_format($undefinedTotal, 2) ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php
            }
            $absoluteTotal += $undefinedTotal;
            ?>
            <tr class="absolute-total-row">
                <td></td>
                <td></td>
                <td></td>
                <td class="dt-right">Absolute Total (All Months + Undefined):</td>
                <td class="dt-right"><?= number_format($absoluteTotal, 2) ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

</div>

<!-- JavaScript & DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script>
$(document).ready(function() {
    $('#expensesTable').DataTable({
        ordering: false,
        dom: 'Bfrtip',
        buttons: ['excelHtml5', 'pdfHtml5', 'print'],
        pageLength: 10000,
        columnDefs: [{ targets: 4, className: 'dt-right' }]
    });
    $('#supplierSummaryTable').DataTable({
        ordering: false,
        dom: 'Bfrtip',
        buttons: ['excelHtml5', 'pdfHtml5', 'print'],
        pageLength: 10000,
        columnDefs: [{ targets: 1, className: 'dt-right' }]
    });
    $('#supplierDetailTable').DataTable({
        ordering: false,
        dom: 'Bfrtip',
        buttons: ['excelHtml5', 'pdfHtml5', 'print'],
        pageLength: 10000,
        columnDefs: [{ targets: 3, className: 'dt-right' }]
    });
});
</script>
</body>
</html>

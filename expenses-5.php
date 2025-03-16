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
    // Attempt standard parse for the given format
    $dt = DateTime::createFromFormat($format, $date);
    if ($dt && $dt->format($format) === $date) {
        return true;
    }

    // If checking d/m/Y, also try j/n/Y
    if ($format === 'd/m/Y') {
        $dt2 = DateTime::createFromFormat('j/n/Y', $date);
        if ($dt2 && $dt2->format('j/n/Y') === $date) {
            return true;
        }
    }

    // If checking m/d/Y, also try n/j/Y
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

    // 1) Attempt d/m/Y or j/n/Y 
    if (!validateDate($datePurchased, 'd/m/Y')) {
        // 2) Attempt m/d/Y or n/j/Y
        if (validateDate($datePurchased, 'm/d/Y')) {
            $dt = DateTime::createFromFormat('m/d/Y', $datePurchased);
            return $dt->format('F Y');
        } else {
            // 3) If datePurchased was invalid, try dateToBePaid
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
        // If d/m/Y (or j/n/Y) is valid for datePurchased
        $dt = DateTime::createFromFormat('d/m/Y', $datePurchased);
        // If it was actually j/n/Y, you would parse it again similarly
        // but for simplicity, we can do:
        if (!$dt) {
            $dt = DateTime::createFromFormat('j/n/Y', $datePurchased);
        }
        return $dt->format('F Y');
    }
}

function unifySupplierName($rawName) {
    // 1) Convert to uppercase
    $name = strtoupper($rawName);

    // 2) Remove all punctuation except spaces (this handles "NOVE;L" → "NOVE L")
    //    This pattern removes any non-alphanumeric, non-whitespace character:
    $name = preg_replace("/[^A-Z0-9\s]/", " ", $name);

    // 3) Collapse multiple spaces into one, then trim
    $name = preg_replace('/\s+/', ' ', $name);
    $name = trim($name);

    // 4) Dictionary of known variants -> a unified "master" name
    //    Each entry has one "master" (left side),
    //    and an array of synonyms (right side).
    //    We’ll loop over these to see if $name matches any known variant.
    $dictionary = [
        // 1) FARMERS CHOICE
        "FARMERS CHOICE" => [
            "FARMETRS CHOICE LTD",   // Spelling error
            "FARMERS CHOICE",       
            "FARMERSCHOICE",         // Missing space
            "FARMERS CHOICE LTD", 
            "FARMERS CHOICE LIMITED",
        ],
        // 2) NAIVAS
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
            "QUICKMART SUPERMARKET",
            "QUICKMART LTD"
        ],
        // 3) MBURU DISTRIBUTORS
        "MBURU DISTRIBUTORS" => [
            "MBURU SODA DISTRIBUTORS",
            "MBURU DISTRIBUTORS",
            "PETER MBURU SODA DISTRIBUTORS",
            "PETER MBURU",
            "MBURU SODA"
        ],
        // 4) NOVEL GREEN STORES
        "NOVEL GREEN STORES" => [
            "NOVE L GREEN STORES",    // from "NOVE;L" → "NOVE L"
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
        "TEXFARM"=>[
            "TEXFARM",
            "TEXFARM SUPPLIES LTD",
            "TEX-FARM SUPPLIES LTD",
            "TEX-FARM BUTCHERY",
            
        ],
        "HEAVES INTERNATIONAL"=>[
            "HEAVES INTERNATIONAL",
            "HEAVES INTERNATIONAL LIMITED"
        ],
         "PRIME CUTS"=>[
            "PRIME CUTS",
            "STARZ PRIME CUTS",
            "STARZ PRIME CYTS"
        ],
        
         "HUGESHARE"=>[
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

    // Check if $name is in any of these arrays. If so, unify:
    foreach ($dictionary as $masterName => $synonyms) {
        if (in_array($name, $synonyms, true)) {
            return $masterName;  // Return the "master"
        }
    }

    // If not in the dictionary, return the normalized name as-is
    return $name;
}


foreach ($jsonEntries as $entry) {
    // $supplier = trim($entry['supplier_name'] ?? '');
    $supplier = unifySupplierName($entry['supplier_name'] ?? '');
    $datePurchased = trim($entry['date_purchased'] ?? '');
    $amount = (float) ($entry['amount'] ?? 0);

    if (empty($supplier) || empty($datePurchased)) {
        $undefinedRows[] = $entry;
        continue;
    }

    // Get valid date
    $monthYear = getValidDate($entry);
    if (!$monthYear) {
        $undefinedRows[] = $entry;
        continue;
    }

    if (!isset($groupedData[$monthYear])) $groupedData[$monthYear] = [];
    if (!isset($groupedData[$monthYear][$supplier])) $groupedData[$monthYear][$supplier] = [];
    $groupedData[$monthYear][$supplier][] = $entry;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
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
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        #expensesTable {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        #expensesTable th, #expensesTable td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        #expensesTable th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        #expensesTable tr:hover {
            background-color: #f1f1f1;
        }
        #expensesTable tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        #expensesTable tr.subtotal-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        #expensesTable tr.total-row {
            background-color: #d0d0d0;
            font-weight: bold;
        }
        #expensesTable tr.undefined-row {
            background-color: #f8d7da;
            font-weight: bold;
        }
        #expensesTable tr.absolute-total-row {
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

<script>
$(document).ready(function() {
    $('#expensesTable').DataTable({
        ordering: false,
        dom: 'Bfrtip',
        buttons: ['excelHtml5', 'pdfHtml5', 'print'],
        pageLength: 10000,
        columnDefs: [
            { targets: 4, className: 'dt-right' } // Align Amount column to the right
        ]
    });
});
</script>

</body>
</html>
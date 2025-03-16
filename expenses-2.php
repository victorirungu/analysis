<?php
session_start();
$title = "Expenses";
require 'resources/includes/head.php'; 
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
// (Adjust this part to however you're actually getting the JSON content.)
//----------------------------------------
$jsonEntries = []; // This should be replaced by your actual fetched JSON data

// For the sake of example, let's assume you read from a file or from a DB column:
if ($latestFile) {
    $filePath = $latestFile; // adjust as needed
    if (file_exists($filePath)) {
        $jsonContent = file_get_contents($filePath);
        $jsonEntries = json_decode($jsonContent, true); // an array of rows
    }
}

// If $jsonEntries is not yet the entire data, you can merge multiple sets, etc.
// We'll assume $jsonEntries is exactly the array you showed in your example.

//----------------------------------------
// GROUP THE DATA
//----------------------------------------

// We'll group by Month-Year from `date_purchased` and inside that by supplier_name.
// If date or supplier is missing/invalid, we'll push the row to $undefined.
$groupedData = [];      // array('MonthName Year' => array( 'SupplierName' => array( rows... ) ) )
$undefinedRows = [];    // rows that have an empty/invalid date or empty supplier

foreach ($jsonEntries as $entry) {
    $supplier = trim($entry['supplier_name']);
    $datePurchased = trim($entry['date_purchased']);
    $amount = (float) $entry['amount'];

    // Check for empty supplier or date
    if (empty($supplier) || empty($datePurchased)) {
        // Goes to undefined
        $undefinedRows[] = $entry;
        continue;
    }

    // Try parsing date in d/m/Y or m/d/Y format, etc.
    // Your data might be "02/09/2023" (m/d/Y) or "27/1/2024" (d/m/Y).
    // Adjust this to match your actual date format. 
    // Because the example is "02/09/2023" which *looks* like mm/dd/yyyy or maybe dd/mm/yyyy. 
    // Let's assume it's mm/dd/yyyy. If it's actually dd/mm/yyyy, swap the format below.
    
    $dt = DateTime::createFromFormat('m/d/Y', $datePurchased);
    if (!$dt) {
        // Might be day/month/year
        $dt = DateTime::createFromFormat('d/m/Y', $datePurchased);
    }
    if (!$dt) {
        // If still not valid, consider it undefined
        $undefinedRows[] = $entry;
        continue;
    }

    // Now we have a valid DateTime
    $monthYear = $dt->format('F Y'); // e.g. "February 2023"

    // Group by month-year, then by supplier
    if (!isset($groupedData[$monthYear])) {
        $groupedData[$monthYear] = [];
    }
    if (!isset($groupedData[$monthYear][$supplier])) {
        $groupedData[$monthYear][$supplier] = [];
    }
    $groupedData[$monthYear][$supplier][] = $entry;
}

//----------------------------------------
// BUILD THE TABLE
//----------------------------------------
// We'll construct an HTML table with the structure you want.
// We will not do any thead sorting by default so that DataTables doesn't reorder your rows.

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <!-- Include DataTables CSS and JS from a CDN or your local files -->
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

            // Output in the order: month-year => suppliers => rows
            // 1) Loop each month-year
            foreach ($groupedData as $monthYear => $suppliers) {
                $monthTotal = 0;

                // 2) Loop each supplier in that month
                foreach ($suppliers as $supplierName => $rows) {
                    $supplierSubtotal = 0;

                    // 3) Loop each row for that supplier
                    foreach ($rows as $row) {
                        $supplierSubtotal += (float)$row['amount'];

                        // Print normal row
                        echo "<tr>";
                        echo "<td>{$monthYear}</td>";
                        echo "<td>{$row['supplier_name']}</td>";
                        echo "<td>{$row['item_purchased']}</td>";
                        echo "<td>{$row['invoice_number']}</td>";
                        echo "<td style='text-align:right;'>" . number_format($row['amount'], 2) . "</td>";
                        echo "<td>{$row['date_purchased']}</td>";
                        echo "<td>{$row['date_to_be_paid']}</td>";
                        echo "<td>{$row['date_paid']}</td>";
                        echo "<td>{$row['paid']}</td>";
                        echo "<td>{$row['long_overdue']}</td>";
                        echo "</tr>";
                    } // end foreach row

                    // Print a Subtotal row for that supplier
                    echo "<tr style='background-color: #f0f0f0; font-weight: bold;'>";
                    echo "<td colspan='4' style='text-align:right;'>Subtotal for {$supplierName}:</td>";
                    echo "<td style='text-align:right;'>" . number_format($supplierSubtotal, 2) . "</td>";
                    // Empty cells for the rest of the columns
                    echo "<td colspan='5'></td>";
                    echo "</tr>";

                    $monthTotal += $supplierSubtotal;
                } // end foreach supplier

                // After all suppliers in a month, print a TOTAL row for that month
                echo "<tr style='background-color: #d0d0d0; font-weight: bold;'>";
                echo "<td colspan='4' style='text-align:right;'>Total for {$monthYear}:</td>";
                echo "<td style='text-align:right;'>" . number_format($monthTotal, 2) . "</td>";
                echo "<td colspan='5'></td>";
                echo "</tr>";

                $absoluteTotal += $monthTotal;
            } // end foreach month-year

            // Now handle all the "undefined" rows
            // (where date or supplier was missing/invalid)
            $undefinedTotal = 0;
            if (count($undefinedRows) > 0) {
                foreach ($undefinedRows as $urow) {
                    $undefinedTotal += (float)$urow['amount'];
                    echo "<tr>";
                    echo "<td>Undefined Month/Year</td>";
                    echo "<td>" . (!empty($urow['supplier_name']) ? $urow['supplier_name'] : "Undefined Supplier") . "</td>";
                    echo "<td>{$urow['item_purchased']}</td>";
                    echo "<td>{$urow['invoice_number']}</td>";
                    echo "<td style='text-align:right;'>" . number_format($urow['amount'], 2) . "</td>";
                    echo "<td>{$urow['date_purchased']}</td>";
                    echo "<td>{$urow['date_to_be_paid']}</td>";
                    echo "<td>{$urow['date_paid']}</td>";
                    echo "<td>{$urow['paid']}</td>";
                    echo "<td>{$urow['long_overdue']}</td>";
                    echo "</tr>";
                }

                // Subtotal for undefined
                echo "<tr style='background-color: #f8d7da; font-weight: bold;'>";
                echo "<td colspan='4' style='text-align:right;'>Total for Undefined:</td>";
                echo "<td style='text-align:right;'>" . number_format($undefinedTotal, 2) . "</td>";
                echo "<td colspan='5'></td>";
                echo "</tr>";
            }

            // Finally, Absolute total row
            $absoluteTotal += $undefinedTotal;
            echo "<tr style='background-color: #c3e6cb; font-weight: bold;'>";
            echo "<td colspan='4' style='text-align:right;'>Absolute Total (All Months + Undefined):</td>";
            echo "<td style='text-align:right;'>" . number_format($absoluteTotal, 2) . "</td>";
            echo "<td colspan='5'></td>";
            echo "</tr>";
        ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable with no ordering (to preserve the grouping order),
    // and with the buttons for export (Excel, PDF, etc.)
    $('#expensesTable').DataTable({
        ordering: false,   // turn off ordering
        // or "order": [], 
        // either way ensures no sorting is applied automatically
        dom: 'Bfrtip',
        buttons: [
            'excelHtml5',
            'pdfHtml5',
            'print'
        ],
        pageLength: 10000 // or whatever you want
    });
});
</script>

</body>
</html>

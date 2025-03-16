<?php 
session_start();
$title = "Expenses";
require 'resources/includes/head.php'; 
require_once 'database/dbconnect.php';
define('ADMIN_BASE_URL', getenv('BASE_URL'));

$latestFile = null;
$stmt = $conn->prepare("SELECT file_name FROM expenses ORDER BY created_at DESC LIMIT 1");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $latestFile = $result->fetch_assoc()['file_name'];
}

// Initialize analysis arrays
$analysis = [
    'monthly_total'             => [],
    'supplier_monthly'          => [],
    'supplier_purchase_monthly' => [],
    'product_monthly'           => []
];

/**
 * Enhanced date parsing with multiple format support and typo handling
 */
function parsePurchaseDate($dateString) {
    // Clean the date string
    $dateString = str_replace(['.', '-'], '/', trim($dateString));
    
    // Handle different date formats
    $formats = [
        'd/m/Y', 'm/d/Y', 
        'd/m/y', 'm/d/y',
        'Y/m/d', 'y/m/d'
    ];
    
    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $dateString);
        if ($date && $date->format($format) === $dateString) {
            // Validate logical date (e.g., not 31/02)
            $errors = DateTime::getLastErrors();
            if ($errors['warning_count'] === 0) {
                return $date;
            }
        }
    }
    
    // Try extracting from malformed dates (e.g., 17/2.2023)
    if (preg_match('/^(\d{1,2})[\/\.](\d{1,2})[\/\.](\d{4})$/', $dateString, $matches)) {
        return DateTime::createFromFormat('d/m/Y', "{$matches[1]}/{$matches[2]}/{$matches[3]}");
    }
    
    return null;
}

/**
 * Improved supplier normalization
 */
function normalizeSupplier($name) {
    $name = preg_replace('/\b(LTD|LIMITED|SUPERMARKET|INC|CO|CORP|ENTERPRISES|VENTURES|COMPANY)\b/i', '', $name);
    $name = preg_replace('/[^a-zA-Z0-9\s]/', '', $name); // Remove special characters
    return trim(strtoupper($name));
}

if ($latestFile && file_exists($latestFile)) {
    $jsonData = json_decode(file_get_contents($latestFile), true);
    
    $supplierMap = [];
    $monthTracker = [];

    foreach ($jsonData as $entry) {
        if (!isset($entry['amount']) || $entry['amount'] <= 0) continue;

        // Date parsing with fallback
        $date = parsePurchaseDate($entry['date_purchased']);
        $monthYear = $date ? $date->format('Y-m') : 'Invalid Date';
        
        // Track original dates for debugging
        $monthTracker[$monthYear][] = $entry['date_purchased'];

        // Supplier normalization
        $originalSupplier = $entry['supplier_name'] ?? 'Unknown Supplier';
        $normalizedSupplier = normalizeSupplier($originalSupplier);

        // Update supplier map
        if (!isset($supplierMap[$normalizedSupplier])) {
            $supplierMap[$normalizedSupplier] = [
                'display_name' => $originalSupplier,
                'variations'   => [$originalSupplier]
            ];
        } elseif (!in_array($originalSupplier, $supplierMap[$normalizedSupplier]['variations'])) {
            $supplierMap[$normalizedSupplier]['variations'][] = $originalSupplier;
        }

        // Update analysis data
        $analysis['monthly_total'][$monthYear] = ($analysis['monthly_total'][$monthYear] ?? 0) + $entry['amount'];
        
        // Supplier monthly
        $analysis['supplier_monthly'][$normalizedSupplier][$monthYear] = 
            ($analysis['supplier_monthly'][$normalizedSupplier][$monthYear] ?? 0) + $entry['amount'];
        
        // Supplier purchase monthly
        if (!empty($entry['item_purchased'])) {
            $key = $normalizedSupplier . '|' . $monthYear;
            $analysis['supplier_purchase_monthly'][$key][] = [
                'item'   => $entry['item_purchased'],
                'amount' => $entry['amount']
            ];
        }
        
        // Product monthly
        if (!empty($entry['item_purchased'])) {
            $product = trim(strtoupper($entry['item_purchased']));
            $analysis['product_monthly'][$product][$monthYear] = 
                ($analysis['product_monthly'][$product][$monthYear] ?? 0) + $entry['amount'];
        }
    }

    // Sort monthly totals chronologically
    uksort($analysis['monthly_total'], function($a, $b) {
        if ($a === 'Invalid Date') return 1;
        if ($b === 'Invalid Date') return -1;
        return strcmp($a, $b);
    });
}
?>

<!-- HTML/Display Section (remainder of your existing HTML) -->
<div class="page-body-wrapper">
    <style>
        .editStyle {
            margin: 10px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    
    <!-- Page Sidebar Start-->
    <?php include 'resources/includes/sidebar.php'; ?>
    <!-- Page Sidebar Ends-->

    <!-- Container-fluid starts-->
    <div class="page-body">
        <div class="container-fluid">
            <!-- Analysis Section -->
            <div class="row">
                <div class="col-sm-12">
                    <!-- 1. Monthly Total Expenditure -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Monthly Total Expenditure</h5>
                            <table class="table analysis-table">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($analysis['monthly_total'] as $month => $total): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            $dateObj = DateTime::createFromFormat('Y-m', $month);
                                            // If $dateObj is valid, print "F Y"; else "Unknown"
                                            echo $dateObj ? $dateObj->format('F Y') : 'Unknown';
                                            ?>
                                        </td>
                                        <td><?= number_format($total, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="analysis-summary">
                                <strong>Total Annual Expenditure:</strong> 
                                <?= number_format(array_sum($analysis['monthly_total']), 2) ?>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Supplier Monthly Expenditure -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title">Supplier Monthly Expenditure</h5>
                            <div class="alert alert-info">
                                Note: Supplier names have been grouped based on similarity. Please verify groupings.
                            </div>
                            <table class="table analysis-table">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th>Month</th>
                                        <th>Total Amount</th>
                                        <th>Name Variations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($analysis['supplier_monthly'] as $supplier => $months): ?>
                                    <?php foreach ($months as $month => $total): ?>
                                        <tr>
                                            <td><?= $supplierMap[$supplier]['display_name'] ?></td>
                                            <td>
                                                <?php
                                                $dateObj = DateTime::createFromFormat('Y-m', $month);
                                                echo $dateObj ? $dateObj->format('F Y') : 'Unknown';
                                                ?>
                                            </td>
                                            <td><?= number_format($total, 2) ?></td>
                                            <td><?= implode(', ', $supplierMap[$supplier]['variations']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 3. Supplier Purchase Analysis -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title">Itemized Supplier Purchases</h5>
                            <table class="table analysis-table">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th>Month</th>
                                        <th>Item</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($analysis['supplier_purchase_monthly'] as $key => $items): ?>
                                    <?php 
                                        list($supplier, $month) = explode('|', $key);
                                        $dateObj = DateTime::createFromFormat('Y-m', $month);
                                        $monthFormatted = $dateObj ? $dateObj->format('F Y') : 'Unknown';
                                    ?>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?= $supplierMap[$supplier]['display_name'] ?></td>
                                            <td><?= $monthFormatted ?></td>
                                            <td><?= $item['item'] ?></td>
                                            <td><?= number_format($item['amount'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 4. Product Monthly Analysis -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title">Product Expenditure Analysis</h5>
                            <table class="table analysis-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Month</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($analysis['product_monthly'] as $product => $months): ?>
                                    <?php foreach ($months as $month => $total): ?>
                                        <tr>
                                            <td><?= ucwords(strtolower($product)) ?></td>
                                            <td>
                                                <?php
                                                $dateObj = DateTime::createFromFormat('Y-m', $month);
                                                echo $dateObj ? $dateObj->format('F Y') : 'Unknown';
                                                ?>
                                            </td>
                                            <td><?= number_format($total, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        <?php include 'resources/includes/footer.php'; ?>
    </div>
</div>

<!-- Add DataTables and Analysis Script -->
<script>
$(document).ready(function() {
    $('.analysis-table').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        pageLength: 10,
        order: [[1, 'desc']],
        responsive: true
    });
});
</script>

<!-- Modal Start -->
<?php include 'resources/includes/logout.php'; ?>
<!-- Modal End -->

<!-- Additional JS and your scripts here -->
<script src="assets/js/dropzone/dropzone.js"></script>
<script src="assets/js/dropzone/dropzone-script.js"></script>
<script src="assets/js/bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/icons/feather-icon/feather.min.js"></script>
<script src="assets/js/icons/feather-icon/feather-icon.js"></script>
<script src="assets/js/scrollbar/simplebar.js"></script>
<script src="assets/js/scrollbar/custom.js"></script>
<script src="assets/js/config.js"></script>
<script src="assets/js/tooltip-init.js"></script>
<script src="assets/js/sidebar-menu.js"></script>
<script src="assets/js/bundle.min.js"></script>
<script src="assets/js/notify/bootstrap-notify.min.js"></script>
<script src="operations/operations.js"></script>
<script src="assets/js/chart/apex-chart/apex-chart1.js"></script>
<script src="assets/js/chart/apex-chart/moment.min.js"></script>
<script src="assets/js/chart/apex-chart/apex-chart.js"></script>
<script src="assets/js/chart/apex-chart/stock-prices.js"></script>
<script src="assets/js/chart/apex-chart/chart-custom1.js"></script>
<script src="assets/js/slick.min.js"></script>
<script src="assets/js/custom-slick.js"></script>
<script src="assets/js/customizer.js"></script>
<script src="assets/js/ratio.js"></script>
<script src="assets/js/sidebareffect.js"></script>
<script src="assets/js/script.js"></script>
<script src="assets/js/jquery.dataTables.js"></script>
<script src="assets/js/custom-data-table.js"></script>
</body>
</html>

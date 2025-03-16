<?php
/*
 * process_excel.php
 *
 * Expects an Excel file upload with name="excelFile".
 * Parses data from each sheet (including multiple sheets).
 * The columns are:
 * SUPPLIER NAME
 * ITEM PURCHASED (may be missing entirely)
 * INVOICE NUMBER
 * AMOUNT (may contain commas)
 * DATE PURCHASED (DD/MM/YYYY) or empty
 * DATE TO BE PAID (DD/MM/YYYY) or empty
 * DATE PAID (DD/MM/YYYY) or "PAID" or "NOT YET" or empty
 * PAID ("PAID" or "NOT YET")
 * LONG OVERDUE ("PAID" or "NOT YET" or empty)
 *
 * Outputs a JSON response:
 *   { success: bool, totalRecords: int, filePath: string, error?: string }
 *
 * Also writes the extracted data to a JSON file on the server, combining all sheets.
 */
header('Content-Type: application/json; charset=utf-8');

// Ensure PhpSpreadsheet is installed (via Composer).
require_once __DIR__ . '/vendor/autoload.php';
require_once 'database/dbconnect.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method. Use POST.'
    ]);
    exit;
}

if (!isset($_FILES['excelFile']) || !is_uploaded_file($_FILES['excelFile']['tmp_name'])) {
    echo json_encode([
        'success' => false,
        'error' => 'No file uploaded.'
    ]);
    exit;
}

$tempPath     = $_FILES['excelFile']['tmp_name'];
$originalName = $_FILES['excelFile']['name'];

// We will store all extracted rows from all sheets in this array.
$allExtractedData = [];

try {
    // Load the uploaded file into PhpSpreadsheet
    $spreadsheet = IOFactory::load($tempPath);

    // We can iterate over each worksheet in the spreadsheet
    foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
        // Let's get the highest row and column
        $highestRow = $worksheet->getHighestDataRow();
        $highestColumn = $worksheet->getHighestDataColumn();

        // Convert to numeric index for iteration
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        // We expect the first row to contain headers.
        $headers = [];
        $headerRow = 1; // We'll assume row 1 has headers

        // Read header columns (converting them to uppercase).
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            // Convert numeric column index to letter
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $cellValue = trim((string) $worksheet->getCell($colLetter . $headerRow)->getValue());
            $headers[$col] = strtoupper($cellValue);
        }

        // Define which headers we need. We'll map them to known keys.
        $needed = [
            'SUPPLIER NAME'  => 'supplier_name',
            'ITEM PURCHASED' => 'item_purchased',
            'INVOICE NUMBER' => 'invoice_number',
            'AMOUNT'         => 'amount',
            'DATE PURCHASED' => 'date_purchased',
            'DATE TO BE PAID'=> 'date_to_be_paid',
            'DATE PAID'      => 'date_paid',
            'PAID'           => 'paid',
            'LONG OVERDUE'   => 'long_overdue'
        ];

        // We'll find their respective column indexes or note null if not found.
        $colIndexes = [];
        foreach ($needed as $expectedHeader => $mappedKey) {
            $foundColumn = array_search($expectedHeader, $headers, true);
            if ($foundColumn !== false) {
                $colIndexes[$mappedKey] = $foundColumn; 
            } else {
                // This header might be absent.
                $colIndexes[$mappedKey] = null;
            }
        }

        // Now we iterate from row 2 onwards to read data.
        for ($row = 2; $row <= $highestRow; $row++) {
            // Check if the row is essentially empty.
            $rowIsEmpty = true;
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $colLetter = Coordinate::stringFromColumnIndex($col);
                $cellValue = $worksheet->getCell($colLetter . $row)->getValue();
                if (!empty($cellValue) && trim($cellValue) !== '') {
                    $rowIsEmpty = false;
                    break;
                }
            }
            if ($rowIsEmpty) {
                // skip this row
                continue;
            }

            // Let's parse each field we care about.
            $rowData = [];

            // Optionally track which sheet this row came from
            $rowData['sheet_name'] = $worksheet->getTitle();

            // supplier_name
            if ($colIndexes['supplier_name']) {
                $letter = Coordinate::stringFromColumnIndex($colIndexes['supplier_name']);
                $supplierName = $worksheet->getCell($letter . $row)->getValue();
                $rowData['supplier_name'] = trim((string) $supplierName);
            } else {
                // if not found, empty
                $rowData['supplier_name'] = '';
            }

            // item_purchased (may be missing in the Excel entirely)
            if ($colIndexes['item_purchased']) {
                $letter = Coordinate::stringFromColumnIndex($colIndexes['item_purchased']);
                $itemPurchased = $worksheet->getCell($letter . $row)->getValue();
                $rowData['item_purchased'] = trim((string) $itemPurchased);
            } else {
                $rowData['item_purchased'] = '';
            }

            // invoice_number
            if ($colIndexes['invoice_number']) {
                $letter = Coordinate::stringFromColumnIndex($colIndexes['invoice_number']);
                $invoiceNumber = $worksheet->getCell($letter . $row)->getValue();
                $rowData['invoice_number'] = trim((string) $invoiceNumber);
            } else {
                $rowData['invoice_number'] = '';
            }

            // amount (convert commas to handle as float)
            $floatAmount = 0.0;
            if ($colIndexes['amount']) {
                $letter = Coordinate::stringFromColumnIndex($colIndexes['amount']);
                $amountRaw = (string) $worksheet->getCell($letter . $row)->getValue();
                $amountRaw = str_replace(',', '', $amountRaw); // remove commas
                if (is_numeric($amountRaw)) {
                    $floatAmount = (float) $amountRaw;
                }
            }
            $rowData['amount'] = $floatAmount;

            // date_purchased
            $datePurchased = '';
            if ($colIndexes['date_purchased']) {
                $letter = Coordinate::stringFromColumnIndex($colIndexes['date_purchased']);
                $cellVal = $worksheet->getCell($letter . $row)->getValue();
                if (is_numeric($cellVal)) {
                    // Typically in Excel, a numeric date is days since 1900.
                    $dateTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellVal);
                    $datePurchased = $dateTime->format('d/m/Y');
                } else {
                    $datePurchased = trim((string) $cellVal);
                }
            }
            $rowData['date_purchased'] = $datePurchased;

            // date_to_be_paid
            $dateToBePaid = '';
            if ($colIndexes['date_to_be_paid']) {
                $letter = Coordinate::stringFromColumnIndex($colIndexes['date_to_be_paid']);
                $cellVal = $worksheet->getCell($letter . $row)->getValue();
                if (is_numeric($cellVal)) {
                    $dateTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellVal);
                    $dateToBePaid = $dateTime->format('d/m/Y');
                } else {
                    $dateToBePaid = trim((string) $cellVal);
                }
            }
            $rowData['date_to_be_paid'] = $dateToBePaid;

            // date_paid
            $datePaid = '';
            if ($colIndexes['date_paid']) {
                $letter = Coordinate::stringFromColumnIndex($colIndexes['date_paid']);
                $cellVal = $worksheet->getCell($letter . $row)->getValue();
                if (is_numeric($cellVal)) {
                    $dateTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellVal);
                    $datePaid = $dateTime->format('d/m/Y');
                } else {
                    $datePaid = trim((string) $cellVal);
                }
            }
            $rowData['date_paid'] = $datePaid;

            // paid (PAID or NOT YET)
            $paidVal = '';
            if ($colIndexes['paid']) {
                $letter = Coordinate::stringFromColumnIndex($colIndexes['paid']);
                $cellVal = $worksheet->getCell($letter . $row)->getValue();
                $paidVal = trim((string) $cellVal);
            }
            $rowData['paid'] = $paidVal;

            // long_overdue (PAID or NOT YET, may be empty)
            $longOverdue = '';
            if ($colIndexes['long_overdue']) {
                $letter = Coordinate::stringFromColumnIndex($colIndexes['long_overdue']);
                $cellVal = $worksheet->getCell($letter . $row)->getValue();
                $longOverdue = trim((string) $cellVal);
            }
            $rowData['long_overdue'] = $longOverdue;

            // Add this row's data to the global array
            $allExtractedData[] = $rowData;
        }
    }

    // Count total records across ALL sheets
    $totalRecords = count($allExtractedData);

    // Define a unique name for the JSON file.
    $outputFilename = 'exported_data_' . time() . '.json';
    $outputDir = __DIR__ . '/uploads';
    $outputPath = $outputDir . '/' . $outputFilename;

    // Ensure the 'uploads' folder exists
    if (!file_exists($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    // Save the data as a JSON-encoded array.
    file_put_contents(
        $outputPath,
        json_encode($allExtractedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
$stmt = $conn->prepare("INSERT INTO expenses (file_name) VALUES (?)");
$stmt->bind_param("s", $outputPath);
if (!$stmt->execute()) {
    throw new Exception("Failed to save file record: " . $stmt->error);
}

// Return success JSON
echo json_encode([
    'success'      => true,
    'totalRecords' => $totalRecords,
    'filePath'     => 'uploads/' . $outputFilename
]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
    exit;
}

<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/vendor/autoload.php';
require_once 'database/dbconnect.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method. Use POST.']);
    exit;
}

if (!isset($_FILES['excelFile']) || !is_uploaded_file($_FILES['excelFile']['tmp_name'])) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded.']);
    exit;
}

$tempPath     = $_FILES['excelFile']['tmp_name'];
$originalName = $_FILES['excelFile']['name'];

$allExtractedData = [];

/**
 * Parse an amount string (which might be:
 * - numeric negative (Excel internal) => "-1234.56"
 * - parentheses => "(1,234.56)"
 * - normal positive => "1234.56"
 * - with or without commas
 */
function parseAmountAndType(string $rawValue): array
{
    $rawValue = trim($rawValue);

    // Check if it's actually numeric (e.g. -2000.0 from Excel)
    if (is_numeric(str_replace(',', '', $rawValue))) {
        $floatVal = (float) str_replace(',', '', $rawValue);
        if ($floatVal < 0) {
            return [abs($floatVal), 'withdrawal'];
        } else {
            return [$floatVal, 'income'];
        }
    }

    // If not purely numeric, check for parentheses (accounting format as a string).
    $isWithdrawal = false;
    if (preg_match('/^\(.*\)$/', $rawValue)) {
        $isWithdrawal = true;
        $rawValue     = preg_replace('/[()]/', '', $rawValue); // remove parentheses
    }

    // Remove commas if any
    $rawValue = str_replace(',', '', $rawValue);

    $floatVal = 0.0;
    if (is_numeric($rawValue)) {
        $floatVal = (float)$rawValue;
    }

    $type = $isWithdrawal ? 'withdrawal' : 'income';
    return [$floatVal, $type];
}

try {
    $spreadsheet = IOFactory::load($tempPath);

    foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
        $highestRow = $worksheet->getHighestDataRow();
        $highestColumn = $worksheet->getHighestDataColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        // Read header row
        $headers = [];
        $headerRow = 1;
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $cellValue = trim((string)$worksheet->getCell($colLetter . $headerRow)->getValue());
            $headers[$col] = strtoupper($cellValue);
        }

        // We want these columns
        $needed = [
            'DATE'              => 'date',
            'CONFIRMATION CODE' => 'confirmation_code',
            'AMOUNT'            => 'amount',
            'COMMISSION'        => 'commission',
            'NET AMOUNT'        => 'net_amount'
        ];

        // Map them to their columns
        $colIndexes = [];
        foreach ($needed as $expectedHeader => $mappedKey) {
            $foundColumn = array_search($expectedHeader, $headers, true);
            $colIndexes[$mappedKey] = $foundColumn ?: null;
        }

        // Iterate data rows
        for ($row = 2; $row <= $highestRow; $row++) {
            // skip empty rows
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
                continue;
            }

            // Gather row data
            $rowData = [];
            $rowData['sheet_name'] = $worksheet->getTitle();

            // Date
            $dateVal = '';
            if ($colIndexes['date']) {
                $letter = Coordinate::stringFromColumnIndex($colIndexes['date']);
                $cellVal = $worksheet->getCell($letter . $row)->getValue();
                if (is_numeric($cellVal)) {
                    $dateTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellVal);
                    $dateVal = $dateTime->format('n/j/Y g:i:s A');
                } else {
                    $dateVal = trim($cellVal);
                }
            }
            $rowData['date'] = $dateVal;

            // Confirmation Code
            $confirmationCode = '';
            if ($colIndexes['confirmation_code']) {
                $letter = Coordinate::stringFromColumnIndex($colIndexes['confirmation_code']);
                $confirmationCode = (string)$worksheet->getCell($letter . $row)->getValue();
                $confirmationCode = trim($confirmationCode);
            }
            $rowData['confirmation_code'] = $confirmationCode;

            // Amount (with transaction type)
            $amountFloat       = 0.0;
            $transactionType   = 'income';
            if ($colIndexes['amount']) {
                $letter = Coordinate::stringFromColumnIndex($colIndexes['amount']);
                $amountRaw = (string)$worksheet->getCell($letter . $row)->getValue();

                list($parsedAmount, $type) = parseAmountAndType($amountRaw);
                $amountFloat     = $parsedAmount;
                $transactionType = $type;
            }

            $rowData['amount'] = $amountFloat;
            // We'll set this to the parse result for now; may override if Commission = '-'
            $rowData['transaction_type'] = $transactionType;

            // Commission
            $commissionVal = 0.0;
            $commissionRaw = '';
            if ($colIndexes['commission']) {
                $letter = Coordinate::stringFromColumnIndex($colIndexes['commission']);
                $commissionRaw = (string)$worksheet->getCell($letter . $row)->getValue();
                $commissionRaw = trim($commissionRaw);

                // if it's "-", interpret as 0.0
                if ($commissionRaw === '-') {
                    $commissionVal = 0.0;
                } else {
                    $cleaned = str_replace(',', '', $commissionRaw);
                    if (is_numeric($cleaned)) {
                        $commissionVal = (float)$cleaned;
                    }
                }
            }
            $rowData['commission'] = $commissionVal;

            // If Commission is "-" => definitely a withdrawal
            // (assuming your business logic is that if Commission = '-', then it must be a withdrawal)
            if ($commissionRaw === '-') {
                $rowData['transaction_type'] = 'withdrawal';
            }

            // Net Amount
            $netAmountFloat       = 0.0;
            $netTransactionType   = 'income';
            if ($colIndexes['net_amount']) {
                $letter = Coordinate::stringFromColumnIndex($colIndexes['net_amount']);
                $netAmountRaw = (string)$worksheet->getCell($letter . $row)->getValue();

                list($parsedNetAmount, $netType) = parseAmountAndType($netAmountRaw);
                $netAmountFloat     = $parsedNetAmount;
                $netTransactionType = $netType;
            }
            $rowData['net_amount'] = $netAmountFloat;
            $rowData['net_transaction_type'] = $netTransactionType;

            // Add row
            $allExtractedData[] = $rowData;
        }
    }

    $totalRecords = count($allExtractedData);

    $outputFilename = 'transaction_data1_' . time() . '.json';
    $outputDir = __DIR__ . '/uploads';
    $outputPath = $outputDir . '/' . $outputFilename;

    if (!file_exists($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    file_put_contents(
        $outputPath,
        json_encode($allExtractedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    // Example DB insert
    $stmt = $conn->prepare("INSERT INTO nrevenue (file_name) VALUES (?)");
    $stmt->bind_param("s", $outputPath);
    if (!$stmt->execute()) {
        throw new Exception("Failed to save file record: " . $stmt->error);
    }

    echo json_encode([
        'success'      => true,
        'totalRecords' => $totalRecords,
        'filePath'     => 'uploads/' . $outputFilename
    ]);
    exit;
}
catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}

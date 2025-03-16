<?php

/**
 * Fetches data for DataTables with server-side processing, supporting joins and custom selects.
 *
 * @param string $tableName The name of the table (used if 'from' is not provided).
 * @param array $options Additional options: 'from', 'select', 'columns', 'excludeColumns', etc.
 * @return array The DataTables-compatible response.
 */
function fetchDataForDataTables($tableName, $options = [])
{
    global $conn;

    // Get DataTables parameters
    $draw = $_POST['draw'];
    $start = (int)$_POST['start'];
    $length = (int)$_POST['length'];
    $search = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';
    $orderColumnIndex = isset($_POST['order'][0]['column']) ? (int)$_POST['order'][0]['column'] : 0;
    $orderDirection = isset($_POST['order'][0]['dir']) && in_array(strtoupper($_POST['order'][0]['dir']), ['ASC', 'DESC']) ? $_POST['order'][0]['dir'] : 'ASC';

    // Determine FROM clause, SELECT clause, and columns
    $fromClause = isset($options['from']) ? $options['from'] : $tableName;
    $selectClause = isset($options['select']) ? $options['select'] : '*';
    $columns = isset($options['columns']) ? $options['columns'] : [];

    // If columns are not provided, fetch from the original table (not recommended for joins)
    if (empty($columns)) {
        $result = $conn->query("SHOW COLUMNS FROM $tableName");
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
    }

    // Exclude specific columns if provided
    if (isset($options['excludeColumns'])) {
        $columns = array_diff($columns, $options['excludeColumns']);
    }

    // Determine order column
    $orderBy = '';
    if (!empty($columns)) {
  $orderColumn = isset($columns[$orderColumnIndex]) ? "`{$columns[$orderColumnIndex]}`" : "`id`";
$orderBy = "ORDER BY $orderColumn $orderDirection";

    }

    // Build search condition
    $where = '';
    if (!empty($search)) {
        $conditions = [];
        foreach ($columns as $col) {
            $conditions[] = "$col LIKE '%$search%'";
        }
        $where = "WHERE " . implode(' OR ', $conditions);
    }


    // Total records
    $totalRecordsQuery = "SELECT COUNT(*) AS total FROM $fromClause";
    $totalResult = $conn->query($totalRecordsQuery);
    $totalRecords = $totalResult->fetch_assoc()['total'];

    // Filtered records
    $filteredRecordsQuery = "SELECT COUNT(*) AS filtered FROM $fromClause $where";
    $filteredResult = $conn->query($filteredRecordsQuery);
    $filteredRecords = $filteredResult->fetch_assoc()['filtered'];

    // Fetch data
    $dataQuery = "SELECT $selectClause FROM $fromClause $where $orderBy LIMIT $start, $length";
    $result = $conn->query($dataQuery);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $rowData = [];
        foreach ($columns as $col) {
            // Use the column name; ensure SELECT clause includes these columns or aliases
            $rowData[$col] = $row[$col] ?? null;
        }
        // Add custom options column if needed
        if (isset($options['addOptionsColumn'])) {
            $rowData['options'] = $options['addOptionsColumn']($row);
        }
        $data[] = $rowData;
    }

    return [
        "draw" => intval($draw),
        "recordsTotal" => intval($totalRecords),
        "recordsFiltered" => intval($filteredRecords),
        "data" => $data
    ];
}

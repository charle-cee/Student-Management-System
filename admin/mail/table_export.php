<?php
// Database connection
include('config.php');

// Query to fetch data from the table
$query = "SELECT * FROM registered_users";
$result = $dbh->query($query);

// Check if any data was found
if ($result->rowCount() > 0) {
    // Set headers for file download as SQL
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="exported_table.sql"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Get column names and their data types from the database
    $columns = $result->fetch(PDO::FETCH_ASSOC);
    $columnNames = array_keys($columns);

    // Get column data types from the database schema
    $columnTypes = [];
    $describeQuery = "DESCRIBE registered_users";
    $describeResult = $dbh->query($describeQuery);
    while ($row = $describeResult->fetch(PDO::FETCH_ASSOC)) {
        $columnTypes[$row['Field']] = $row['Type'];
    }

    // Create table structure with correct data types
    echo "DROP TABLE IF EXISTS registered_users;\n";
    echo "CREATE TABLE registered_users (\n";

    $columnDefinitions = [];
    foreach ($columnNames as $column) {
        $type = $columnTypes[$column] ?? 'TEXT'; // Default to TEXT if type not found
        $columnDefinitions[] = "$column $type";
    }
    echo implode(",\n", $columnDefinitions) . "\n";
    echo ");\n\n";

    // Fetch the rows and insert them
    echo "INSERT INTO registered_users (" . implode(", ", $columnNames) . ") VALUES\n";
    $firstRow = true;
    foreach ($result as $row) {
        if (!$firstRow) {
            echo ",\n";
        }
        $firstRow = false;

        $values = [];
        foreach ($columnNames as $column) {
            // Handle proper value formatting for data types
            if (is_numeric($row[$column])) {
                $values[] = $row[$column]; // No quotes for numbers
            } elseif (strtotime($row[$column])) {
                // Check if the value is a valid date and format it as 'YYYY-MM-DD'
                $values[] = "'" . date('Y-m-d', strtotime($row[$column])) . "'";
            } else {
                // Default case for text or string values
                $values[] = "'" . addslashes($row[$column]) . "'";
            }
        }
        echo "(" . implode(", ", $values) . ")";
    }
    echo ";\n";
    exit();
} else {
    echo "No data found for export.";
}
?>

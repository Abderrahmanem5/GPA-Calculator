<?php
include 'db.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="gpa_results.csv"');

$output = fopen("php://output", "w");

fputcsv($output, ['Student','Semester','GPA','Interpretation','Date']);

$result = $conn->query("SELECT * FROM results");

while($row = $result->fetch_assoc()){
fputcsv($output, $row);
}

fclose($output);
exit;
?>

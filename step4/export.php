<?php
// =====================================================================
// export.php – Download course table as CSV
// =====================================================================
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    exit('Invalid record ID.');
}

$stmt = $pdo->prepare("SELECT * FROM gpa_records WHERE id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch();

if (!$record) {
    http_response_code(404);
    exit('Record not found.');
}

$stmtC = $pdo->prepare("SELECT * FROM gpa_courses WHERE record_id = ?");
$stmtC->execute([$id]);
$courses = $stmtC->fetchAll();

// Set CSV headers
$filename = 'GPA_' . preg_replace('/\s+/', '_', $record['student_name'])
          . '_' . $record['semester'] . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');

// BOM for Excel UTF-8 support
fwrite($out, "\xEF\xBB\xBF");

// Meta info
fputcsv($out, ['Student', $record['student_name']]);
fputcsv($out, ['Semester', $record['semester']]);
fputcsv($out, ['GPA', number_format($record['gpa'], 2)]);
fputcsv($out, ['Result', $record['interpretation']]);
fputcsv($out, ['Date', $record['created_at']]);
fputcsv($out, []);

// Table header
fputcsv($out, ['Course Name', 'Credits', 'Grade Points', 'Weighted Points']);

// Table rows
foreach ($courses as $c) {
    $pts = $c['credits'] * $c['grade_point'];
    fputcsv($out, [$c['course_name'], $c['credits'], $c['grade_point'], $pts]);
}

fclose($out);
exit;
?>

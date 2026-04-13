<?php
// =====================================================================
// history.php – Return GPA history as JSON
// =====================================================================
header('Content-Type: application/json');
require_once 'db.php';

// Single record detail
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM gpa_records WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch();

    if (!$record) {
        echo json_encode(['detail' => null]);
        exit;
    }

    $stmtC = $pdo->prepare("SELECT * FROM gpa_courses WHERE record_id = ?");
    $stmtC->execute([$id]);
    $courses = $stmtC->fetchAll();

    $tableHtml  = '<table class="table table-bordered table-sm">';
    $tableHtml .= '<thead class="thead-dark"><tr><th>Course</th><th>Credits</th><th>Grade Points</th><th>Weighted</th></tr></thead><tbody>';
    foreach ($courses as $c) {
        $pts = $c['credits'] * $c['grade_point'];
        $tableHtml .= "<tr>
                         <td>{$c['course_name']}</td>
                         <td>{$c['credits']}</td>
                         <td>{$c['grade_point']}</td>
                         <td>$pts</td>
                       </tr>";
    }
    $tableHtml .= '</tbody></table>';

    $record['tableHtml'] = $tableHtml;
    echo json_encode(['detail' => $record]);
    exit;
}

// All records (last 20)
$stmt = $pdo->query("SELECT * FROM gpa_records ORDER BY created_at DESC LIMIT 20");
$records = $stmt->fetchAll();
echo json_encode(['records' => $records]);
exit;
?>

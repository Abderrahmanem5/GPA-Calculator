<?php
// =====================================================================
// calculate.php – Step 4 (AJAX + MySQL storage)
// =====================================================================
header('Content-Type: application/json');
require_once 'db.php';   // Database connection

if (!isset($_POST['course'], $_POST['credits'], $_POST['grade'],
           $_POST['student_name'], $_POST['semester'])) {
    echo json_encode(['success' => false, 'message' => 'Incomplete data received.']);
    exit;
}

$studentName = trim(htmlspecialchars($_POST['student_name']));
$semester    = trim(htmlspecialchars($_POST['semester']));
$courses     = $_POST['course'];
$credits     = $_POST['credits'];
$grades      = $_POST['grade'];

// ---- Server-side validation ----
if (empty($studentName) || empty($semester)) {
    echo json_encode(['success' => false, 'message' => 'Student name and semester are required.']);
    exit;
}

$totalPoints  = 0;
$totalCredits = 0;
$rows         = [];
$courseNames  = [];

for ($i = 0; $i < count($courses); $i++) {
    $course = trim(htmlspecialchars($courses[$i]));
    $cr     = floatval($credits[$i]);
    $g      = floatval($grades[$i]);

    if (empty($course)) {
        echo json_encode(['success' => false, 'message' => 'All course name fields are required.']);
        exit;
    }
    if ($cr <= 0 || $cr > 10) {
        echo json_encode(['success' => false, 'message' => 'Credit hours must be between 1 and 10.']);
        exit;
    }
    if (in_array(strtolower($course), $courseNames)) {
        echo json_encode(['success' => false, 'message' => "Duplicate course name: \"$course\"."]);
        exit;
    }
    $courseNames[] = strtolower($course);

    $pts = $cr * $g;
    $totalPoints  += $pts;
    $totalCredits += $cr;
    $rows[] = ['course' => $course, 'cr' => $cr, 'g' => $g, 'pts' => $pts];
}

if ($totalCredits <= 0) {
    echo json_encode(['success' => false, 'message' => 'No valid courses entered.']);
    exit;
}

if ($totalCredits > 60) {
    echo json_encode(['success' => false, 'message' => 'Total credits exceed the maximum of 60.']);
    exit;
}

// ---- Compute GPA ----
$gpa = $totalPoints / $totalCredits;
if ($gpa >= 3.7)      { $interpretation = 'Distinction'; }
elseif ($gpa >= 3.0)  { $interpretation = 'Merit'; }
elseif ($gpa >= 2.0)  { $interpretation = 'Pass'; }
else                   { $interpretation = 'Fail'; }

$message = "Your GPA is " . number_format($gpa, 2) . " ($interpretation).";

// ---- Bootstrap styled table HTML ----
$tableHtml  = '<table class="table table-bordered table-sm mt-3">';
$tableHtml .= '<thead class="thead-dark"><tr>
                 <th>Course</th><th>Credits</th><th>Grade Points</th><th>Weighted</th>
               </tr></thead><tbody>';
foreach ($rows as $r) {
    $tableHtml .= "<tr>
                     <td>{$r['course']}</td>
                     <td>{$r['cr']}</td>
                     <td>{$r['g']}</td>
                     <td>{$r['pts']}</td>
                   </tr>";
}
$tableHtml .= '</tbody></table>';

// ---- Save to MySQL ----
$recordId = null;
try {
    // Insert GPA record
    $stmt = $pdo->prepare(
        "INSERT INTO gpa_records (student_name, semester, gpa, interpretation, created_at)
         VALUES (:sn, :sem, :gpa, :interp, NOW())"
    );
    $stmt->execute([
        ':sn'     => $studentName,
        ':sem'    => $semester,
        ':gpa'    => $gpa,
        ':interp' => $interpretation,
    ]);
    $recordId = $pdo->lastInsertId();

    // Insert individual courses
    $stmtC = $pdo->prepare(
        "INSERT INTO gpa_courses (record_id, course_name, credits, grade_point)
         VALUES (:rid, :cn, :cr, :gp)"
    );
    foreach ($rows as $r) {
        $stmtC->execute([
            ':rid' => $recordId,
            ':cn'  => $r['course'],
            ':cr'  => $r['cr'],
            ':gp'  => $r['g'],
        ]);
    }
} catch (PDOException $e) {
    // DB errors are non-fatal: still return the result
    error_log('DB error: ' . $e->getMessage());
}

echo json_encode([
    'success'        => true,
    'gpa'            => $gpa,
    'interpretation' => $interpretation,
    'message'        => $message,
    'tableHtml'      => $tableHtml,
    'record_id'      => $recordId,
]);
exit;
?>

<?php
// ======================================================================
// index.php – Step 2: Single-file GPA Calculator (PHP + HTML combined)
// ======================================================================
$result    = '';
$tableHtml = '';
$gpa       = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courses = $_POST['course']  ?? [];
    $credits = $_POST['credits'] ?? [];
    $grades  = $_POST['grade']   ?? [];
    $totalPoints  = 0;
    $totalCredits = 0;
    $rows = [];

    $gradeMap = ['4.0'=>'A','3.0'=>'B','2.0'=>'C','1.0'=>'D','0.0'=>'F'];

    for ($i = 0; $i < count($courses); $i++) {
        $course = trim(htmlspecialchars($courses[$i]));
        $cr     = floatval($credits[$i]);
        $g      = floatval($grades[$i]);
        if ($cr <= 0 || empty($course)) continue;
        $pts = $cr * $g;
        $totalPoints  += $pts;
        $totalCredits += $cr;
        $rows[] = ['course'=>$course,'cr'=>$cr,'g'=>$g,'pts'=>$pts];
    }

    if ($totalCredits > 0) {
        $gpa = $totalPoints / $totalCredits;
        if ($gpa >= 3.7)      $interpretation = 'Distinction';
        elseif ($gpa >= 3.0)  $interpretation = 'Merit';
        elseif ($gpa >= 2.0)  $interpretation = 'Pass';
        else                  $interpretation = 'Fail';

        $result = 'Your GPA is <strong>' . number_format($gpa, 2) . '</strong> — ' . $interpretation;

        $tableHtml  = '<table>';
        $tableHtml .= '<thead><tr><th>Course</th><th>Credits</th><th>Grade</th><th>Grade Points</th></tr></thead><tbody>';
        foreach ($rows as $r) {
            $gradeLabel = $gradeMap[number_format($r['g'], 1)] ?? number_format($r['g'], 1);
            $tableHtml .= "<tr>
                <td>{$r['course']}</td>
                <td>{$r['cr']}</td>
                <td>{$gradeLabel}</td>
                <td>" . number_format($r['pts'], 2) . "</td>
              </tr>";
        }
        $tableHtml .= '</tbody></table>';
    } else {
        $result = 'No valid courses were entered.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="GPA Calculator Step 2 – single-page PHP form with result.">
  <title>GPA Calculator – Step 2</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="style.css">
  <script src="script.js" defer></script>
</head>
<body>
  <div class="wrapper">
    <header class="page-header">
      <h1>GPA Calculator</h1>
      <p class="subtitle">Enter your courses to compute your Grade Point Average</p>
    </header>

    <main class="card">

      <?php if ($result !== ''): ?>
        <div class="result-box <?= ($gpa !== null && $gpa >= 3.7) ? 'result-box--success' : (($gpa !== null && $gpa >= 2.0) ? 'result-box--warn' : 'result-box--fail') ?>">
          <p><?= $result ?></p>
        </div>
        <?= $tableHtml ?>
        <hr class="divider">
      <?php endif; ?>

      <form action="" method="post" onsubmit="return validateForm();">
        <div id="courses">
          <div class="course-row">
            <div class="field">
              <label>Course Name</label>
              <input type="text" name="course[]" placeholder="e.g. Mathematics" required>
            </div>
            <div class="field field--sm">
              <label>Credits</label>
              <input type="number" name="credits[]" placeholder="3" min="1" max="10" required>
            </div>
            <div class="field field--sm">
              <label>Grade</label>
              <select name="grade[]">
                <option value="4.0">A</option>
                <option value="3.0">B</option>
                <option value="2.0">C</option>
                <option value="1.0">D</option>
                <option value="0.0">F</option>
              </select>
            </div>
          </div>
        </div>

        <div class="actions">
          <button type="button" class="btn btn--secondary" onclick="addCourse()">+ Add Course</button>
          <button type="submit" class="btn btn--primary">Calculate GPA</button>
        </div>
      </form>
    </main>
  </div>
</body>
</html>

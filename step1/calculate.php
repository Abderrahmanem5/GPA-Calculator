<?php
// ======================================================================
// calculate.php – Step 1: Process GPA form and render result
// ======================================================================
if (!isset($_POST['course'], $_POST['credits'], $_POST['grade'])) {
    header('Location: index.html');
    exit;
}

$courses = $_POST['course'];
$credits = $_POST['credits'];
$grades  = $_POST['grade'];
$totalPoints  = 0;
$totalCredits = 0;
$rows = [];

for ($i = 0; $i < count($courses); $i++) {
    $course = trim(htmlspecialchars($courses[$i]));
    $cr     = floatval($credits[$i]);
    $g      = floatval($grades[$i]);
    if ($cr <= 0 || empty($course)) continue;
    $pts = $cr * $g;
    $totalPoints  += $pts;
    $totalCredits += $cr;
    $rows[] = ['course' => $course, 'cr' => $cr, 'g' => $g, 'pts' => $pts];
}

$gpa = 0;
$interpretation = 'N/A';
if ($totalCredits > 0) {
    $gpa = $totalPoints / $totalCredits;
    if ($gpa >= 3.7)      $interpretation = 'Distinction';
    elseif ($gpa >= 3.0)  $interpretation = 'Merit';
    elseif ($gpa >= 2.0)  $interpretation = 'Pass';
    else                  $interpretation = 'Fail';
}

// Grade label map (numeric → letter)
$gradeMap = ['4.0'=>'A','3.0'=>'B','2.0'=>'C','1.0'=>'D','0.0'=>'F'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GPA Result – Step 1</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="wrapper">
    <header class="page-header">
      <h1>GPA Result</h1>
      <p class="subtitle">Your calculated Grade Point Average</p>
    </header>

    <main class="card">
      <?php if (count($rows) === 0): ?>
        <p style="color:#ff4d6d;text-align:center;">No valid courses were submitted.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Course</th>
              <th>Credits</th>
              <th>Grade</th>
              <th>Grade Points</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r):
              $gradeLabel = $gradeMap[number_format($r['g'], 1)] ?? number_format($r['g'], 1);
            ?>
            <tr>
              <td><?= $r['course'] ?></td>
              <td><?= $r['cr'] ?></td>
              <td><?= $gradeLabel ?></td>
              <td><?= number_format($r['pts'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <p style="margin-top:24px;text-align:center;font-size:1.1rem;">
          Your GPA is <strong><?= number_format($gpa, 2) ?></strong>
          &nbsp;—&nbsp; <?= $interpretation ?>
        </p>
      <?php endif; ?>

      <div class="actions" style="margin-top:24px;justify-content:center;">
        <a href="index.html" class="btn btn--secondary">← Calculate Again</a>
      </div>
    </main>
  </div>
</body>
</html>

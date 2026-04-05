<?php
header('Content-Type: application/json');
include 'db.php';

$student = $_POST['student'];
$semester = $_POST['semester'];
$courses = $_POST['course'];
$credits = $_POST['credits'];
$grades = $_POST['grade'];

$totalPoints = 0;
$totalCredits = 0;

$table = "<table class='table table-bordered mt-3'>
<tr><th>Course</th><th>Credits</th><th>Grade</th><th>Points</th></tr>";

for ($i = 0; $i < count($courses); $i++) {

$cr = floatval($credits[$i]);
$gr = floatval($grades[$i]);

$pts = $cr * $gr;

$totalPoints += $pts;
$totalCredits += $cr;

$table .= "<tr>
<td>{$courses[$i]}</td>
<td>$cr</td>
<td>$gr</td>
<td>$pts</td>
</tr>";
}

$table .= "</table>";

if($totalCredits > 0){

$gpa = $totalPoints / $totalCredits;

if ($gpa >= 3.7) $interp = "Distinction";
elseif ($gpa >= 3.0) $interp = "Merit";
elseif ($gpa >= 2.0) $interp = "Pass";
else $interp = "Fail";

$conn->query("INSERT INTO results (student_name, semester, gpa, interpretation)
VALUES ('$student','$semester','$gpa','$interp')");

echo json_encode([
"success" => true,
"gpa" => $gpa,
"message" => "GPA = " . number_format($gpa,2) . " ($interp)",
"table" => $table
]);

} else {

echo json_encode(["success"=>false]);

}
?>

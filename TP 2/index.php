<?php
$result_message = ""; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $credits = $_POST['credits'];
    $grades = $_POST['grade'];

    $totalPoints = 0;
    $totalCredits = 0;

    for ($i = 0; $i < count($credits); $i++) {
        $c = floatval($credits[$i]);
        $g = floatval($grades[$i]);
        $totalPoints += ($c * $g);
        $totalCredits += $c;
    }

    if ($totalCredits > 0) {
        $gpa = $totalPoints / $totalCredits;
        // نجهز رسالة النتيجة لعرضها في الأسفل
        $result_message = "<div style='background:#d4edda; padding:15px; border-radius:5px; margin-bottom:20px;'>
                            <h3>المعدل المحسوب هو: " . number_format($gpa, 2) . "</h3>
                          </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>حاسبة المعدل - ملف مدمج</title>
    <style>
        /* --- الجزء الثاني: كود CSS (التنسيق) --- */
        body { font-family: Arial; margin: 40px; background-color: #f4f4f4; text-align: center; }
        .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); display: inline-block; min-width: 400px; }
        input, select { margin: 10px; padding: 10px; width: 80%; border: 1px solid #ddd; border-radius: 5px; }
        button { padding: 10px 20px; background: #28a745; color: white; border: none; cursor: pointer; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container">
    <h1>حاسبة المعدل (GPA)</h1>

    <?php echo $result_message; ?>

    <form method="POST" action="">
        <div id="course-row">
            <input type="text" name="course[]" placeholder="اسم المادة" required>
            <input type="number" name="credits[]" placeholder="الوحدات (Credits)" required>
            <select name="grade[]">
                <option value="4.0">ممتاز (A)</option>
                <option value="3.0">جيد جداً (B)</option>
                <option value="2.0">متوسط (C)</option>
                <option value="1.0">مقبول (D)</option>
                <option value="0.0">راسب (F)</option>
            </select>
        </div>
        <button type="submit">احسب المعدل الآن</button>
    </form>
</div>

</body>
</html>

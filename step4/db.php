<?php
// =====================================================================
// db.php – Database connection + table setup
// =====================================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'gpa_calculator');
define('DB_USER', 'root');       // Change to your MySQL username
define('DB_PASS', '');           // Change to your MySQL password
define('DB_CHARSET', 'utf8mb4');

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// ---- Auto-create tables if they don't exist ----
$pdo->exec("
    CREATE TABLE IF NOT EXISTS gpa_records (
        id              INT AUTO_INCREMENT PRIMARY KEY,
        student_name    VARCHAR(100) NOT NULL,
        semester        VARCHAR(50)  NOT NULL,
        gpa             DECIMAL(4,2) NOT NULL,
        interpretation  VARCHAR(20)  NOT NULL,
        created_at      DATETIME     NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS gpa_courses (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        record_id   INT            NOT NULL,
        course_name VARCHAR(80)    NOT NULL,
        credits     DECIMAL(4,1)   NOT NULL,
        grade_point DECIMAL(3,1)   NOT NULL,
        FOREIGN KEY (record_id) REFERENCES gpa_records(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
?>

# GPA Calculator — Web Development Lab 2

> **Course:** Web Development — 2nd Year LMD, Computer Science  
> **Objective:** Build a GPA Calculator web app across 4 progressive steps.

---

## 📁 Project Structure

```
gpa-calculator/
├── step1/              ← Separate files, no jQuery
│   ├── index.html
│   ├── script.js
│   ├── style.css
│   └── calculate.php
│
├── step2/              ← Merged HTML + PHP in one file
│   ├── index.php
│   ├── script.js
│   └── style.css
│
├── step3/              ← jQuery + Bootstrap + AJAX
│   ├── index.html
│   ├── script.js
│   ├── style.css
│   └── calculate.php
│
├── step4/              ← Homework: MySQL + CSV + Progress Bar + Modal
│   ├── index.php
│   ├── script.js
│   ├── style.css
│   ├── calculate.php
│   ├── db.php
│   ├── history.php
│   └── export.php
│
└── README.md
```

---

## 🚀 How to Run

### Requirements
- **PHP 7.4+** (or PHP 8.x)
- **Apache / Nginx** with PHP support — recommended: [XAMPP](https://www.apachefriends.org/) or [Laragon](https://laragon.org/)
- **MySQL 5.7+** (only for Step 4)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/YOUR_USERNAME/gpa-calculator.git
   ```

2. **Place inside your web server root**
   - XAMPP → `C:/xampp/htdocs/gpa-calculator/`
   - Laragon → `C:/laragon/www/gpa-calculator/`

3. **Start Apache (and MySQL for Step 4)**

4. **Open in browser:**
   - Step 1: `http://localhost/gpa-calculator/step1/index.html`
   - Step 2: `http://localhost/gpa-calculator/step2/index.php`
   - Step 3: `http://localhost/gpa-calculator/step3/index.html`
   - Step 4: `http://localhost/gpa-calculator/step4/index.php`

---

## 🗄️ Database Setup (Step 4 only)

1. Open **phpMyAdmin** → Create a new database named `gpa_calculator`
2. Edit `step4/db.php` and set your MySQL credentials:
   ```php
   define('DB_USER', 'root');   // your username
   define('DB_PASS', '');       // your password
   ```
3. Tables are **created automatically** on first run (no SQL import needed).

### Database Schema

```sql
-- Stores each GPA calculation session
CREATE TABLE gpa_records (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    student_name    VARCHAR(100) NOT NULL,
    semester        VARCHAR(50)  NOT NULL,
    gpa             DECIMAL(4,2) NOT NULL,
    interpretation  VARCHAR(20)  NOT NULL,
    created_at      DATETIME     NOT NULL
);

-- Stores individual courses for each session
CREATE TABLE gpa_courses (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    record_id   INT          NOT NULL,
    course_name VARCHAR(80)  NOT NULL,
    credits     DECIMAL(4,1) NOT NULL,
    grade_point DECIMAL(3,1) NOT NULL,
    FOREIGN KEY (record_id) REFERENCES gpa_records(id) ON DELETE CASCADE
);
```

---

## 📖 GPA Formula

```
GPA = Σ (Grade Points × Credits) / Σ Credits
```

| Letter Grade | Grade Points | Interpretation |
|:---:|:---:|:---|
| A / A+ | 4.0 | Excellent |
| B | 3.0 | Good |
| C | 2.0 | Average |
| D | 1.0 | Below Average |
| F | 0.0 | Fail |

| GPA Range | Result |
|:---:|:---|
| ≥ 3.7 | 🟢 Distinction |
| 3.0 – 3.69 | 🔵 Merit |
| 2.0 – 2.99 | 🟡 Pass |
| < 2.0 | 🔴 Fail |

---

## 📋 Step-by-Step Summary

### Step 1 — Separate Files (Vanilla JS)
- `index.html` — HTML form with course/credits/grade fields
- `script.js` — `addCourse()` to add rows dynamically + `validateForm()` for client-side validation
- `style.css` — Basic styling
- `calculate.php` — PHP processes POST data, computes GPA, echoes result + table

### Step 2 — Merged File
- Everything combined into `index.php`
- Form stays visible after submission
- Result + table displayed above the form

### Step 3 — jQuery + Bootstrap + AJAX
- Bootstrap 4 grid layout and alert color classes
- jQuery: dynamic row cloning, remove button, AJAX form submission
- `calculate.php` returns **JSON** (no page reload)
- Alert color: `success` (Distinction), `info` (Merit), `warning` (Pass), `danger` (Fail)

### Step 4 — Homework (Extended Features)
| Feature | Implementation |
|---|---|
| Student name + semester input | HTML fields, validated server & client |
| MySQL storage | `gpa_records` + `gpa_courses` tables via PDO |
| History view | `history.php` returns JSON, rendered in Bootstrap table |
| Detail modal | Bootstrap modal shows full course breakdown |
| Progress bar | Bootstrap `.progress-bar` color-coded by GPA range |
| CSV export | `export.php` streams `.csv` with BOM for Excel |
| Duplicate course check | Client + server-side validation |
| Max credits limit | Capped at 60 total credits (server validated) |
| Tooltips | Bootstrap tooltip on each input field |

---

## 🔧 Technologies Used

- **HTML5 / CSS3**
- **PHP 7.4+** (PDO for MySQL)
- **JavaScript (ES5 Vanilla + jQuery 3.6)**
- **Bootstrap 4.5**
- **MySQL 5.7+**

---

## 👨‍💻 GitHub Workflow

```bash
# First time setup
git init
git add .
git commit -m "Initial commit: GPA Calculator - Steps 1-4"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/gpa-calculator.git
git push -u origin main

# For each step update
git add step1/
git commit -m "Step 1: separate files with vanilla JS validation"
git push

git add step2/
git commit -m "Step 2: merged HTML+PHP single file"
git push

git add step3/
git commit -m "Step 3: jQuery AJAX + Bootstrap alerts"
git push

git add step4/
git commit -m "Step 4: MySQL storage, progress bar, CSV export, modal"
git push
```

---

## 📝 .gitignore

```
# Ignore sensitive config (if you separate DB credentials)
step4/config.php

# OS files
.DS_Store
Thumbs.db
```

---

*Lab 2 — Web Development, 2nd Year LMD Computer Science*

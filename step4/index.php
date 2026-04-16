<?php
// =====================================================================
// Step 4 – Homework: Extended GPA Calculator
// Features:
//   - MySQL storage (student name + semester + courses)
//   - Bootstrap progress bar (color-coded by GPA range)
//   - CSV export button
//   - Bootstrap modals, cards, tooltips
//   - Extended client + server validation
// =====================================================================
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GPA Calculator – Extended</title>
  <!-- Bootstrap 4 CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="style.css">
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="script.js" defer></script>
</head>
<body>
<div class="container mt-4">

  <!-- Header Card -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h2 class="card-title mb-0">
        <span class="badge badge-primary mr-2">GPA</span>
        Grade Point Average Calculator
      </h2>
      <p class="card-text text-muted mt-1">Enter your courses, credits and grades to compute your GPA.</p>
    </div>
  </div>

  <!-- Result area -->
  <div id="result" class="mb-3"></div>

  <!-- Main Form Card -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <form id="gpaForm">

        <!-- Student info row -->
        <div class="form-row mb-3">
          <div class="col-md-5">
            <label for="studentName">Student Name <span class="text-danger">*</span></label>
            <input type="text" id="studentName" name="student_name"
                   class="form-control" placeholder="e.g. Ahmed Benali"
                   maxlength="100" required>
          </div>
          <div class="col-md-4">
            <label for="semester">Semester / Term <span class="text-danger">*</span></label>
            <input type="text" id="semester" name="semester"
                   class="form-control" placeholder="e.g. S1 2024"
                   maxlength="50" required>
          </div>
        </div>

        <!-- Course rows -->
        <div id="courses">
          <div class="course-row form-row mb-2 align-items-center">
            <div class="col">
              <input type="text" name="course[]" class="form-control"
                     placeholder="Course name" maxlength="80" required
                     data-toggle="tooltip" title="Enter the course name (max 80 chars)">
            </div>
            <div class="col-2">
              <input type="number" name="credits[]" class="form-control"
                     placeholder="Credits" min="1" max="10" required
                     data-toggle="tooltip" title="Credit hours: 1–10">
            </div>
            <div class="col-2">
              <select name="grade[]" class="form-control">
                <option value="4.0">A / A+</option>
                <option value="3.0">B</option>
                <option value="2.0">C</option>
                <option value="1.0">D</option>
                <option value="0.0">F</option>
              </select>
            </div>
          </div>
        </div>

        <div class="d-flex gpa-actions mt-2 mb-3">
          <button type="button" id="addCourse" class="btn btn-outline-secondary mr-2">
            + Add Course
          </button>
          <button type="submit" class="btn btn-primary">
            Calculate GPA
          </button>
        </div>

      </form>
    </div>
  </div>

  <!-- Previous Calculations (loaded from DB) -->
  <div class="card shadow-sm mb-4" id="historyCard" style="display:none;">
    <div class="card-header d-flex justify-content-between align-items-center">
      <strong>Previous Calculations</strong>
      <button class="btn btn-sm btn-outline-secondary" id="refreshHistory">Refresh</button>
    </div>
    <div class="card-body p-0" id="historyBody"></div>
  </div>

  <!-- View All History button -->
  <button class="btn btn-outline-info btn-sm mb-4" id="loadHistory">
    View Calculation History
  </button>

</div><!-- /container -->

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Calculation Detail</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body" id="modalBody"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

</body>
</html>

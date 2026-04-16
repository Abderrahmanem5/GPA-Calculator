$(document).ready(function () {

  // -------------------------------------------------------
  // Add course row (clone first row, clear values)
  // -------------------------------------------------------
  $('#addCourse').click(function () {
    var rowCount = $('.course-row').length;
    if (rowCount >= 15) {
      alert('Maximum 15 courses allowed.');
      return;
    }

    var row = $('.course-row').first().clone();
    row.find('input').val('');

    // Add remove button if not already present
    if (row.find('.btn-remove-row').length === 0) {
      row.append(
        '<div class="col-auto align-self-end">' +
        '<button type="button" class="btn-remove-row">✕</button>' +
        '</div>'
      );
    }

    $('#courses').append(row);
  });

  // -------------------------------------------------------
  // Remove course row (delegate, keep at least 1)
  // -------------------------------------------------------
  $(document).on('click', '.btn-remove-row', function () {
    if ($('.course-row').length > 1) {
      $(this).closest('.course-row').remove();
    } else {
      alert('You need at least one course.');
    }
  });

  // -------------------------------------------------------
  // Calculate GPA client-side on submit
  // -------------------------------------------------------
  $('#gpaForm').submit(function (e) {
    e.preventDefault();

    var courseInputs = $('[name="course[]"]');
    var creditInputs = $('[name="credits[]"]');
    var gradeInputs  = $('[name="grade[]"]');
    var valid = true;

    // Validate course names
    courseInputs.each(function () {
      if ($(this).val().trim() === '') {
        valid = false;
        return false; // break
      }
    });

    // Validate credits
    if (valid) {
      creditInputs.each(function () {
        var v = parseFloat($(this).val());
        if (isNaN(v) || v <= 0) {
          valid = false;
          return false;
        }
      });
    }

    if (!valid) {
      $('#result').html(
        '<div class="alert alert-warning">Please fill in all fields with valid values.</div>'
      );
      return;
    }

    // Compute GPA
    var totalCredits = 0;
    var weightedSum  = 0;
    var gradeMap     = { '4.0': 'A', '3.0': 'B', '2.0': 'C', '1.0': 'D', '0.0': 'F' };
    var tableRows    = '';

    courseInputs.each(function (i) {
      var name      = $(this).val().trim();
      var credit    = parseFloat($(creditInputs[i]).val());
      var gradeVal  = parseFloat($(gradeInputs[i]).val());
      var gradeKey  = parseFloat($(gradeInputs[i]).val()).toFixed(1);
      var gradeStr  = gradeMap[gradeKey] || gradeKey;

      weightedSum  += gradeVal * credit;
      totalCredits += credit;

      tableRows += '<tr>' +
        '<td>' + name + '</td>' +
        '<td>' + credit + '</td>' +
        '<td>' + gradeStr + '</td>' +
        '<td>' + (gradeVal * credit).toFixed(2) + '</td>' +
        '</tr>';
    });

    var gpa = totalCredits > 0 ? (weightedSum / totalCredits) : 0;

    // Color classes
    var alertClass, progressColor, interpretation;
    if (gpa >= 3.7) {
      alertClass = 'alert-success'; progressColor = 'bg-success'; interpretation = 'Distinction';
    } else if (gpa >= 3.0) {
      alertClass = 'alert-info';    progressColor = 'bg-info';    interpretation = 'Merit';
    } else if (gpa >= 2.0) {
      alertClass = 'alert-warning'; progressColor = 'bg-warning'; interpretation = 'Pass';
    } else {
      alertClass = 'alert-danger';  progressColor = 'bg-danger';  interpretation = 'Fail';
    }

    var progressWidth = Math.min((gpa / 4.0) * 100, 100).toFixed(1);

    var tableHtml =
      '<table class="table table-bordered table-sm mt-3">' +
        '<thead><tr>' +
          '<th>Course</th><th>Credits</th><th>Grade</th><th>Weighted Points</th>' +
        '</tr></thead>' +
        '<tbody>' + tableRows + '</tbody>' +
      '</table>';

    var html =
      '<div class="alert ' + alertClass + '">' +
        'Your GPA is: <strong>' + gpa.toFixed(2) + '</strong> / 4.00 &nbsp;—&nbsp; ' + interpretation +
      '</div>' +
      '<div class="mb-3">' +
      '  <small class="text-muted">GPA Scale (0 – 4.0)</small>' +
      '  <div class="progress mt-1" style="height:22px;">' +
      '    <div class="progress-bar ' + progressColor + '" role="progressbar"' +
      '         style="width:' + progressWidth + '%"' +
      '         aria-valuenow="' + gpa + '" aria-valuemin="0" aria-valuemax="4">' +
      '      ' + gpa.toFixed(2) +
      '    </div>' +
      '  </div>' +
      '</div>' +
      tableHtml;

    $('#result').html(html);

    // Scroll to result
    $('html, body').animate({ scrollTop: $('#result').offset().top - 20 }, 300);
  });

});

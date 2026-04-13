$(document).ready(function () {

  // Add a new course row
  $('#addCourse').click(function () {
    var row = $('.course-row').first().clone();
    row.find('input').val('');
    row.append(
      '<div class="col-auto">' +
      '<button type="button" class="btn btn-danger remove-row">X</button>' +
      '</div>'
    );
    $('#courses').append(row);
  });

  // Remove a course row
  $(document).on('click', '.remove-row', function () {
    if ($('.course-row').length > 1) {
      $(this).closest('.course-row').remove();
    }
  });

  // Calculate GPA entirely in JavaScript (no server needed)
  $('#gpaForm').submit(function (e) {
    e.preventDefault();

    var courses  = $('[name="course[]"]');
    var credits  = $('[name="credits[]"]');
    var grades   = $('[name="grade[]"]');
    var valid    = true;

    // Validation
    courses.each(function () {
      if ($(this).val().trim() === '') valid = false;
    });
    credits.each(function () {
      var v = parseFloat($(this).val());
      if (isNaN(v) || v <= 0) valid = false;
    });

    if (!valid) {
      $('#result').html(
        '<div class="alert alert-warning">Please enter valid values in all fields.</div>'
      );
      return;
    }

    // Compute GPA
    var totalCredits = 0;
    var weightedSum  = 0;
    var gradeLabels  = { '4.0': 'A', '3.0': 'B', '2.0': 'C', '1.0': 'D', '0.0': 'F' };
    var tableRows    = '';

    for (var i = 0; i < courses.length; i++) {
      var courseName  = $(courses[i]).val().trim();
      var creditVal   = parseFloat($(credits[i]).val());
      var gradeVal    = parseFloat($(grades[i]).val());
      var gradeStr    = gradeLabels[$(grades[i]).val()] || $(grades[i]).val();

      weightedSum  += gradeVal * creditVal;
      totalCredits += creditVal;

      tableRows += '<tr>' +
        '<td>' + courseName + '</td>' +
        '<td>' + creditVal + '</td>' +
        '<td>' + gradeStr + '</td>' +
        '<td>' + gradeVal.toFixed(1) + '</td>' +
        '</tr>';
    }

    var gpa = totalCredits > 0 ? (weightedSum / totalCredits) : 0;

    var alertClass = 'alert-danger';
    if (gpa >= 3.7)      alertClass = 'alert-success';
    else if (gpa >= 3.0) alertClass = 'alert-info';
    else if (gpa >= 2.0) alertClass = 'alert-warning';

    var tableHtml =
      '<table class="table table-bordered mt-3">' +
        '<thead><tr>' +
          '<th>Course</th><th>Credits</th><th>Grade</th><th>Points</th>' +
        '</tr></thead>' +
        '<tbody>' + tableRows + '</tbody>' +
      '</table>';

    $('#result').html(
      '<div class="alert ' + alertClass + '">' +
        'Your GPA is: <strong>' + gpa.toFixed(2) + '</strong> / 4.00' +
      '</div>' +
      tableHtml
    );
  });

});

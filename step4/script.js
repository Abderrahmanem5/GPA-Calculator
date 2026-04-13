$(document).ready(function () {

  // Enable Bootstrap tooltips
  $('[data-toggle="tooltip"]').tooltip();

  // -------------------------------------------------------
  // Add course row
  // -------------------------------------------------------
  $('#addCourse').click(function () {
    var rowCount = $('.course-row').length;
    if (rowCount >= 15) {
      alert('Maximum 15 courses allowed.');
      return;
    }
    var row = $('.course-row').first().clone();
    row.find('input').val('');
    row.find('[data-toggle="tooltip"]').tooltip();
    row.append(
      '<div class="col-auto">' +
      '<button type="button" class="btn btn-danger btn-sm remove-row" title="Remove course">✕</button>' +
      '</div>'
    );
    $('#courses').append(row);
  });

  // -------------------------------------------------------
  // Remove course row
  // -------------------------------------------------------
  $(document).on('click', '.remove-row', function () {
    if ($('.course-row').length > 1) {
      $(this).closest('.course-row').remove();
    } else {
      alert('You need at least one course.');
    }
  });

  // -------------------------------------------------------
  // Validate and submit via AJAX
  // -------------------------------------------------------
  $('#gpaForm').submit(function (e) {
    e.preventDefault();
    var valid = true;
    var courseNames = [];

    // Validate student name
    if ($('#studentName').val().trim() === '') {
      showAlert('danger', 'Student name is required.');
      return;
    }
    if ($('#semester').val().trim() === '') {
      showAlert('danger', 'Semester/Term is required.');
      return;
    }

    // Validate courses
    $('[name="course[]"]').each(function () {
      var name = $(this).val().trim();
      if (name === '') { valid = false; return false; }
      if (courseNames.includes(name.toLowerCase())) {
        showAlert('warning', 'Duplicate course name: "' + name + '". Please use unique names.');
        valid = false;
        return false;
      }
      courseNames.push(name.toLowerCase());
    });

    if (!valid) return;

    var totalCredits = 0;
    $('[name="credits[]"]').each(function () {
      var v = parseFloat($(this).val());
      if (isNaN(v) || v <= 0 || v > 10) {
        showAlert('warning', 'Credits must be between 1 and 10.');
        valid = false;
        return false;
      }
      totalCredits += v;
    });

    if (!valid) return;

    if (totalCredits > 60) {
      showAlert('warning', 'Total credits exceed the maximum limit of 60.');
      return;
    }

    $.ajax({
      url: 'calculate.php',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      beforeSend: function () {
        $('#result').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>');
      },
      success: function (response) {
        if (response.success) {
          var alertClass = gpaAlertClass(response.gpa);
          var progressColor = gpaProgressColor(response.gpa);
          var progressWidth = Math.min((response.gpa / 4.0) * 100, 100).toFixed(1);

          var html =
            '<div class="alert ' + alertClass + '">' + response.message + '</div>' +
            // Progress bar
            '<div class="mb-3">' +
            '  <small class="text-muted">GPA Scale (0 – 4.0)</small>' +
            '  <div class="progress" style="height:24px;">' +
            '    <div class="progress-bar ' + progressColor + '" role="progressbar"' +
            '         style="width:' + progressWidth + '%"' +
            '         aria-valuenow="' + response.gpa + '" aria-valuemin="0" aria-valuemax="4">' +
            '      ' + parseFloat(response.gpa).toFixed(2) +
            '    </div>' +
            '  </div>' +
            '</div>' +
            response.tableHtml +
            // CSV Export button
            '<a href="export.php?id=' + (response.record_id || '') + '"' +
            '   class="btn btn-outline-success btn-sm mt-2" id="csvBtn">' +
            '  &#x1F4E5; Export CSV' +
            '</a>';

          $('#result').html(html);
        } else {
          showAlert('danger', response.message);
        }
      },
      error: function () {
        showAlert('danger', 'Server error occurred. Please try again.');
      }
    });
  });

  // -------------------------------------------------------
  // History
  // -------------------------------------------------------
  $('#loadHistory, #refreshHistory').click(function () {
    $.get('history.php', function (data) {
      if (data.records && data.records.length > 0) {
        var html = '<table class="table table-sm table-hover mb-0">' +
                   '<thead class="thead-light"><tr>' +
                   '<th>Student</th><th>Semester</th><th>GPA</th><th>Result</th><th>Date</th><th></th>' +
                   '</tr></thead><tbody>';
        data.records.forEach(function (r) {
          html += '<tr>' +
                  '<td>' + escHtml(r.student_name) + '</td>' +
                  '<td>' + escHtml(r.semester) + '</td>' +
                  '<td><strong>' + parseFloat(r.gpa).toFixed(2) + '</strong></td>' +
                  '<td><span class="badge badge-' + gpaBootstrapBadge(r.gpa) + '">' + r.interpretation + '</span></td>' +
                  '<td><small>' + r.created_at + '</small></td>' +
                  '<td>' +
                    '<button class="btn btn-xs btn-outline-primary btn-sm view-detail" data-id="' + r.id + '">View</button> ' +
                    '<a href="export.php?id=' + r.id + '" class="btn btn-xs btn-outline-success btn-sm">CSV</a>' +
                  '</td>' +
                  '</tr>';
        });
        html += '</tbody></table>';
        $('#historyBody').html(html);
        $('#historyCard').show();
      } else {
        $('#historyBody').html('<p class="p-3 text-muted mb-0">No records found.</p>');
        $('#historyCard').show();
      }
    }, 'json');
  });

  // View detail modal
  $(document).on('click', '.view-detail', function () {
    var id = $(this).data('id');
    $.get('history.php', { id: id }, function (data) {
      if (data.detail) {
        $('#modalTitle').text(data.detail.student_name + ' — ' + data.detail.semester);
        var body = '<p>GPA: <strong>' + parseFloat(data.detail.gpa).toFixed(2) + '</strong> (' + data.detail.interpretation + ')</p>';
        body += data.detail.tableHtml;
        $('#modalBody').html(body);
        $('#detailModal').modal('show');
      }
    }, 'json');
  });

  // -------------------------------------------------------
  // Helpers
  // -------------------------------------------------------
  function showAlert(type, msg) {
    $('#result').html('<div class="alert alert-' + type + '">' + msg + '</div>');
  }

  function gpaAlertClass(gpa) {
    if (gpa >= 3.7) return 'alert-success';
    if (gpa >= 3.0) return 'alert-info';
    if (gpa >= 2.0) return 'alert-warning';
    return 'alert-danger';
  }

  function gpaProgressColor(gpa) {
    if (gpa >= 3.7) return 'bg-success';
    if (gpa >= 3.0) return 'bg-info';
    if (gpa >= 2.0) return 'bg-warning';
    return 'bg-danger';
  }

  function gpaBootstrapBadge(gpa) {
    if (gpa >= 3.7) return 'success';
    if (gpa >= 3.0) return 'info';
    if (gpa >= 2.0) return 'warning';
    return 'danger';
  }

  function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

});

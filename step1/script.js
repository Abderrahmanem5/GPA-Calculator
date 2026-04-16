function addCourse() {
  var first = document.querySelector('#courses .course-row');
  var row   = document.createElement('div');
  row.className = 'course-row';
  row.innerHTML =
    '<div class="field">' +
      '<label>Course Name</label>' +
      '<input type="text" name="course[]" placeholder="e.g. Mathematics" required>' +
    '</div>' +
    '<div class="field field--sm">' +
      '<label>Credits</label>' +
      '<input type="number" name="credits[]" placeholder="3" min="1" max="10" required>' +
    '</div>' +
    '<div class="field field--sm">' +
      '<label>Grade</label>' +
      '<select name="grade[]">' +
        '<option value="4.0">A</option>' +
        '<option value="3.0">B</option>' +
        '<option value="2.0">C</option>' +
        '<option value="1.0">D</option>' +
        '<option value="0.0">F</option>' +
      '</select>' +
    '</div>' +
    '<button type="button" class="btn--remove" onclick="this.closest(\'.course-row\').remove()" title="Remove course">✕</button>';
  document.getElementById('courses').appendChild(row);
}

function validateForm() {
  var courses = document.querySelectorAll('[name="course[]"]');
  var credits = document.querySelectorAll('[name="credits[]"]');

  for (var i = 0; i < courses.length; i++) {
    if (courses[i].value.trim() === '') {
      alert('All course name fields are required.');
      courses[i].focus();
      return false;
    }
  }
  for (var j = 0; j < credits.length; j++) {
    var v = parseFloat(credits[j].value);
    if (isNaN(v) || v <= 0) {
      alert('Credit hours must be positive numbers.');
      credits[j].focus();
      return false;
    }
  }
  return true;
}

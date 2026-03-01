function addCourse() {
    var row = document.createElement('div');
    row.className = 'course-row';
    row.innerHTML = `
        <input type="text" name="course[]" placeholder="Course Name" required>
        <input type="number" name="credits[]" placeholder="Credits" min="1" required>
        <select name="grade[]">
            <option value="4.0">A</option>
            <option value="3.0">B</option>
            <option value="2.0">C</option>
            <option value="1.0">D</option>
            <option value="0.0">F</option>
        </select>
        <button type="button" onclick="this.parentNode.remove()">Remove</button>
    `;
    document.getElementById('courses').appendChild(row);
}

function validateForm() {
    var credits = document.querySelectorAll('[name="credits[]"]');
    for (var i = 0; i < credits.length; i++) {
        if (credits[i].value <= 0 || isNaN(credits[i].value)) {
            alert("Credit hours must be positive numbers.");
            return false;
        }
    }
    return true;
}

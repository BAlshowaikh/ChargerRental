document.getElementById('edit-profile-btn').addEventListener('click', function () {
    const inputs = document.querySelectorAll('.editable');
    inputs.forEach(input => input.removeAttribute('readonly'));

    this.classList.add("editing");
    this.innerText = "Editing...";

    document.getElementById('save-btn').disabled = false;
});
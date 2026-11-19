document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('applicationModal');
    var form = modal && modal.querySelector('.application-form');
    var planInput = document.getElementById('application_plan');
    var planSummary = document.getElementById('application-selected-plan');
    var planName = document.getElementById('application-selected-plan-name');

    if (!modal || !form || !planInput) return;

    function clearApplicationForm() {
        form.reset();
        planInput.value = '';
        if (planName) planName.textContent = '';
        if (planSummary) planSummary.hidden = true;
        var requiredNote = document.getElementById('application-plan-required-note');
        if (requiredNote) requiredNote.hidden = false;
    }

    if (planInput.value && planSummary) planSummary.hidden = false;
    var initialRequiredNote = document.getElementById('application-plan-required-note');
    if (initialRequiredNote && planInput.value) initialRequiredNote.hidden = true;

    document.querySelectorAll('.plan-card button[data-plan-id]').forEach(function (button) {
        button.addEventListener('click', function () {
            planInput.value = button.dataset.planId || '';
            if (planName) planName.textContent = button.dataset.planName || '';
            if (planSummary) planSummary.hidden = !planInput.value;
            var requiredNote = document.getElementById('application-plan-required-note');
            if (requiredNote) requiredNote.hidden = !!planInput.value;
        });
    });

    form.addEventListener('submit', function (event) {
        if (!planInput.value) {
            event.preventDefault();
            window.alert('Please choose a plan using one of the plan cards before submitting.');
        }
    });

    modal.addEventListener('hidden.bs.modal', clearApplicationForm);
    modal.addEventListener('click', function (event) {
        if (event.target.closest('[data-bs-dismiss="modal"]')) {
            window.setTimeout(clearApplicationForm, 0);
        }
    });
});

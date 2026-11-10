document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const searchButton = document.getElementById("searchButton");
    const table = document.getElementById("applicantTable");

    if (searchInput && table) {
        function searchApplicants() {
            const searchValue = searchInput.value.toLowerCase().trim();
            const rows = table.querySelectorAll("tbody tr");

            rows.forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().includes(searchValue) ? "" : "none";
            });
        }

        searchInput.addEventListener("input", searchApplicants);
        if (searchButton) {
            searchButton.addEventListener("click", searchApplicants);
        }
    }

    document.querySelectorAll('form[action="../crud/update_applicants.php"]').forEach(function (form) {
        const statusInput = form.querySelector('[name="status"]');
        const remarksInput = form.querySelector('[name="remarks"]');
        const saveButton = form.querySelector('button[name="update_status"]');

        if (!statusInput || !remarksInput || !saveButton) {
            return;
        }

        const originalStatus = statusInput.value;
        const originalRemarks = remarksInput.value;

        function updateSaveButton() {
            const hasChanges = statusInput.value !== originalStatus || remarksInput.value !== originalRemarks;
            saveButton.disabled = !hasChanges;
        }

        statusInput.addEventListener("change", updateSaveButton);
        remarksInput.addEventListener("input", updateSaveButton);
        remarksInput.addEventListener("change", updateSaveButton);
        updateSaveButton();
    });
});

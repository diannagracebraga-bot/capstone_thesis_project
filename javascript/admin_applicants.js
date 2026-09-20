document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("searchInput");
    const searchButton = document.getElementById("searchButton");
    const table = document.getElementById("applicantTable");

    function searchApplicants() {

        const searchValue = searchInput.value.toLowerCase().trim();
        const rows = table.querySelectorAll("tbody tr");

        rows.forEach(function (row) {

            const rowText = row.textContent.toLowerCase();

            if (rowText.includes(searchValue)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }

        });
    }
    searchInput.addEventListener("keyup", searchApplicants);
    searchButton.addEventListener("click", searchApplicants);

});

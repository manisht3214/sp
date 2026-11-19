
document.addEventListener("DOMContentLoaded", function () {

    // Search sports on the dashboard
    const sportSearch = document.getElementById("sportSearch");

    if (sportSearch) {
        sportSearch.addEventListener("input", function () {
            const query = this.value.toLowerCase().trim();

            document.querySelectorAll(".sport-item").forEach(item => {
                const name = item.dataset.name || "";

                item.style.display = name.includes(query) ? "" : "none";
            });
        });
    }

    // Search records in category tables
    const tableSearch = document.getElementById("tableSearch");
    const statusFilter = document.getElementById("statusFilter");

    function filterRecords() {
        const query = tableSearch
            ? tableSearch.value.toLowerCase().trim()
            : "";

        const status = statusFilter
            ? statusFilter.value.toLowerCase()
            : "";

        document.querySelectorAll("#recordsTable tbody tr").forEach(row => {
            const text = row.textContent.toLowerCase();
            const rowStatus = row.dataset.status || "";

            const matchesSearch = text.includes(query);
            const matchesStatus = !status || rowStatus === status;

            row.style.display =
                matchesSearch && matchesStatus ? "" : "none";
        });
    }

    if (tableSearch) {
        tableSearch.addEventListener("input", filterRecords);
    }

    if (statusFilter) {
        statusFilter.addEventListener("change", filterRecords);
    }

});

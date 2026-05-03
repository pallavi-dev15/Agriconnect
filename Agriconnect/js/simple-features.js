(function () {
    "use strict";
    function setupBackToTop() {
        if (document.getElementById("js-back-to-top")) {
            return;
        }
        const button = document.createElement("button");
        button.type = "button";
        button.id = "js-back-to-top";
        button.textContent = "Top";
        button.setAttribute("aria-label", "Back to top");

        document.body.appendChild(button);
        function toggleButton() {
            if (window.scrollY > 220) {
                button.classList.add("show");
            } else {
                button.classList.remove("show");
            }
        }
        window.addEventListener("scroll", toggleButton);
        window.addEventListener("resize", toggleButton);
        button.addEventListener("click", function () {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
        toggleButton();
    }
    function setupMarketplaceFilter() {
        const searchInput = document.getElementById("market-search");
        const cards = document.querySelectorAll(".card-container .card[data-crop]");
        const emptyState = document.getElementById("market-empty-state");

        if (!searchInput || !cards.length) {
            return;
        }
        function applyFilter() {
            const term = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;

            cards.forEach(function (card) {
                const cropName = (card.getAttribute("data-crop") || "").toLowerCase();
                const matches = !term || cropName.indexOf(term) !== -1;
                card.hidden = !matches;

                if (matches) {
                    visibleCount += 1;
                }
            });
            if (emptyState) {
                emptyState.hidden = visibleCount !== 0;
            }
        }
        searchInput.addEventListener("input", applyFilter);
        applyFilter();
    }
    function setupContactCounter() {
        const messageField = document.getElementById("contact-message");
        const counter = document.getElementById("contact-message-counter");

        if (!messageField || !counter) {
            return;
        }

        const maxLength = Number(messageField.getAttribute("maxlength")) || 200;

        function updateCounter() {
            const remaining = Math.max(maxLength - messageField.value.length, 0);
            counter.textContent = remaining + " characters left";
            counter.classList.toggle("warning", remaining <= 30);
        }

        messageField.addEventListener("input", updateCounter);
        updateCounter();
    }
    setupBackToTop();
    setupMarketplaceFilter();
    setupContactCounter();
})();

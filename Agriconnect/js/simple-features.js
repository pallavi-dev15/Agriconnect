(function () {
    "use strict";

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
                const isMatch = !term || cropName.indexOf(term) !== -1;
                card.hidden = !isMatch;
                if (isMatch) {
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

    function setupContactValidation() {
        const form = document.getElementById("contact-form");
        if (!form) {
            return;
        }

        const nameField = document.getElementById("contact-name");
        const emailField = document.getElementById("contact-email");
        const messageField = document.getElementById("contact-message");
        const feedback = document.getElementById("contact-feedback");

        if (!nameField || !emailField || !messageField || !feedback) {
            return;
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        function setFieldError(field, message) {
            const errorBox = document.getElementById(field.id + "-error");
            field.classList.add("input-error");
            if (errorBox) {
                errorBox.textContent = message;
            }
        }

        function clearFieldError(field) {
            const errorBox = document.getElementById(field.id + "-error");
            field.classList.remove("input-error");
            if (errorBox) {
                errorBox.textContent = "";
            }
        }

        [nameField, emailField, messageField].forEach(function (field) {
            field.addEventListener("input", function () {
                clearFieldError(field);
                feedback.textContent = "";
                feedback.className = "contact-feedback";
            });
        });

        form.addEventListener("submit", function (event) {
            event.preventDefault();

            let isValid = true;
            clearFieldError(nameField);
            clearFieldError(emailField);
            clearFieldError(messageField);

            if (nameField.value.trim().length < 2) {
                setFieldError(nameField, "Please enter at least 2 characters.");
                isValid = false;
            }

            if (!emailPattern.test(emailField.value.trim())) {
                setFieldError(emailField, "Please enter a valid email address.");
                isValid = false;
            }

            if (messageField.value.trim().length < 10) {
                setFieldError(messageField, "Message should be at least 10 characters.");
                isValid = false;
            }

            if (!isValid) {
                feedback.textContent = "Please fix the errors and try again.";
                feedback.className = "contact-feedback error";
                return;
            }

            feedback.textContent = "Message sent successfully.";
            feedback.className = "contact-feedback success";
            form.reset();
        });
    }

    function setupFaqAccordion() {
        const items = document.querySelectorAll(".faq-item");
        if (!items.length) {
            return;
        }

        items.forEach(function (item) {
            const button = item.querySelector(".faq-question");
            const answer = item.querySelector(".faq-answer");
            if (!button || !answer) {
                return;
            }

            button.addEventListener("click", function () {
                const isOpen = item.classList.contains("is-open");

                items.forEach(function (otherItem) {
                    const otherButton = otherItem.querySelector(".faq-question");
                    const otherAnswer = otherItem.querySelector(".faq-answer");
                    otherItem.classList.remove("is-open");
                    if (otherButton) {
                        otherButton.setAttribute("aria-expanded", "false");
                    }
                    if (otherAnswer) {
                        otherAnswer.hidden = true;
                    }
                });

                if (!isOpen) {
                    item.classList.add("is-open");
                    button.setAttribute("aria-expanded", "true");
                    answer.hidden = false;
                }
            });
        });
    }

    setupMarketplaceFilter();
    setupContactValidation();
    setupFaqAccordion();
})();
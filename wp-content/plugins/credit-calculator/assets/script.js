document.addEventListener("DOMContentLoaded", function () {
    const productSelect = document.getElementById("cc-product");
    const amountInput = document.getElementById("cc-amount");
    const amountRange = document.getElementById("cc-amount-range");
    const amountMinLabel = document.getElementById("cc-amount-min");
    const amountMaxLabel = document.getElementById("cc-amount-max");

    const termRange = document.getElementById("cc-term");
    const termValue = document.getElementById("cc-term-value");
    const termMinLabel = document.getElementById("cc-term-min");
    const termMaxLabel = document.getElementById("cc-term-max");

    const monthlyEl = document.getElementById("monthly");

    const modal = document.getElementById("calc-modal");
    const openModalBtn = document.getElementById("open-modal");
    const closeModalBtn = document.querySelector(".cc-modal-close");
    const leadForm = document.getElementById("lead-form");

    function recalc() {
        const selected = productSelect.options[productSelect.selectedIndex];
        const rate = parseFloat(selected.dataset.rate) / 100 / 12;
        const amount = parseFloat(amountInput.value);
        const term = parseInt(termRange.value);

        const monthly = (amount * rate) / (1 - Math.pow(1 + rate, -term));
        monthlyEl.textContent = isNaN(monthly) ? 0 : monthly.toFixed(2);

        // обновляем поле срока правильно
        if (termValue) termValue.value = term;
    }


    function updateLimits() {
        const selected = productSelect.options[productSelect.selectedIndex];
        const min = parseInt(selected.dataset.min);
        const max = parseInt(selected.dataset.max);
        const termMin = parseInt(selected.dataset.termMin);
        const termMax = parseInt(selected.dataset.termMax);

        // сумма
        amountInput.min = min;
        amountInput.max = max;
        amountInput.value = min;
        amountRange.min = min;
        amountRange.max = max;
        amountRange.value = min;
        amountMinLabel.textContent = "от " + min;
        amountMaxLabel.textContent = "до " + max;

        // срок
        termRange.min = termMin;
        termRange.max = termMax;
        termRange.value = termMin;
        termValue.value = termMin; // ← исправлено на .value
        termMinLabel.textContent = "от " + termMin + " мес.";
        termMaxLabel.textContent = "до " + termMax + " мес.";

        recalc();
    }



    // события
    productSelect.addEventListener("change", updateLimits);

    amountInput.addEventListener("input", function () {
        amountRange.value = this.value;
        recalc();
    });

    amountRange.addEventListener("input", function () {
        amountInput.value = this.value;
        recalc();
    });

    termRange.addEventListener("input", recalc);

    // стартовые значения
    updateLimits();

    // модалка
    if (openModalBtn && modal) {
        openModalBtn.addEventListener("click", () => {
            // продукт
            const selectedProduct = productSelect.value;
            const leadProduct = document.getElementById("cc-lead-product");
            if (leadProduct) {
                leadProduct.value = selectedProduct;
            }

            // сумма
            const currentAmount = amountInput.value;
            const leadAmount = document.getElementById("cc-lead-amount");
            if (leadAmount) {
                leadAmount.value = currentAmount;
            }

            // срок
            const currentTerm = termRange.value;
            const leadTerm = document.getElementById("cc-lead-term");
            if (leadTerm) {
                leadTerm.value = currentTerm;
            }

            // показать модалку
            modal.style.display = "flex";
        });
    }


    if (closeModalBtn) {
        closeModalBtn.addEventListener("click", () => {
            modal.style.display = "none";
        });
    }
    window.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });

    // отправка формы
    if (leadForm) {
        leadForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(leadForm);
            formData.append("action", "save_lead");

            fetch(cc_ajax.url, {
                method: "POST",
                body: formData,
            })
                .then((res) => res.json())
                .then((r) => {
                    alert(r.data.message);
                    modal.style.display = "none";
                })
                .catch((err) => console.error(err));
        });
    }
});

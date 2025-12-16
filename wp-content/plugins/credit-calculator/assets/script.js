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

    /* ============================
       CALC LOGIC
    ============================ */

    function recalc() {
        if (!productSelect) return;

        const selected = productSelect.options[productSelect.selectedIndex];
        const rate = parseFloat(selected.dataset.rate) / 100 / 12;
        const amount = parseFloat(amountInput.value);
        const term = parseInt(termRange.value);

        const monthly = (amount * rate) / (1 - Math.pow(1 + rate, -term));
        monthlyEl.textContent = isNaN(monthly) ? 0 : monthly.toFixed(2);

        if (termValue) termValue.value = term;
    }

    function updateLimits() {
        if (!productSelect) return;

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
        amountMinLabel.textContent = cc_ajax.i18n.from + " " + min;
        amountMaxLabel.textContent = cc_ajax.i18n.to + " " + max;

        // срок
        termRange.min = termMin;
        termRange.max = termMax;
        termRange.value = termMin;
        termValue.value = termMin;
        termMinLabel.textContent = cc_ajax.i18n.from + " " + termMin + " " + cc_ajax.i18n.months;
        termMaxLabel.textContent = cc_ajax.i18n.to + " " + termMax + " " + cc_ajax.i18n.months;

        recalc();
    }

    if (productSelect) {
        productSelect.addEventListener("change", updateLimits);
        updateLimits();
    }

    if (amountInput && amountRange) {
        amountInput.addEventListener("input", () => {
            amountRange.value = amountInput.value;
            recalc();
        });

        amountRange.addEventListener("input", () => {
            amountInput.value = amountRange.value;
            recalc();
        });
    }

    if (termRange) {
        termRange.addEventListener("input", recalc);
    }

    /* ============================
       MODAL OPEN (UNIFIED)
    ============================ */

    function openCalcModal(productName = null) {
        if (!modal) return;

        // если клик из карточки — синхронизируем продукт
        if (productName && productSelect) {
            for (let opt of productSelect.options) {
                if (opt.value === productName) {
                    productSelect.value = productName;
                    productSelect.dispatchEvent(new Event("change"));
                    break;
                }
            }
        }

        // скрытые поля лида
        const leadProduct = document.getElementById("cc-lead-product");
        if (leadProduct) {
            leadProduct.value = productName || productSelect?.value || "";
        }

        const leadAmount = document.getElementById("cc-lead-amount");
        if (leadAmount && amountInput) {
            leadAmount.value = amountInput.value;
        }

        const leadTerm = document.getElementById("cc-lead-term");
        if (leadTerm && termRange) {
            leadTerm.value = termRange.value;
        }

        modal.style.display = "flex";
    }

    /* ============================
       OPEN FROM CALCULATOR
    ============================ */

    if (openModalBtn) {
        openModalBtn.addEventListener("click", function () {
            openCalcModal(productSelect?.value || null);
        });
    }

    /* ============================
       OPEN FROM PRODUCT CARDS
    ============================ */

    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".open-credit-modal");
        if (!btn) return;

        e.preventDefault();
        openCalcModal(btn.dataset.product || null);
    });

    /* ============================
       CLOSE MODAL
    ============================ */

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

    /* ============================
       SEND FORM
    ============================ */

    if (leadForm) {
        leadForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(leadForm);
            formData.append("action", "save_lead");

            fetch(cc_ajax.url, {
                method: "POST",
                body: formData,
            })
                .then(res => res.json())
                .then(r => {
                    alert(r.data.message);
                    modal.style.display = "none";
                })
                .catch(console.error);
        });
    }

});

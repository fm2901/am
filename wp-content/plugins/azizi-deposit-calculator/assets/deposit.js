(function(){
    function format(num){
        if (isNaN(num)) return '—';
        return new Intl.NumberFormat('ru-RU').format(Math.round(num));
    }

    function calcDeposit(amount, months, rate, cap){
        var balance = amount;
        var r_month = rate/100/12;
        var rows=[];
        if(cap==="none"){
            var profit = amount * (rate/100) * (months/12);
            var total = amount + profit;
            for(var i=1;i<=months;i++){
                var bal = amount + profit*(i/months);
                rows.push({i:i,bal:bal});
            }
            return {profit:profit,total:total,rows:rows};
        }
        if(cap==="monthly"){
            for(var i=1;i<=months;i++){
                balance *= (1+r_month);
                rows.push({i:i,bal:balance});
            }
            return {profit:balance-amount,total:balance,rows:rows};
        }
        if(cap==="yearly"){
            var r_year = rate/100;
            for(var i=1;i<=months;i++){
                if(i%12===0){ balance *= (1+r_year); }
                rows.push({i:i,bal:balance});
            }
            return {profit:balance-amount,total:balance,rows:rows};
        }
    }

    function initOne(wrapper){
        var amount=wrapper.querySelector('.js-dep-amount');
        var term=wrapper.querySelector('.js-dep-term');
        var rate=wrapper.querySelector('.js-dep-rate');
        var cap=wrapper.dataset.capitalization || 'monthly';

        var amountVal=wrapper.querySelector('.js-dep-amount-value');
        var termVal=wrapper.querySelector('.js-dep-term-value');
        var rateVal=wrapper.querySelector('.js-dep-rate-value');
        var profitEl=wrapper.querySelector('.js-dep-profit');
        var totalEl=wrapper.querySelector('.js-dep-total');
        var schedTable=wrapper.querySelector('.js-dep-schedule tbody');

        function render(){
            var A=parseFloat(amount.value);
            var n=parseInt(term.value,10);
            var r=parseFloat(rate.value);

            amountVal.textContent=format(A);
            termVal.textContent=n;
            rateVal.textContent=r.toFixed(1);

            var result=calcDeposit(A,n,r,cap);
            profitEl.textContent=format(result.profit);
            totalEl.textContent=format(result.total);

            schedTable.innerHTML='';
            result.rows.forEach(function(row){
                var tr=document.createElement('tr');
                tr.innerHTML='<td>'+row.i+'</td><td>'+format(row.bal)+'</td>';
                schedTable.appendChild(tr);
            });
        }

        [amount,term,rate].forEach(el=>el.addEventListener('input',render));
        render();
    }

    function initAll(){
        document.querySelectorAll('.azizi-deposit-wrapper').forEach(initOne);
    }

    if(document.readyState==='loading'){
        document.addEventListener('DOMContentLoaded',initAll);
    } else {
        initAll();
    }

    // === ДОРАБОТКА: модалка для лида ===
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("deposit-modal");
        const openBtn = document.getElementById("open-deposit-modal");
        if(!modal || !openBtn) return;

        const closeBtn = modal.querySelector(".cc-modal-close");
        const leadForm = document.getElementById("deposit-lead-form");

        // открыть и подставить сумму + срок
        openBtn.addEventListener("click", () => {
            const amountField = document.querySelector(".js-dep-amount");
            const termField = document.querySelector(".js-dep-term");

            const amount = amountField ? amountField.value : "";
            const term = termField ? termField.value : "";

            // hidden для отправки
            document.getElementById("lead-deposit-amount").value = amount;
            document.getElementById("lead-deposit-term").value = term;

            // отображение пользователю
            const amountDisplay = document.getElementById("lead-deposit-amount-display");
            const termDisplay = document.getElementById("lead-deposit-term-display");
            if(amountDisplay) amountDisplay.value = amount;
            if(termDisplay) termDisplay.value = term;

            modal.style.display = "flex";
        });

        // закрыть крестиком
        closeBtn.addEventListener("click", () => {
            modal.style.display = "none";
        });

        // закрыть по клику на фон
        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });

        // отправка формы
        leadForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(leadForm);

            // добавляем action вручную
            formData.append("action", "save_deposit_lead");

            fetch(cc_ajax.url, {
                method: "POST",
                body: formData,
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.data.message || "Спасибо! Ваша заявка отправлена.");
                        modal.style.display = "none";
                        leadForm.reset();
                    } else {
                        alert(data.data.message || "Ошибка при отправке заявки.");
                    }
                })
                .catch(() => {
                    alert("Ошибка соединения. Попробуйте позже.");
                });
        });
    });

})();

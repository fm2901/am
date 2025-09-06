(function(){
    function format(num){ if (isNaN(num)) return '—'; return new Intl.NumberFormat('ru-RU').format(Math.round(num)); }

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

    function initAll(){ document.querySelectorAll('.azizi-deposit-wrapper').forEach(initOne); }
    if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded',initAll);} else{initAll();}
})();

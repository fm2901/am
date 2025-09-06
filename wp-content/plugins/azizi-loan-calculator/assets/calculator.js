(function(){
  function format(num){ if (isNaN(num)) return '—'; return new Intl.NumberFormat('ru-RU').format(Math.round(num)); }
  function calcMonthly(P, months, annualRate){
    var r=(annualRate/100)/12;
    if(r===0) return P/months;
    var k=r*Math.pow(1+r,months)/(Math.pow(1+r,months)-1);
    return P*k;
  }
  function buildSchedule(P, n, rate){
    var r=(rate/100)/12, balance=P, monthly=calcMonthly(P,n,rate), rows=[];
    for(var i=1;i<=n;i++){
      var interest=balance*r;
      var principal=monthly-interest;
      balance-=principal;
      if(balance<0) balance=0;
      rows.push({i:i,pay:monthly,pr:principal,int:interest,bal:balance});
    }
    return rows;
  }

  function initOne(wrapper){
    var amountRange=wrapper.querySelector('.js-amount-range');
    var termRange=wrapper.querySelector('.js-term-range');
    var rateRange=wrapper.querySelector('.js-rate-range');
    var amountValue=wrapper.querySelector('.js-amount-value');
    var termValue=wrapper.querySelector('.js-term-value');
    var rateValue=wrapper.querySelector('.js-rate-value');
    var monthlyEl=wrapper.querySelector('.js-monthly');
    var overpayEl=wrapper.querySelector('.js-overpay');
    var totalEl=wrapper.querySelector('.js-total');
    var schedTable=wrapper.querySelector('.js-schedule-table tbody');

    function render(){
      var P=parseFloat(amountRange.value);
      var n=parseInt(termRange.value,10);
      var r=parseFloat(rateRange.value);

      var m=calcMonthly(P,n,r);
      var total=m*n;
      var overpay=total-P;

      amountValue.textContent=format(P);
      termValue.textContent=format(n);
      rateValue.textContent=r.toFixed(1);

      monthlyEl.textContent=format(m);
      overpayEl.textContent=format(overpay);
      totalEl.textContent=format(total);

      // schedule
      var rows=buildSchedule(P,n,r);
      schedTable.innerHTML='';
      rows.forEach(function(row){
        var tr=document.createElement('tr');
        tr.innerHTML='<td>'+row.i+'</td>'
            +'<td>'+format(row.pay)+'</td>'
            +'<td>'+format(row.bal)+'</td>';
        // Tooltip with details
        tr.title="Principal: "+format(row.pr)+" | Interest: "+format(row.int);
        schedTable.appendChild(tr);
      });
    }

    [amountRange,termRange,rateRange].forEach(function(el){ el.addEventListener('input',render); });
    render();
  }

  function initAll(){ document.querySelectorAll('.azizi-loan-wrapper').forEach(initOne); }
  if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded',initAll);} else{initAll();}
})();

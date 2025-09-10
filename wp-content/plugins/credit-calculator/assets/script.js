jQuery(document).ready(function($){
    function recalc() {
        let rate = $("#cc-product option:selected").data("rate") / 100 / 12;
        let amount = parseFloat($("#cc-amount").val());
        let term = parseInt($("#cc-term").val());
        let monthly = (amount * rate) / (1 - Math.pow(1 + rate, -term));
        $("#monthly").text(isNaN(monthly) ? 0 : monthly.toFixed(2));
        $("#cc-term-value").text(term);
    }

    function updateLimits() {
        let min = $("#cc-product option:selected").data("min");
        let max = $("#cc-product option:selected").data("max");
        let termMin = $("#cc-product option:selected").data("term-min");
        let termMax = $("#cc-product option:selected").data("term-max");

        $("#cc-amount").attr("min", min).attr("max", max).val(min);
        $("#cc-amount-range").attr("min", min).attr("max", max).val(min);

        $("#cc-term").attr("min", termMin).attr("max", termMax).val(termMin);
        $("#cc-term-value").text(termMin);

        recalc();
    }

    $("#cc-product").on("change", updateLimits);

    $("#cc-amount").on("input", function(){
        $("#cc-amount-range").val($(this).val());
        recalc();
    });
    $("#cc-amount-range").on("input", function(){
        $("#cc-amount").val($(this).val());
        recalc();
    });

    $("#cc-term").on("input", recalc);

    updateLimits();

    // Модалка
    $("#open-modal").on("click", ()=>$("#calc-modal").css("display","flex"));
    $(".cc-modal-close").on("click", ()=>$("#calc-modal").hide());
    $(window).on("click", function(e){
        if($(e.target).hasClass("cc-modal")) $("#calc-modal").hide();
    });

    // Отправка формы
    $("#lead-form").on("submit", function(e){
        e.preventDefault();
        $.post(cc_ajax.url, {
            action:"save_lead",
            name:$("input[name=name]").val(),
            phone:$("input[name=phone]").val(),
            city:$("input[name=city]").val()
        }, function(r){
            alert(r.data.message);
            $("#calc-modal").hide();
        });
    });
});

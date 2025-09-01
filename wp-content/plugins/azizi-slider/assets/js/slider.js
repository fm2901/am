/* Vanilla enhancements for ANT site (no frameworks) */
(function(){
    'use strict';

    function ready(fn){
        if(document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    }

    function qs(sel, ctx){ return (ctx||document).querySelector(sel); }
    function qsa(sel, ctx){ return Array.from((ctx||document).querySelectorAll(sel)); }

    // ======= Carousels (basic Swiper-like) =======
    function initCarousels(){
        qsa('.swiper').forEach(function(root){
            var wrapper = qs('.swiper-wrapper', root);
            var slides = qsa('.swiper-slide', wrapper);
            if(!wrapper || slides.length === 0){ return; }

            var current = 0;
            var looping = root.classList.contains('swiper-loop') || root.dataset.loop === 'true';
            var autoplayMs = parseInt(root.dataset.autoplay || '0', 10) || 0;
            var effect = (root.dataset.effect || (root.classList.contains('swiper-effect-fade') ? 'fade' : 'none')).toLowerCase();

            // Кнопки
            var prevBtn = qs('.swiper-button-prev', root);
            var nextBtn = qs('.swiper-button-next', root);

            function update(){
                slides.forEach(function(slide, i){
                    var active = (i === current);
                    slide.classList.toggle('is-active', active);
                    if(effect === 'fade'){
                        slide.setAttribute('aria-hidden', String(!active));
                    }
                });
                // Высота под активный слайд
                if(effect === 'fade'){
                    var h = slides[current] ? slides[current].offsetHeight : 0;
                    if(h){ wrapper.style.height = h + 'px'; }
                }
            }

            function go(n){
                if(n < 0) n = looping ? slides.length-1 : 0;
                if(n >= slides.length) n = looping ? 0 : slides.length-1;
                current = n;
                update();
            }

            // События
            if(prevBtn) prevBtn.addEventListener('click', function(){ go(current-1); });
            if(nextBtn) nextBtn.addEventListener('click', function(){ go(current+1); });

            // Touch (свайп)
            var startX = 0, startY = 0, swiping = false;
            root.addEventListener('touchstart', function(e){
                var t = e.touches[0];
                startX = t.clientX; startY = t.clientY; swiping = true;
            }, {passive:true});
            root.addEventListener('touchmove', function(e){
                if(!swiping) return;
                var t = e.touches[0];
                var dx = t.clientX - startX, dy = t.clientY - startY;
                if(Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 40){
                    e.preventDefault();
                    if(dx < 0) go(current+1); else go(current-1);
                    swiping = false;
                }
            }, {passive:false});
            root.addEventListener('touchend', function(){ swiping=false; });

            // Автоплей
            if(autoplayMs > 0){
                setInterval(function(){ go(current+1); }, autoplayMs);
            }

            // Инициализация
            update();
        });
    }

    ready(function(){
        initCarousels();
    });
})();

(function(){
    'use strict';
    function ready(fn){
        if(document.readyState!=='loading') fn();
        else document.addEventListener('DOMContentLoaded',fn);
    }
    function qs(s,c){return (c||document).querySelector(s);}
    function qsa(s,c){return Array.from((c||document).querySelectorAll(s));}

    function initAzpCarousel(){
        qsa('.azp-swiper').forEach(root=>{
            let wrapper = qs('.azp-wrapper', root);
            let slides = qsa('.azp-slide', wrapper);
            if(!slides.length) return;

            let autoplay = parseInt(root.dataset.autoplay||0, 10);

            function getPerView(){
                return window.innerWidth <= 640 ? 1 : 2;
            }

            let perView = getPerView();

            // клоны для бесконечного скролла
            let prepend = slides.slice(-perView).map(s=>s.cloneNode(true));
            let append  = slides.slice(0, perView).map(s=>s.cloneNode(true));
            prepend.forEach(cl=>wrapper.insertBefore(cl, wrapper.firstChild));
            append.forEach(cl=>wrapper.appendChild(cl));

            slides = qsa('.azp-slide', wrapper);
            let total = slides.length;

            let current = perView;
            let slideWidth = 100 / perView;

            function applySizes(){
                perView = getPerView();
                slideWidth = 100 / perView;
                slides.forEach(s=>{
                    s.style.flex = `0 0 ${slideWidth}%`;
                });
                wrapper.style.transform = `translateX(-${current * slideWidth}%)`;
            }

            wrapper.style.display = 'flex';
            wrapper.style.transition = 'transform 0.5s ease';
            applySizes();

            function go(n){
                current = n;
                wrapper.style.transition = 'transform 0.5s ease';
                wrapper.style.transform = `translateX(-${current * slideWidth}%)`;

                setTimeout(()=>{
                    if(current >= total - perView){
                        current = perView;
                        wrapper.style.transition = 'none';
                        wrapper.style.transform = `translateX(-${current * slideWidth}%)`;
                    }
                    if(current < perView){
                        current = total - perView*2;
                        wrapper.style.transition = 'none';
                        wrapper.style.transform = `translateX(-${current * slideWidth}%)`;
                    }
                }, 500);
            }

            // кнопки навигации
            let prevBtn = qs('.azp-prev',root);
            let nextBtn = qs('.azp-next',root);
            if(prevBtn) prevBtn.addEventListener('click',()=>go(current-1));
            if(nextBtn) nextBtn.addEventListener('click',()=>go(current+1));

            // автопрокрутка
            if(autoplay > 0){
                setInterval(()=>go(current+1), autoplay);
            }

            // ресайз
            window.addEventListener('resize', ()=>{
                applySizes();
            });
        });
    }

    ready(initAzpCarousel);
})();

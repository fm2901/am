const qs  = (s,p=document)=>p.querySelector(s);
const qsa = (s,p=document)=>[...p.querySelectorAll(s)];

function initSlider(){

    const root = qs('.azizi-slider');
    if(!root) return;

    const slider = qs('.swiper', root);
    const slides = qsa('.swiper-slide', slider);
    const pagBtns = qsa('.mts-pagination-button', root);

    const btnPrev = qs('.mts-prev', root);
    const btnNext = qs('.mts-next', root);

    const autoplayMs = parseInt(slider.dataset.autoplay) || 6000;

    root.style.setProperty('--count', slides.length);

    let current = 0;
    let timer = null;

    /* -----------------------------
       APPLY BACKGROUND
    ------------------------------*/
    function applyBG(slide){
        let bg = qs('.slide-bg', slide);
        let desk = slide.dataset.desktop;
        let mob  = slide.dataset.mobile;
        let url = (window.innerWidth <= 768 ? mob : desk);
        if(url) bg.style.backgroundImage = `url("${url}")`;
    }

    function applyAllBG() {
        slides.forEach(sl => applyBG(sl));
    }

    /* -----------------------------
       UPDATE ACTIVE/PREV/NEXT
    ------------------------------*/
    function updateSlides(){
        const len = slides.length;

        slides.forEach((sl,i)=>{
            sl.classList.remove("is-active","prev","next");

            if(i === current){
                sl.classList.add("is-active");
            } else if(i === (current - 1 + len) % len){
                sl.classList.add("prev");
            } else if(i === (current + 1) % len){
                sl.classList.add("next");
            }
        });
    }

    /* -----------------------------
       PAGINATION BARS
    ------------------------------*/
    function resetPagination(){
        pagBtns.forEach(btn=>{
            let bar = qs(".mts-pagination-progress", btn);
            bar.style.transitionDuration = "0ms";
            bar.style.width = "0%";
            void bar.offsetWidth;
        });
    }

    function runPagination(i){
        let bar = qs(".mts-pagination-progress", pagBtns[i]);
        bar.style.transitionDuration = autoplayMs + "ms";
        bar.style.width = "100%";
    }

    /* -----------------------------
       GO TO SLIDE
    ------------------------------*/
    function go(n){
        const len = slides.length;

        current = (n + len) % len;

        updateSlides();
        applyAllBG();

        resetPagination();
        runPagination(current);

        restartAutoplay();
    }

    /* -----------------------------
       AUTOPLAY
    ------------------------------*/
    function restartAutoplay(){
        if(timer) clearInterval(timer);
        timer = setInterval(()=>go(current + 1), autoplayMs);
    }

    /* -----------------------------
       BUTTON EVENTS
    ------------------------------*/
    pagBtns.forEach((btn,i)=> btn.onclick = ()=>go(i));

    btnPrev.onclick = ()=> go(current - 1);
    btnNext.onclick = ()=> go(current + 1);

    /* -----------------------------
       INIT
    ------------------------------*/
    slides.forEach(sl=>applyBG(sl));

    updateSlides();
    resetPagination();
    runPagination(0);
    restartAutoplay();

    setTimeout(()=>root.classList.add("loaded"), 200);
}

window.addEventListener("load", initSlider);

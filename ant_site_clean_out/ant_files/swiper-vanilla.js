/* Vanilla slider (no external libs). Works with `.swiper` markup. */
(function() {
  'use strict';

  function ready(fn){ if(document.readyState!=='loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }
  function qsa(sel, ctx){ return Array.from((ctx||document).querySelectorAll(sel)); }
  function qs(sel, ctx){ return (ctx||document).querySelector(sel); }

  function initVanillaSwiper(root, opts){
    opts = Object.assign({
      delay: 3000,
      pauseOnHover: true,
      disableOnInteraction: false
    }, opts||{});

    var wrapper = qs('.swiper-wrapper', root);
    if (!wrapper) return;
    var slides = qsa('.swiper-slide', wrapper);
    if (slides.length <= 1) return;

    // pagination bullets
    var pag = qs('.swiper-pagination', root);
    var bullets = [];

    // nav buttons
    var nextBtn = qs('.swiper-button-next', root) || qs('[data-next-button]', root);
    var prevBtn = qs('.swiper-button-prev', root) || qs('[data-prev-button]', root);

    // initial display
    slides.forEach(function(s, i){
      s.style.display = (i === 0) ? '' : 'none';
      s.classList.toggle('is-active', i === 0);
    });
    var current = 0;

    function renderBullets(){
      if (!pag) return;
      pag.innerHTML = '';
      bullets = slides.map(function(_, i){
        var b = document.createElement('span');
        b.className = 'swiper-pagination-bullet' + (i===current ? ' swiper-pagination-bullet-active' : '');
        b.setAttribute('role','button');
        b.setAttribute('aria-label','Go to slide ' + (i+1));
        b.addEventListener('click', function(){
          go(i, true);
        });
        pag.appendChild(b);
        return b;
      });
    }

    function update(){
      slides.forEach(function(s, i){
        var active = (i === current);
        s.style.display = active ? '' : 'none';
        s.classList.toggle('is-active', active);
      });
      if (bullets.length){
        bullets.forEach(function(b, i){
          b.classList.toggle('swiper-pagination-bullet-active', i===current);
        });
      }
    }

    function go(n, byUser){
      if (n < 0) n = slides.length - 1;
      if (n >= slides.length) n = 0;
      current = n;
      update();
      if (byUser && opts.disableOnInteraction) stop();
    }

    function next(){ go(current + 1, false); }
    function prev(){ go(current - 1, false); }

    var timer = null;
    function start(){
      stop();
      if (opts.delay > 0){
        timer = setInterval(next, opts.delay);
      }
    }
    function stop(){
      if (timer){ clearInterval(timer); timer = null; }
    }

    // Controls
    if (nextBtn) nextBtn.addEventListener('click', function(e){ e.preventDefault(); go(current+1, true); if(!opts.disableOnInteraction) start(); });
    if (prevBtn) prevBtn.addEventListener('click', function(e){ e.preventDefault(); go(current-1, true); if(!opts.disableOnInteraction) start(); });

    // Hover pause
    if (opts.pauseOnHover){
      root.addEventListener('mouseenter', stop);
      root.addEventListener('mouseleave', start);
    }

    // Visibility pause (when user switches tab)
    document.addEventListener('visibilitychange', function(){
      if (document.hidden) stop(); else start();
    });

    renderBullets();
    start();

    // expose small API
    root.__vanillaSwiper__ = { next: next, prev: prev, go: go, stop: stop, start: start };
  }

  ready(function(){
    qsa('.swiper').forEach(function(root){
      initVanillaSwiper(root, {
        delay: 3000,
        pauseOnHover: true,
        disableOnInteraction: false
      });
    });
  });
})();
/* Vanilla enhancements for ANT site (no frameworks) */
(function(){
  'use strict';

  function ready(fn){ if(document.readyState !== 'loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }

  function qs(sel, ctx){ return (ctx||document).querySelector(sel); }
  function qsa(sel, ctx){ return Array.from((ctx||document).querySelectorAll(sel)); }

  // ======= Carousels (basic Swiper-like) =======
  function initCarousels(){
    qsa('.swiper').forEach(function(root, idx){
      // expected structure: .swiper > .swiper-wrapper > .swiper-slide*
      var wrapper = qs('.swiper-wrapper', root);
      var slides = qsa('.swiper-slide', wrapper);
      if(!wrapper || slides.length === 0){ return; }

      var current = 0;
      var looping = root.classList.contains('swiper-loop') || root.dataset.loop === 'true';
      var autoplayMs = parseInt(root.dataset.autoplay || '0', 10) || 0;

      // Strip inline transforms left from previous init
      wrapper.style.transform = '';
      slides.forEach(function(s){ s.style.transform=''; });

      // Create controls if not provided
      var prevBtn = qs('.swiper-button-prev', root);
      var nextBtn = qs('.swiper-button-next', root);
      if(!prevBtn){
        prevBtn = document.createElement('button');
        prevBtn.className = 'swiper-button-prev';
        prevBtn.setAttribute('aria-label','Previous');
        prevBtn.textContent = '‹';
        root.appendChild(prevBtn);
      }
      if(!nextBtn){
        nextBtn = document.createElement('button');
        nextBtn.className = 'swiper-button-next';
        nextBtn.setAttribute('aria-label','Next');
        nextBtn.textContent = '›';
        root.appendChild(nextBtn);
      }

      function update(){
        slides.forEach(function(slide, i){
          slide.classList.toggle('is-active', i === current);
          slide.style.display = (i === current) ? '' : 'none';
        });
      }
      function go(n){
        if(n < 0) n = looping ? slides.length-1 : 0;
        if(n >= slides.length) n = looping ? 0 : slides.length-1;
        current = n;
        update();
      }

      prevBtn.addEventListener('click', function(){ go(current-1); });
      nextBtn.addEventListener('click', function(){ go(current+1); });

      // Swipe support
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

      // Autoplay
      if(autoplayMs > 0){
        setInterval(function(){ go(current+1); }, autoplayMs);
      }

      update();
    });
  }

  // ======= Header menu toggles (heuristic) =======
  function initMenus(){
    // Toggle any element with data-toggle targeting [data-menu]
    qsa('[data-toggle]').forEach(function(btn){
      var targetSel = btn.getAttribute('data-toggle');
      var target = qs(targetSel);
      if(!target) return;
      btn.addEventListener('click', function(e){
        e.preventDefault();
        var expanded = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', String(!expanded));
        target.classList.toggle('is-open', !expanded);
      });
    });

    // Dropdowns: elements with [data-dropdown]
    qsa('[data-dropdown]').forEach(function(drop){
      var trigger = qs('[data-trigger]', drop) || drop;
      var menu = qs('[data-menu]', drop);
      if(!menu) return;
      function close(){ menu.classList.remove('is-open'); trigger.setAttribute('aria-expanded','false'); }
      trigger.addEventListener('click', function(e){
        e.preventDefault();
        var open = menu.classList.toggle('is-open');
        trigger.setAttribute('aria-expanded', String(open));
      });
      document.addEventListener('click', function(e){
        if(!drop.contains(e.target)) close();
      });
      document.addEventListener('keydown', function(e){ if(e.key === 'Escape') close(); });
    });
  }

  // ======= Smooth scroll for same-page anchors =======
  function initSmoothScroll(){
    qsa('a[href^="#"]').forEach(function(a){
      a.addEventListener('click', function(e){
        var id = a.getAttribute('href').slice(1);
        var el = document.getElementById(id);
        if(!el) return;
        e.preventDefault();
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    });
  }

  // ======= Simple SPA guard: force full navigation for React-router links =======
  function disableClientRouter(){
    qsa('a[data-discover="true"]').forEach(function(a){
      // remove client router handler attribute if present
      a.removeAttribute('data-discover');
    });
  }

  // ======= Form enhancements (no frameworks) =======
  function initForms(){
    qsa('form').forEach(function(form){
      form.addEventListener('submit', function(e){
        // If action is "#" or empty, prevent default to avoid navigating away
        var action = (form.getAttribute('action')||'').trim();
        if(action === '' || action === '#'){
          e.preventDefault();
          // basic validation hint
          qsa('[required]', form).forEach(function(inp){
            if(!inp.value){ inp.classList.add('is-invalid'); }
            else { inp.classList.remove('is-invalid'); }
          });
        }
      });
    });
  }

  ready(function(){
    disableClientRouter();
    initCarousels();
    initMenus();
    initSmoothScroll();
    initForms();
    // expose minimal API if needed
    window.ANT = {
      nextCarousel: function(sel){ var root = qs(sel); if(!root) return; var slides=qsa('.swiper-slide', root); var curr=slides.findIndex(s=>s.classList.contains('is-active')); var next=(curr+1)%slides.length; slides[curr]?.classList.remove('is-active'); slides[curr].style.display='none'; slides[next].classList.add('is-active'); slides[next].style.display=''; }
    };
  });
})();
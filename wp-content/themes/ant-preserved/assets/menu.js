/* Hover dropdowns + active highlight & arrow rotation (vanilla) */
(function(){
  'use strict';
  function ready(fn){ if(document.readyState!=='loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }
  function qsa(sel, ctx){ return Array.from((ctx||document).querySelectorAll(sel)); }

  function openWrap(wrap){ if(!wrap) return;
    wrap.style.opacity='1'; wrap.style.pointerEvents='auto'; wrap.style.transform='translateY(0)'; wrap.style.height='auto';
  }
  function closeWrap(wrap){ if(!wrap) return;
    wrap.style.opacity='0'; wrap.style.pointerEvents='none'; wrap.style.transform='translateY(-10px)'; wrap.style.height='0px';
  }

  function initHoverMenus(){
    qsa('nav [class*="_menu_"] > li').forEach(function(li){
      var wrap = li.querySelector('._submenuWrapper_1md32_24');
      if(!wrap) return;
      var a = li.querySelector('a,button');
      // caret: последний SVG внутри ссылки
      var caret = a ? a.querySelector('svg:last-of-type') : null;
      var closeTimer = null;

      function setCaret(open){
        if(!caret) return;
        caret.style.transition = 'transform 180ms ease';
        caret.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
        caret.style.transformOrigin = '50% 50%';
      }
      function open(){
        if(closeTimer){ clearTimeout(closeTimer); closeTimer=null; }
        li.classList.add('is-open');
        if(a){ a.classList.add('is-hover'); a.setAttribute('aria-expanded','true'); }
        setCaret(true);
        openWrap(wrap);
      }
      function scheduleClose(){
        if(closeTimer){ clearTimeout(closeTimer); }
        closeTimer = setTimeout(function(){
          li.classList.remove('is-open');
          if(a){ a.classList.remove('is-hover'); a.setAttribute('aria-expanded','false'); }
          setCaret(false);
          closeWrap(wrap);
        }, 120);
      }

      li.addEventListener('mouseenter', open);
      li.addEventListener('mouseleave', scheduleClose);
      if(a){
        a.addEventListener('focus', open);
        a.addEventListener('blur', scheduleClose);
        a.addEventListener('click', function(e){
          if(wrap && getComputedStyle(wrap).opacity!=='1'){
            e.preventDefault();
            open();
          }
        });
      }
      wrap.addEventListener('mouseenter', function(){ if(closeTimer){ clearTimeout(closeTimer); closeTimer=null; } });
      wrap.addEventListener('mouseleave', scheduleClose);
      // init closed
      setCaret(false); closeWrap(wrap);
    });
  }

  ready(initHoverMenus);
})();
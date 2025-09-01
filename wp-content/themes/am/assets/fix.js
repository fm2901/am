
(function(){
  function enhanceMenus(){
    var menuItems = document.querySelectorAll('._navigation_1md32_86 li, ._menu_17w3r_114 li');
    menuItems.forEach(function(li){
      var submenu = li.querySelector('._submenuWrapper_1md32_24, ul, div');
      if(submenu && !li.__hoverBound){
        li.__hoverBound = true;
        li.addEventListener('mouseenter', function(){
          submenu.style.display = 'block';
          submenu.style.opacity = '1';
          submenu.style.visibility = 'visible';
          submenu.style.pointerEvents = 'auto';
          submenu.style.height = 'auto';
          var toggle = li.querySelector('a, button');
          if(toggle) toggle.setAttribute('aria-expanded', 'true');
        });
        li.addEventListener('mouseleave', function(){
          submenu.style.display = '';
          submenu.style.opacity = '';
          submenu.style.visibility = '';
          submenu.style.pointerEvents = '';
          submenu.style.height = '';
          var toggle = li.querySelector('a, button');
          if(toggle) toggle.setAttribute('aria-expanded', 'false');
        });
      }
    });
  }
  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', enhanceMenus);
  } else {
    enhanceMenus();
  }
})();

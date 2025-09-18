document.addEventListener('DOMContentLoaded', function(){
    // Переключение категорий
    document.querySelectorAll('.documents-categories li').forEach(function(item){
        item.addEventListener('click', function(){
            document.querySelectorAll('.documents-categories li').forEach(el => el.classList.remove('active-cat'));
            document.querySelectorAll('.documents-categories .category-indicator').forEach(el => el.remove());
            this.classList.add('active-cat');
            let indicator = document.createElement('span');
            indicator.classList.add('category-indicator');
            this.appendChild(indicator);
            document.querySelectorAll('.documents-cat-block').forEach(el => el.classList.remove('active-block'));
            let catId = this.getAttribute('data-cat');
            document.getElementById(catId).classList.add('active-block');
        });
    });

    // Аккордеон архива со стрелкой
    document.querySelectorAll('.archive-accordion-toggle').forEach(btn => {
        btn.addEventListener('click', function(){
            let content = this.nextElementSibling;
            let arrow = this.querySelector('.arrow');
            this.classList.toggle('active');
            if (content.classList.contains('open')) {
                content.style.maxHeight = null;
                content.classList.remove('open');
                arrow.classList.remove('rotated');
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
                content.classList.add('open');
                arrow.classList.add('rotated');
            }
        });
    });
});

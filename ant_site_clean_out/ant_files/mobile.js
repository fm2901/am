document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.querySelector(".menu-toggle"); // кнопка "гамбургер"
    const nav = document.querySelector(".main-nav"); // твой блок меню

    if (toggleBtn && nav) {
        toggleBtn.addEventListener("click", () => {
            nav.classList.toggle("open");
        });

        // если кликаем по ссылке в меню → скрываем
        nav.querySelectorAll("a").forEach(link => {
            link.addEventListener("click", () => {
                nav.classList.remove("open");
            });
        });
    }
});

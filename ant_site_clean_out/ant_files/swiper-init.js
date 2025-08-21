document.addEventListener("DOMContentLoaded", () => {
    new Swiper(".swiper", {
        slidesPerView: 1,
        slidesPerGroup: 1,
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        parallax: true,
    });
});

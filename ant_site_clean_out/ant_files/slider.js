document.addEventListener("DOMContentLoaded", () => {
  if (typeof Swiper === "undefined") {
    console.error("Swiper not loaded");
    return;
  }
  const swiper = new Swiper("._swiper_n3568_5", {
    slidesPerView: 1,
    slidesPerGroup: 1,
    loop: true,
    speed: 600,
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
    observeParents: true,
    observer: true
  });
  console.log("Swiper init OK", swiper);
});
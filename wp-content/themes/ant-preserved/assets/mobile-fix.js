document.addEventListener("DOMContentLoaded", function() {
  const burger = document.querySelector(".burger");
  const nav = document.querySelector(".navigation");

  if (burger && nav) {
    burger.addEventListener("click", () => {
      burger.classList.toggle("active");
      nav.classList.toggle("open");
    });
  }
});

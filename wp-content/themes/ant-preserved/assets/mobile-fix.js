document.addEventListener("DOMContentLoaded", function() {
  const burger = document.querySelector(".burger");
  const body = document.querySelector("body");
  const nav = document.querySelector(".navigation");

  if (burger && nav) {
    burger.addEventListener("click", () => {
      burger.classList.toggle("active");
      nav.classList.toggle("open");
      body.classList.toggle("open");
    });
  }

});

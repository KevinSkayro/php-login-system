const burgerMenuBtn = document.querySelector("[data-burger-menu]");
const navLinks = document.querySelector("[data-nav-links]");
burgerMenuBtn.addEventListener("click", () => {
  burgerMenuBtn.classList.toggle("active");
  navLinks.classList.toggle("active");
});

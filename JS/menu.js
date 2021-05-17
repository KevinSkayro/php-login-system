const burgerMenuBtn = document.querySelector(".burger_menu");
const navLinks = document.querySelector(".nav_links");
const lineOne = document.querySelector(".line_one");
const lineTwo = document.querySelector(".line_two");
const lineThree = document.querySelector(".line_three");
burgerMenuBtn.addEventListener("click", () => {
  burgerMenuBtn.classList.toggle("active");
  navLinks.classList.toggle("active");
});

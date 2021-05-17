//Selectors
const openRegistrationBtn = document.querySelector(".open_registration");
const closeRegistrationBtn = document.querySelector(".close_registration");
const registrationContainer = document.querySelector(
  ".main_registration_container"
);
const loginMsg = document.querySelector(".login_msg");

const form = document.querySelector(".registration_form");

//Event listeners

openRegistrationBtn.addEventListener("click", () => {
  registrationContainer.classList.add("active");
  loginMsg.classList.remove("msg");
});
closeRegistrationBtn.addEventListener("click", () => {
  registrationContainer.classList.remove("active");
  loginMsg.classList.add("msg");
});

//Functions
form.onsubmit = function (event) {
  event.preventDefault();
  var form_data = new FormData(form);
  var xhr = new XMLHttpRequest();
  xhr.open("POST", form.action, true);
  xhr.onload = function () {
    document.querySelector(".msg").innerHTML = this.responseText;
  };
  xhr.send(form_data);
};

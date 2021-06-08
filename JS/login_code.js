//Selectors
const openRegistrationBtn = document.querySelector("[data-open-reg-btn]");
const closeRegistrationBtn = document.querySelector("[data-close-reg-btn]");
const registrationContainer = document.querySelector("[data-reg-container]");
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

//Selectors
const loginMsg = document.querySelector(".login_msg");

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

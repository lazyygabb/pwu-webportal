
const container = document.getElementById("container");
const registerBtn = document.getElementById("login-admin");
const loginBtn = document.getElementById("login-student");

registerBtn.addEventListener("click", () => {
  container.classList.add("active");
});

loginBtn.addEventListener("click", () => {
  container.classList.remove("active");
});

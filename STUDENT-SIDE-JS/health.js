const toggleButton = document.getElementById('toggle-btn')
const sidebar = document.getElementById('sidebar')

function toggleSidebar(){
  sidebar.classList.toggle('close')
  toggleButton.classList.toggle('rotate')

  closeAllSubMenus()
}

function toggleSubMenu(button){

  if(!button.nextElementSibling.classList.contains('show')){
    closeAllSubMenus()
  }

  button.nextElementSibling.classList.toggle('show')
  button.classList.toggle('rotate')

  if(sidebar.classList.contains('close')){
    sidebar.classList.toggle('close')
    toggleButton.classList.toggle('rotate')
  }
}

function closeAllSubMenus(){
  Array.from(sidebar.getElementsByClassName('show')).forEach(ul => {
    ul.classList.remove('show')
    ul.previousElementSibling.classList.remove('rotate')
  })
}


var btn_about = document.getElementById("btn_about");
var btn_emergency_hotlines = document.getElementById("btn_emergency_hotlines");
var btn_health_announcements = document.getElementById("btn_health_announcements");
var btn_medical_clearances = document.getElementById("btn_medical_clearances");
var btn_mental_health_resources = document.getElementById("btn_mental_health_resources");
 
var about = document.getElementById("about");
var emergency_hotlines = document.getElementById("emergency_hotlines");
var health_announcements = document.getElementById("health_announcements");
var medical_clearances = document.getElementById("medical_clearances");
var mental_health_resources = document.getElementById("mental_health_resources");

btn_about.addEventListener('click', () => {
  about.style.display = "block";
  emergency_hotlines.style.display = "none";
  health_announcements.style.display = "none";
  medical_clearances.style.display = "none";
  mental_health_resources.style.display = "none";
});

btn_emergency_hotlines.addEventListener('click', () => {
  about.style.display = "none";
  emergency_hotlines.style.display = "block";
  health_announcements.style.display = "none";
  medical_clearances.style.display = "none";
  mental_health_resources.style.display = "none";
});

btn_health_announcements.addEventListener('click', () => {
  about.style.display = "none";
  emergency_hotlines.style.display = "none";
  health_announcements.style.display = "block";
  medical_clearances.style.display = "none";
  mental_health_resources.style.display = "none";
});

btn_medical_clearances.addEventListener('click', () => {
  about.style.display = "none";
  emergency_hotlines.style.display = "none";
  health_announcements.style.display = "none";
  medical_clearances.style.display = "block";
  mental_health_resources.style.display = "none";
});

btn_mental_health_resources.addEventListener('click', () => {
  about.style.display = "none";
  emergency_hotlines.style.display = "none";
  health_announcements.style.display = "none";
  medical_clearances.style.display = "none";
  mental_health_resources.style.display = "block";
});
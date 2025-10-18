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

function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  sidebar.classList.toggle("close"); 
}

ocument.addEventListener("DOMContentLoaded", function () {
  const termSelect = document.getElementById("termSelect");
  const yearSelect = document.getElementById("yearSelect");
  const tables = document.querySelectorAll(".table-box table[data-course]");
  const wrapper = document.querySelector(".tables-wrapper");

  function filterTables() {
    let firstVisible = false;

    tables.forEach(table => {
      const tableTerm = table.getAttribute("data-term");
      const tableYear = table.getAttribute("data-year");

      const matchesTerm = termSelect.value === "all" || termSelect.value === tableTerm;
      const matchesYear = yearSelect.value === "all" || yearSelect.value === tableYear;

      table.parentElement.style.display = matchesTerm && matchesYear ? "block" : "none";

      if (!firstVisible && matchesTerm && matchesYear) firstVisible = true;
    });

    
    if (firstVisible) wrapper.scrollTop = 0;
  }

  termSelect.addEventListener("change", filterTables);
  yearSelect.addEventListener("change", filterTables);

 
  filterTables();
});
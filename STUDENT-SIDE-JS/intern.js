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

document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.info-toggle');

    toggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();

            const box = this.closest('.info-box');

            // close other boxes
            document.querySelectorAll('.info-box').forEach(otherBox => {
                if (otherBox !== box) {
                    otherBox.classList.remove('active');
                }
            });

            // toggle current box
            box.classList.toggle('active');
        });
    });
});

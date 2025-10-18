const toggleButton = document.getElementById('toggle-btn');
const sidebar = document.getElementById('sidebar');

function toggleSidebar() {
  sidebar.classList.toggle('close');
  toggleButton.classList.toggle('rotate');
  closeAllSubMenus();
}

function toggleSubMenu(button) {
  if (!button.nextElementSibling.classList.contains('show')) {
    closeAllSubMenus();
  }

  button.nextElementSibling.classList.toggle('show');
  button.classList.toggle('rotate');

  if (sidebar.classList.contains('close')) {
    sidebar.classList.toggle('close');
    toggleButton.classList.toggle('rotate');
  }
}

function closeAllSubMenus() {
  Array.from(sidebar.getElementsByClassName('show')).forEach(ul => {
    ul.classList.remove('show');
    ul.previousElementSibling.classList.remove('rotate');
  });
}

// ===== FAQ Toggle =====
function toggleAnswer(element) {
  const question = element;
  const answer = question.nextElementSibling;
  const toggle = question.querySelector('.faq-toggle');

  if (answer.classList.contains('active')) {
    // Close current
    answer.classList.remove('active');
    toggle.textContent = '+';
    toggle.classList.remove('minus');
  } else {
    // Close other open answers
    const allAnswers = document.querySelectorAll('.faq-answer.active');
    allAnswers.forEach(activeAnswer => {
      if (activeAnswer !== answer) {
        activeAnswer.classList.remove('active');
        const parentQuestion = activeAnswer.previousElementSibling;
        const parentToggle = parentQuestion.querySelector('.faq-toggle');
        parentToggle.textContent = '+';
        parentToggle.classList.remove('minus');
      }
    });

    // Open selected answer
    answer.classList.add('active');
    toggle.textContent = '−'; // Unicode minus sign for better appearance
    toggle.classList.add('minus');
  }
}

document.addEventListener('DOMContentLoaded', () => {
    var btn_faq = document.getElementById("btn_faq");
    var btn_feedback = document.getElementById("btn_feedback");

    var faq_section = document.getElementById("faq_section");
    var feedback_section = document.getElementById("feedback_section");

    btn_faq.addEventListener('click', () => {
        faq_section.style.display = "block";
        feedback_section.style.display = "none";
    });

    btn_feedback.addEventListener('click', () => {
        faq_section.style.display = "none";
        feedback_section.style.display = "block";
    });
});

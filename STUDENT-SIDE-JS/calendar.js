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

const monthYear = document.getElementById("monthYear");
const calendarDays = document.getElementById("calendarDays");
const prevMonthBtn = document.getElementById("prevMonth");
const nextMonthBtn = document.getElementById("nextMonth");

const months = [
  "January", "February", "March", "April", "May", "June",
  "July", "August", "September", "October", "November", "December"
];

let date = new Date();
let currentMonth = date.getMonth();
let currentYear = date.getFullYear();

// Fixed events for the year
const events = [
  { day: 30, month: 6, year: currentYear, title: "PRELIM Exam Week", type: "reminder" }, 
  { day: 21, month: 7, year: currentYear, title: "Ninoy Aquino", type: "holiday" },
  { day: 22, month: 7, year: currentYear, title: "Acquaintance Day", type: "meeting" },
  { day: 25, month: 7, year: currentYear, title: "National Heroes Day", type: "holiday" },
  { day: 30, month: 7, year: currentYear, title: "Buwan ng Wika", type: "reminder"},
  { day: 1, month: 8, year: currentYear, title: "Midterm Exam Week", type: "reminder" },
  { day: 8, month: 8, year: currentYear, title: "Sport Fest", type: "meeting" },
  { day: 1, month: 10, year: currentYear, title: "All Saint's Day", type: "holiday" },
  { day: 30, month: 10, year: currentYear, title: "Bonifacio Day", type: "holiday" },
  { day: 25, month: 11, year: currentYear, title: "Christmas", type: "holiday" },
  { day: 30, month: 11, year: currentYear, title: "Rizal Day", type: "holiday" },
  { day: 31, month: 11, year: currentYear, title: "New Year's Eve", type: "holiday" },
];

function renderCalendar(month, year) {
  calendarDays.innerHTML = "";
  monthYear.innerText = `${months[month]} ${year}`;

  const firstDay = new Date(year, month, 1).getDay();
  const lastDate = new Date(year, month + 1, 0).getDate();

  const dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
  dayNames.forEach(d => {
    const div = document.createElement("div");
    div.classList.add("day-name");
    div.innerText = d;
    calendarDays.appendChild(div);
  });

  for (let i = 0; i < firstDay; i++) {
    const empty = document.createElement("div");
    empty.classList.add("day", "other-month");
    calendarDays.appendChild(empty);
  }

  for (let d = 1; d <= lastDate; d++) {
    const dayElement = document.createElement("div");
    dayElement.classList.add("day", "current-month");
    
    const dayNumber = document.createElement("div");
    dayNumber.classList.add("day-number");
    dayNumber.innerText = d;
    dayElement.appendChild(dayNumber);
    
    const today = new Date();
    if (d === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
      dayElement.classList.add("today");
    }
    
    const dayEvents = events.filter(event => 
      event.day === d && event.month === month && event.year === year
    );
    
    if (dayEvents.length > 0) {
      dayElement.classList.add("has-event");
      
      dayEvents.slice(0, 2).forEach(event => {
        const eventIndicator = document.createElement("div");
        eventIndicator.classList.add("event-indicator", event.type);
        eventIndicator.innerText = event.title;
        dayElement.appendChild(eventIndicator);
      });
      
      if (dayEvents.length > 2) {
        const moreEvents = document.createElement("div");
        moreEvents.classList.add("more-indicator");
        moreEvents.innerText = `+${dayEvents.length - 2} more`;
        dayElement.appendChild(moreEvents);
      }
    }
    
    calendarDays.appendChild(dayElement);
  }

  const totalCells = 42;
  const existingCells = firstDay + lastDate;
  const emptyCellsEnd = totalCells - existingCells;
  
  for (let i = 0; i < emptyCellsEnd; i++) {
    const empty = document.createElement("div");
    empty.classList.add("day", "other-month");
    calendarDays.appendChild(empty);
  }

  updateEventList(month, year);
}

function updateEventList(month, year) {
  const eventList = document.querySelector(".event-list");
  eventList.innerHTML = "";

  const monthEvents = events.filter(e => e.month === month && e.year === year);
  monthEvents.sort((a, b) => a.day - b.day);

  const displayEvents = monthEvents.slice(0, 4);

  if (displayEvents.length === 0) {
    eventList.innerHTML = "<li>No events this month</li>";
    return;
  }

  displayEvents.forEach(e => {
    const li = document.createElement("li");
    li.classList.add("event-item");
    li.innerHTML = `
      <div class="event-time">${months[e.month]} ${e.day}, ${e.year}</div>
      <div class="event-title">${e.title}</div>
      <div class="event-description">${e.type}</div>
    `;
    eventList.appendChild(li);
  });
}

function prevMonth() {
  currentMonth--;
  if (currentMonth < 0) {
    currentMonth = 11;
    currentYear--;
  }
  renderCalendar(currentMonth, currentYear);
}

function nextMonth() {
  currentMonth++;
  if (currentMonth > 11) {
    currentMonth = 0;
    currentYear++;
  }
  renderCalendar(currentMonth, currentYear);
}

// Event listeners
prevMonthBtn.addEventListener("click", prevMonth);
nextMonthBtn.addEventListener("click", nextMonth);

// Initialize calendar
renderCalendar(currentMonth, currentYear);
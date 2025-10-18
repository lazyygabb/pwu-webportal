const sidebar = document.getElementById('sidebar');

    function toggleSidebar() {
      sidebar.classList.toggle('close');
    }

    document.getElementById('concernForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const msg = document.getElementById('successMessage');
      msg.style.display = 'block';
      this.reset();
      setTimeout(() => msg.style.display = 'none', 4000);
    });

    document.querySelectorAll('#sidebar .dropdown-btn').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const next = btn.nextElementSibling;
        if(next && !next.classList.contains('show')){
          document.querySelectorAll('#sidebar .sub-menu.show').forEach(s=>s.classList.remove('show'));
          document.querySelectorAll('#sidebar .dropdown-btn.rotate').forEach(r=>r.classList.remove('rotate'));
        }
        if(next) next.classList.toggle('show');
        btn.classList.toggle('rotate');
        if(sidebar.classList.contains('close')) sidebar.classList.remove('close');
      });
    });

    function closeAllSubMenus(){
      document.querySelectorAll('#sidebar .sub-menu.show').forEach(s=>s.classList.remove('show'));
      document.querySelectorAll('#sidebar .dropdown-btn.rotate').forEach(r=>r.classList.remove('rotate'));
    }
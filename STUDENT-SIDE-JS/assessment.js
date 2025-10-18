function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('close');
        }

        function toggleSubMenu(button) {
            button.nextElementSibling.classList.toggle('show');
            button.classList.toggle('rotate');
        }

        
        function showSection(sectionName) {
            
            const sections = document.querySelectorAll('.section-content');
            sections.forEach(section => {
                section.style.display = 'none';
            });

         
            const activeSection = document.getElementById(sectionName + '-section');
            if (activeSection) {
                activeSection.style.display = 'block';
            }
        }

        
        function showTab(tabName) {
            
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
            });

           
            const activeTab = document.getElementById(tabName + '-tab');
            if (activeTab) {
                activeTab.classList.add('active');
            }

           
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => {
                button.classList.remove('active');
            });

           
            if (event && event.target) {
                event.target.classList.add('active');
            }
        }

        
        function printStatement() {
            window.print();
        }

      
        document.addEventListener('DOMContentLoaded', function() {
            showSection('tuition');
        });
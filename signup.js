 document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const phoneNumber = document.getElementById('phonenumber').value;
            
            // Clear previous error messages
            document.querySelectorAll('.error-msg').forEach(msg => msg.remove());
            
            let isValid = true;
            
            // Password validation
            if (password.length < 8) {
                showError('password', 'Password must be at least 8 characters long');
                isValid = false;
            }
            
            if (password !== confirmPassword) {
                showError('confirm-password', 'Passwords do not match');
                isValid = false;
            }
            
            // Phone number validation (basic)
            const phoneRegex = /^[0-9]{10,15}$/;
            if (!phoneRegex.test(phoneNumber.replace(/\D/g, ''))) {
                showError('phonenumber', 'Please enter a valid phone number');
                isValid = false;
            }
            
            if (!document.getElementById('terms').checked) {
                showError('terms', 'Please agree to the Terms and Conditions');
                isValid = false;
            }
            
            // If all validations pass
            if (isValid) {
                // Show success message
                document.getElementById('successMessage').style.display = 'block';
                
                // In a real application, you would submit the form here
                // For demo purposes, we'll just log the data
                const formData = new FormData(this);
                const data = Object.fromEntries(formData);
                console.log('Form data:', data);
                
                // Simulate form submission
                setTimeout(() => {
                    alert('Registration successful! In a real application, this would connect to your database.');
                    // this.submit(); // Uncomment this to actually submit the form
                }, 1500);
            }
        });
        
        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorMsg = document.createElement('div');
            errorMsg.className = 'error-msg';
            errorMsg.textContent = message;
            field.parentNode.insertBefore(errorMsg, field.nextSibling);
            
            // Highlight the field with error
            field.style.borderColor = 'maroon';
        }
        
        // Remove error styling when user starts typing
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = '#ddd';
                const errorMsg = this.parentNode.querySelector('.error-msg');
                if (errorMsg) {
                    errorMsg.remove();
                }
            });
        });
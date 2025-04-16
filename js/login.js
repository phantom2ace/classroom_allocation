// login.js - Updated version
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const loginForm = document.getElementById('loginForm');
    const errorMessage = document.getElementById('errorMessage');
    const passwordInput = document.getElementById('password');
    const togglePasswordBtn = document.querySelector('.toggle-password');
    const usernameInput = document.getElementById('username');

    // Remove the auto-redirect check at the bottom of the file
    // We'll handle redirection only after successful login

    // Form submission handler
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear previous error messages
        errorMessage.textContent = '';
        
        // Get input values with trim to remove whitespace
        const username = usernameInput.value.trim();
        const password = passwordInput.value.trim();
        
        // Basic validation
        if (!username || !password) {
            errorMessage.textContent = 'Please enter both username and password.';
            return;
        }
        
        // Simple validation - password must be "password"
        if (password === 'password') {
            // Store login state in localStorage (with timestamp)
            const loginData = {
                isLoggedIn: true,
                username: username,
                timestamp: new Date().getTime()
            };
            localStorage.setItem('auth', JSON.stringify(loginData));
            
            // Store user details
            const userDetails = {
                fullName: username, // This will be displayed in index.html
                role: "Course Rep",
                department: "Computer Science",
                level: "300"
            };
            localStorage.setItem('userDetails', JSON.stringify(userDetails));
            
            // Initialize empty bookings if not exists
            if (!localStorage.getItem('bookings')) {
                localStorage.setItem('bookings', JSON.stringify({
                    past: [],
                    current: [],
                    upcoming: []
                }));
            }
            
            // Simulate loading state
            const submitBtn = loginForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.textContent;
            submitBtn.textContent = 'Signing in...';
            submitBtn.disabled = true;
            
            // Redirect after short delay to simulate processing
            setTimeout(() => {
                window.location.href = 'index.html';
            }, 800);
        } else {
            // Show error message with animation
            errorMessage.textContent = 'Invalid credentials. Please try again.';
            errorMessage.style.display = 'block';
            
            // Add shake animation to form
            loginForm.classList.add('shake');
            setTimeout(() => {
                loginForm.classList.remove('shake');
            }, 500);
            
            // Focus on password field
            passwordInput.focus();
        }
    });

    // Toggle password visibility
    if (togglePasswordBtn) {
        togglePasswordBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle SVG icon
            this.innerHTML = type === 'password' ? 
                `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>` :
                `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.94 17.94L14.12 14.12M6.06 6.06L3 3M20 12C20 12 18.18 18.18 12 18.18C10.09 18.18 8.33 17.58 6.88 16.56L4.59 19.35M4 12C4 12 5.82 5.82 12 5.82C13.91 5.82 15.67 6.42 17.12 7.44L19.41 4.65" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>`;
            
            this.setAttribute('aria-label', type === 'password' ? 'Show password' : 'Hide password');
        });
    }

    // Add keydown event for Enter key on password field
    passwordInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            loginForm.dispatchEvent(new Event('submit'));
        }
    });
});
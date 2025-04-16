// user-display.js
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is logged in
    const auth = JSON.parse(localStorage.getItem('auth'));
    if (!auth || !auth.isLoggedIn) {
        window.location.href = 'Login.html';
        return;
    }

    // Load user details
    const userDetails = JSON.parse(localStorage.getItem('userDetails'));
    if (userDetails && document.querySelector('.user-details h3')) {
        // Display user info
        document.querySelector('.user-details h3').textContent = userDetails.fullName;
        document.querySelector('.user-details p:nth-of-type(1)').textContent = 
            `${userDetails.role} • ${userDetails.department} • Level ${userDetails.level}`;
    }
});
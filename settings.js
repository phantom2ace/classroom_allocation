document.addEventListener('DOMContentLoaded', function() {
    // Check if user is logged in
    const auth = JSON.parse(localStorage.getItem('auth'));
    if (!auth || !auth.isLoggedIn) {
        window.location.href = 'Login.html';
        return;
    }

    // Load user details
    const userDetails = JSON.parse(localStorage.getItem('userDetails'));
    if (userDetails) {
        document.getElementById('full-name').value = userDetails.fullName || '';
        document.getElementById('email').value = userDetails.email || '';
        document.getElementById('department').value = userDetails.department || 'Computer Science';
        document.getElementById('level').value = userDetails.level || '300';
        
        // Notification preferences
        if (userDetails.notifications) {
            document.getElementById('email-notifications').checked = userDetails.notifications.email || false;
            document.getElementById('booking-reminders').checked = userDetails.notifications.reminders || false;
            document.getElementById('availability-alerts').checked = userDetails.notifications.alerts || false;
        }
    }

    // Profile form submission
    document.getElementById('profile-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const updatedDetails = {
            fullName: document.getElementById('full-name').value,
            email: document.getElementById('email').value,
            department: document.getElementById('department').value,
            level: document.getElementById('level').value,
            role: userDetails.role || "Course Rep",
            notifications: userDetails.notifications || {}
        };
        
        localStorage.setItem('userDetails', JSON.stringify(updatedDetails));
        alert('Profile updated successfully!');
    });

    // Password form submission
    document.getElementById('password-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const currentPassword = document.getElementById('current-password').value;
        const newPassword = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        
        if (newPassword !== confirmPassword) {
            alert('New passwords do not match!');
            return;
        }
        
        // In a real app, you would verify current password with server
        alert('Password changed successfully!');
        document.getElementById('password-form').reset();
    });

    // Notifications form submission
    document.getElementById('notifications-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const userDetails = JSON.parse(localStorage.getItem('userDetails'));
        userDetails.notifications = {
            email: document.getElementById('email-notifications').checked,
            reminders: document.getElementById('booking-reminders').checked,
            alerts: document.getElementById('availability-alerts').checked
        };
        
        localStorage.setItem('userDetails', JSON.stringify(userDetails));
        alert('Notification preferences saved!');
    });
});
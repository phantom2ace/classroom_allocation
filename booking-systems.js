// booking-system.js - Complete Working Solution

document.addEventListener('DOMContentLoaded', function() {
    // Only run on pages with manage-bookings section
    if (!document.querySelector('.manage-bookings')) return;
  
    // Load bookings immediately
    loadUserBookings();
  
    // Check booking status every minute
    const bookingCheckInterval = setInterval(checkBookingStatus, 60000);
  
    // Cleanup interval when leaving page
    window.addEventListener('beforeunload', () => {
        clearInterval(bookingCheckInterval);
    });
});

function loadUserBookings() {
    try {
        const auth = JSON.parse(localStorage.getItem('auth'));
        if (!auth || !auth.username) return;

        const bookings = JSON.parse(localStorage.getItem('bookings')) || {
            past: [],
            current: [],
            upcoming: []
        };

        const now = new Date();
        const userBookings = [...bookings.upcoming, ...bookings.current]
            .filter(booking => booking.bookedBy === auth.username)
            .sort((a, b) => {
                const dateA = new Date(`${a.date}T${a.startTime}`);
                const dateB = new Date(`${b.date}T${b.startTime}`);
                return dateA - dateB;
            });

        updateBookingDisplay(userBookings, now);
    } catch (error) {
        console.error("Failed to load bookings:", error);
    }
}

function updateBookingDisplay(bookings, now) {
    const container = document.querySelector('.manage-bookings .room-cards');
    const nextBookingEl = document.getElementById('nextBooking');
    
    if (!container || !nextBookingEl) return;

    container.innerHTML = '';

    if (bookings.length === 0) {
        container.innerHTML = '<div class="no-bookings">You have no active bookings</div>';
        nextBookingEl.textContent = 'No upcoming bookings';
        return;
    }

    // Update next booking
    nextBookingEl.textContent = `${bookings[0].roomId} on ${bookings[0].date} (${bookings[0].startTime}-${bookings[0].endTime})`;

    // Create booking cards
    bookings.forEach(booking => {
        const start = new Date(`${booking.date}T${booking.startTime}`);
        const end = new Date(`${booking.date}T${booking.endTime}`);
        
        const card = document.createElement('div');
        card.className = 'room-card';
        card.innerHTML = `
            <div class="room-icon">🏫</div>
            <h3 class="room-title">${booking.roomId}</h3>
            <p class="room-date">${booking.date}</p>
            <p class="room-time">${booking.startTime} - ${booking.endTime}</p>
            <p class="room-purpose">Purpose: ${booking.purpose || 'Not specified'}</p>
            <span class="room-status ${getStatusClass(now, start, end)}">
                ${getStatusText(now, start, end)}
            </span>
            <button class="btn btn-cancel" data-booking-id="${booking.timestamp}">
                Cancel Booking
            </button>
        `;
        
        card.querySelector('.btn-cancel').addEventListener('click', () => {
            cancelBooking(booking.timestamp, booking.roomId);
        });
        
        container.appendChild(card);
    });
}

function getStatusClass(now, start, end) {
    if (now > end) return 'past';
    if (now >= start) return 'current';
    return 'upcoming';
}

function getStatusText(now, start, end) {
    if (now > end) return 'Completed';
    if (now >= start) return 'In Progress';
    return 'Upcoming';
}

function cancelBooking(bookingId, roomId) {
    if (!confirm(`Cancel booking for Room ${roomId}?`)) return;
    
    try {
        const bookings = JSON.parse(localStorage.getItem('bookings'));
        
        // Remove from upcoming and current
        bookings.upcoming = bookings.upcoming.filter(b => b.timestamp !== bookingId);
        bookings.current = bookings.current.filter(b => b.timestamp !== bookingId);
        
        localStorage.setItem('bookings', JSON.stringify(bookings));
        loadUserBookings();
        alert(`Booking for Room ${roomId} cancelled`);
    } catch (error) {
        console.error("Cancel failed:", error);
        alert("Failed to cancel booking");
    }
}

function checkBookingStatus() {
    try {
        const bookings = JSON.parse(localStorage.getItem('bookings'));
        const now = new Date();
        let needsUpdate = false;

        // Check upcoming bookings
        bookings.upcoming = bookings.upcoming.filter(booking => {
            const start = new Date(`${booking.date}T${booking.startTime}`);
            if (now >= start) {
                bookings.current.push(booking);
                needsUpdate = true;
                return false;
            }
            return true;
        });

        // Check current bookings
        bookings.current = bookings.current.filter(booking => {
            const end = new Date(`${booking.date}T${booking.endTime}`);
            if (now >= end) {
                bookings.past.push(booking);
                needsUpdate = true;
                return false;
            }
            return true;
        });

        if (needsUpdate) {
            localStorage.setItem('bookings', JSON.stringify(bookings));
            loadUserBookings();
        }
    } catch (error) {
        console.error("Error checking booking status:", error);
    }
}
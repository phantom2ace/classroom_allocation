document.addEventListener('DOMContentLoaded', function() {
    // Header elements
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const searchToggle = document.querySelector('.search-toggle');
    const navLinks = document.querySelector('.nav-links');
    const searchContainer = document.querySelector('.search-container');
    const header = document.querySelector('.header-container');

    // Toggle functions
    const toggleMenu = (e) => {
        e.stopPropagation();
        navLinks.classList.toggle('active');
        searchContainer?.classList.remove('active');
    };

    const toggleSearch = (e) => {
        e.stopPropagation();
        searchContainer.classList.toggle('active');
        navLinks?.classList.remove('active');
    };

    // Close menus when clicking outside
    const closeMenus = (e) => {
        if (!e.target.closest('.header')) {
            navLinks?.classList.remove('active');
            searchContainer?.classList.remove('active');
        }
    };

    // Search functionality
    const performSearch = () => {
        const searchInput = document.getElementById('searchInput');
        const roomCards = document.querySelectorAll('.room-card');
        if (!searchInput || roomCards.length === 0) return;

        const query = searchInput.value.toLowerCase();
        const roomFilter = document.querySelector('.filter-dropdown:first-of-type')?.value || '';
        const deptFilter = document.querySelector('.filter-dropdown:last-of-type')?.value || '';

        roomCards.forEach(card => {
            const roomNumber = card.querySelector('.room-number')?.textContent.toLowerCase() || '';
            const department = card.querySelector('.room-department')?.textContent.toLowerCase() || '';
            const shouldShow = (roomNumber.includes(query) || department.includes(query)) &&
                             (!roomFilter || roomNumber.includes(roomFilter.toLowerCase())) &&
                             (!deptFilter || department.includes(deptFilter.toLowerCase()));
            
            card.style.display = shouldShow ? 'block' : 'none';
        });
    };

    // Event listeners
    if (menuToggle && navLinks) menuToggle.addEventListener('click', toggleMenu);
    if (searchToggle && searchContainer) searchToggle.addEventListener('click', toggleSearch);
    if (header) document.addEventListener('click', closeMenus);

    // Search event listeners
    document.querySelector('.search-btn')?.addEventListener('click', performSearch);
    document.getElementById('searchInput')?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') performSearch();
    });
    document.querySelectorAll('.filter-dropdown').forEach(dropdown => {
        dropdown.addEventListener('change', performSearch);
    });

    // Mobile search (if exists)
    document.querySelector('.mobile-search input')?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') performSearch();
    });
});
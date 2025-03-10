function myFunction() {
    var x = document.getElementById("myTopnav");
    const navLinks = x.querySelector('.nav-links');
    const menuIcon = x.querySelector('.menu-icon');
    const closeIcon = x.querySelector('.close-icon');

    // Toggle responsive class
    if (x.className === "topnav" || x.className === "topnav sticky") {
        x.className = x.className + " responsive";
    } else {
        x.className = x.className.replace(" responsive", "");
    }
    
    // Toggle nav-links and icons
    if (x.className.includes('responsive')) {
        navLinks.style.display = 'flex';
        menuIcon.style.display = 'none';
        closeIcon.style.display = 'block';
    } else {
        navLinks.style.display = '';
        menuIcon.style.display = 'block';
        closeIcon.style.display = 'none';
    }
}

// Add resize handler to fix menu visibility
window.addEventListener('resize', function() {
    const x = document.getElementById("myTopnav");
    const navLinks = x.querySelector('.nav-links');
    
    if (window.innerWidth > 768) {
        // Reset classes and styles for larger screens
        x.className = "topnav";
        if (navLinks) {
            navLinks.style.display = '';
        }
    } else {
        // Reset for mobile view
        if (!x.className.includes('responsive')) {
            if (navLinks) {
                navLinks.style.display = 'none';
            }
        }
    }
});

// Ensure correct initial visibility of icons
document.addEventListener('DOMContentLoaded', function() {
    const x = document.getElementById("myTopnav");
    const menuIcon = x.querySelector('.menu-icon');
    const closeIcon = x.querySelector('.close-icon');
    if (menuIcon && closeIcon) {
        menuIcon.style.display = 'block';
        closeIcon.style.display = 'none';
    }
});

// Keep the scroll handler simple
window.addEventListener('scroll', function() {
    const header = document.getElementById("myTopnav");
    const isResponsive = header.className.includes('responsive');
    
    if (window.scrollY > 0) {
        if (isResponsive) {
            header.className = "topnav sticky responsive";
        } else {
            header.className = "topnav sticky";
        }
    } else {
        if (isResponsive) {
            header.className = "topnav responsive";
        } else {
            header.className = "topnav";
        }
    }
});

// Update click outside handler
document.addEventListener('click', function(event) {
    const x = document.getElementById("myTopnav");
    const menuIcon = x.querySelector('.menu-icon');
    const closeIcon = x.querySelector('.close-icon');
    const navLinks = x.querySelector('.nav-links');
    
    if (!x.contains(event.target) && x.className.includes('responsive')) {
        x.className = x.className.replace(" responsive", "");
        navLinks.style.display = '';
        menuIcon.style.display = 'block';
        closeIcon.style.display = 'none';
    }
});
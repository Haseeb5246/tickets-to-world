// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Form validation
const bookingForm = document.querySelector('form');
if (bookingForm) {
    bookingForm.addEventListener('submit', function(e) {
        const name = document.querySelector('input[name="name"]');
        const email = document.querySelector('input[name="email"]');
        const phone = document.querySelector('input[name="phone"]');
        const from = document.querySelector('input[name="from"]');
        const to = document.querySelector('input[name="to"]');
        const departureDate = document.querySelector('input[name="departure_date"]');
        
        let isValid = true;
        
        // Basic validation
        if (!name.value.trim()) {
            isValid = false;
            name.classList.add('error');
        }
        
        if (!email.value.trim() || !email.value.includes('@')) {
            isValid = false;
            email.classList.add('error');
        }
        
        if (!phone.value.trim()) {
            isValid = false;
            phone.classList.add('error');
        }
        
        if (!from.value.trim()) {
            isValid = false;
            from.classList.add('error');
        }
        
        if (!to.value.trim()) {
            isValid = false;
            to.classList.add('error');
        }
        
        if (!departureDate.value) {
            isValid = false;
            departureDate.classList.add('error');
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields correctly.');
        }
    });
}

// FAQ Accordion functionality
document.querySelectorAll('.faq-item').forEach(item => {
    const question = item.querySelector('.faq-question');
    
    question.addEventListener('click', () => {
        const isActive = item.classList.contains('active');
        
        // Close all items
        document.querySelectorAll('.faq-item').forEach(otherItem => {
            otherItem.classList.remove('active');
        });
        
        // Toggle clicked item
        if (!isActive) {
            item.classList.add('active');
        }
    });
});

// Mobile dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const dropbtn = dropdown.querySelector('.dropbtn');
        
        dropbtn.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                dropdown.classList.toggle('active');
                
                // Close other dropdowns
                dropdowns.forEach(other => {
                    if (other !== dropdown) {
                        other.classList.remove('active');
                    }
                });
            }
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }
    });
});


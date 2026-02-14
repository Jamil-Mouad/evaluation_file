/* ==========================================================================
   FORM PAGE JS — EST Fkih Ben Salah
   Burger menu, scroll-to-top, jury member filter, nav active state
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function() {

    // ===== Burger Menu Toggle =====
    const burgerBtn = document.getElementById('burgerBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    const burgerOpen = burgerBtn ? burgerBtn.querySelector('.burger-open') : null;
    const burgerClose = burgerBtn ? burgerBtn.querySelector('.burger-close') : null;

    if (burgerBtn && mobileMenu) {
        burgerBtn.addEventListener('click', function() {
            const isOpen = mobileMenu.classList.toggle('open');
            if (burgerOpen) burgerOpen.style.display = isOpen ? 'none' : 'block';
            if (burgerClose) burgerClose.style.display = isOpen ? 'block' : 'none';
        });

        // Close menu on link click
        mobileMenu.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('open');
                if (burgerOpen) burgerOpen.style.display = 'block';
                if (burgerClose) burgerClose.style.display = 'none';
            });
        });
    }

    // ===== Scroll to Top Button =====
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    if (scrollTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY >= 120) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        });

        scrollTopBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ===== Active Nav Link on Scroll =====
    const navLinks = document.querySelectorAll('.topnav-link');
    const sections = document.querySelectorAll('.card[id]');

    function updateActiveNav() {
        var scrollPos = window.scrollY + 100;
        sections.forEach(function(section) {
            var top = section.offsetTop;
            var bottom = top + section.offsetHeight;
            var id = section.getAttribute('id');
            navLinks.forEach(function(link) {
                if (link.getAttribute('href') === '#' + id) {
                    if (scrollPos >= top && scrollPos < bottom) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                }
            });
        });
    }

    window.addEventListener('scroll', updateActiveNav);
    updateActiveNav();

    // ===== Jury Member Filter (prevent duplicate selections) =====
    filterMembers();

    document.querySelectorAll('select[name^="membre_"]').forEach(function(select) {
        select.addEventListener('change', filterMembers);
    });
});

function filterMembers() {
    var selects = document.querySelectorAll('select[name^="membre_"]');
    var selectedValues = [];
    selects.forEach(function(select) {
        if (select.value) {
            selectedValues.push(select.value);
        }
    });
    selects.forEach(function(select) {
        var options = select.querySelectorAll('option');
        options.forEach(function(option) {
            if (option.value) {
                option.disabled = selectedValues.includes(option.value) && !option.selected;
            }
        });
    });
}

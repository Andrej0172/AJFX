// Page Load Handler
document.addEventListener('DOMContentLoaded', () => {
    const loadingIndicator = document.getElementById('loadingIndicator');
    
    // Hide loading after short delay
    setTimeout(() => {
        loadingIndicator.classList.remove('active');
    }, 300);
    
    // Initialize features
    setupNavigation();
});

// Mobile Menu Toggle
function toggleMobileMenu() {
    const navMenu = document.getElementById('navMenu');
    const toggle = document.querySelector('.mobile-menu-toggle');
    
    navMenu.classList.toggle('active');
    toggle.classList.toggle('active');
}

// Close mobile menu when clicking a link
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
        const navMenu = document.getElementById('navMenu');
        const toggle = document.querySelector('.mobile-menu-toggle');
        
        if (navMenu.classList.contains('active')) {
            navMenu.classList.remove('active');
            toggle.classList.remove('active');
        }
    });
});

// Navigation
function navigateTo(section) {
    const loadingIndicator = document.getElementById('loadingIndicator');
    loadingIndicator.classList.add('active');
    
    // Simulate navigation delay
    setTimeout(() => {
        loadingIndicator.classList.remove('active');
        showPlaceholderAlert(section);
    }, 600);
}

// Show placeholder alert
function showPlaceholderAlert(section) {
    const sectionNames = {
        '#home': 'Home',
        '#lessen': 'Lessen',
        '#reserveringen': 'Reserveringen',
        '#account': 'Account',
        '#dashboard': 'Dashboard',
        '#zoeken': 'Zoeken op Lesnaam',
        '#filteren': 'Les Filteren',
        '#reserveringen-overzicht': 'Reserveringen Overzicht',
        '#account-overzicht': 'Account Overzicht',
        '#home-pagina': 'Home Pagina',
        '#aanbiedingen': 'Aanbiedingen Lessen Pagina',
        '#account-beheren': 'Account Beheren',
        '#medewerker-overzicht': 'Medewerker Overzicht',
        '#medewerker-beheer': 'Medewerker Beheer',
        '#leden-overzicht': 'Leden Overzicht',
        '#lid-zoeken': 'Lid Zoeken op Achternaam',
        '#leden-beheren': 'Leden Beheren',
        '#lessen-overzicht': 'Lessen Overzicht',
        '#les-beheren': 'Les Beheren',
        '#reservering-beheren': 'Reservering Beheren',
        '#geplande-lessen': 'Overzicht Geplande Lessen',
        '#help': 'Help Center',
        '#contact': 'Contact',
        '#status': 'Status'
    };
    
    const sectionName = sectionNames[section] || 'Deze pagina';
    
    // Create alert notification
    const alertDiv = document.createElement('div');
    alertDiv.className = 'custom-alert';
    alertDiv.innerHTML = `
        <div class="alert-content">
            <div class="alert-icon">ℹ️</div>
            <div class="alert-text">
                <strong>${sectionName}</strong>
                <p>Deze pagina wordt binnenkort beschikbaar. Op dit moment is alleen de homepagina toegankelijk.</p>
            </div>
            <button onclick="closeCustomAlert()" class="alert-close">✕</button>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Show alert
    setTimeout(() => {
        alertDiv.classList.add('show');
    }, 10);
    
    // Auto close after 4 seconds
    setTimeout(() => {
        closeCustomAlert();
    }, 4000);
}

// Close custom alert
function closeCustomAlert() {
    const alert = document.querySelector('.custom-alert');
    if (alert) {
        alert.classList.remove('show');
        setTimeout(() => {
            alert.remove();
        }, 300);
    }
}

// Add alert styles
const alertStyles = document.createElement('style');
alertStyles.textContent = `
    .custom-alert {
        position: fixed;
        top: 90px;
        right: 20px;
        max-width: 380px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 2000;
        transform: translateX(420px);
        transition: transform 0.3s;
    }
    
    .custom-alert.show {
        transform: translateX(0);
    }
    
    .alert-content {
        padding: 1.25rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .alert-icon {
        font-size: 1.75rem;
        flex-shrink: 0;
    }
    
    .alert-text strong {
        display: block;
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
        color: #1e293b;
    }
    
    .alert-text p {
        font-size: 0.9rem;
        color: #475569;
        margin: 0;
        line-height: 1.5;
    }
    
    .alert-close {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        background: none;
        border: none;
        font-size: 1.25rem;
        cursor: pointer;
        color: #94a3b8;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
    }
    
    .alert-close:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    
    @media (max-width: 768px) {
        .custom-alert {
            right: 10px;
            left: 10px;
            max-width: none;
        }
    }
`;
document.head.appendChild(alertStyles);

// Error Modal Functions
function showErrorModal() {
    const modal = document.getElementById('errorModal');
    modal.classList.add('active');
}

function closeErrorModal() {
    const modal = document.getElementById('errorModal');
    modal.classList.remove('active');
}

// Close modal when clicking outside
document.getElementById('errorModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'errorModal') {
        closeErrorModal();
    }
});

// Setup Navigation
function setupNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    
    window.addEventListener('scroll', () => {
        let scrollPosition = window.scrollY;
        
        if (scrollPosition > 100) {
            navLinks.forEach(link => {
                if (link.getAttribute('href') === '#home') {
                    link.classList.remove('active');
                }
            });
        } else {
            navLinks.forEach(link => {
                if (link.getAttribute('href') === '#home') {
                    link.classList.add('active');
                }
            });
        }
    });
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        
        if (href === '#home' || href === '#') {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    });
});

// Keyboard accessibility
document.querySelectorAll('.feature-item').forEach(element => {
    element.setAttribute('tabindex', '0');
    element.setAttribute('role', 'button');
    
    element.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            element.click();
        }
    });
});

// Console message
console.log('%cAJFX - Lessen Management Platform', 'font-size: 16px; font-weight: bold; color: #2563eb;');
console.log('Homepage geladen. Alle functionaliteiten zijn beschikbaar voor navigatie.');

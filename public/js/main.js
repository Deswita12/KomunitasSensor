// public/js/main.js

// ============================================================
// 1. NAVBAR DROPDOWN "HOME" (DESKTOP)
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    const desktopDataBtn = document.getElementById('desktop-data-btn');
    const desktopDropdown = document.getElementById('desktop-dropdown');
    const dataArrow = document.getElementById('data-arrow');

    if (desktopDataBtn && desktopDropdown) {
        desktopDataBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = !desktopDropdown.classList.contains('hidden');
            desktopDropdown.classList.toggle('hidden', isOpen);
            if (dataArrow) dataArrow.style.transform = isOpen ? '' : 'rotate(180deg)';
        });

        document.addEventListener('click', (e) => {
            if (!desktopDataBtn.contains(e.target) && !desktopDropdown.contains(e.target)) {
                desktopDropdown.classList.add('hidden');
                if (dataArrow) dataArrow.style.transform = '';
            }
        });
    }
});

// ============================================================
// 2. HAMBURGER MENU MOBILE
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const hamburgerIcon = document.getElementById('hamburger-icon');
    const mobileDataBtn = document.getElementById('mobile-data-btn');
    const mobileDataSub = document.getElementById('mobile-data-sub');
    const mobileDataArrow = document.getElementById('mobile-data-arrow');

    if (!hamburgerBtn || !mobileMenu) return;

    hamburgerBtn.addEventListener('click', () => {
        const isOpen = !mobileMenu.classList.contains('hidden');
        mobileMenu.classList.toggle('hidden', isOpen);
        mobileMenu.classList.toggle('flex', !isOpen);
        if (hamburgerIcon) hamburgerIcon.textContent = isOpen ? 'menu' : 'close';
        hamburgerBtn.setAttribute('aria-expanded', String(!isOpen));
    });

    if (mobileDataBtn && mobileDataSub) {
        mobileDataBtn.addEventListener('click', () => {
            const isOpen = !mobileDataSub.classList.contains('hidden');
            mobileDataSub.classList.toggle('hidden', isOpen);
            mobileDataSub.classList.toggle('flex', !isOpen);
            if (mobileDataArrow) mobileDataArrow.style.transform = isOpen ? '' : 'rotate(180deg)';
        });
    }

    document.addEventListener('click', (e) => {
        if (!hamburgerBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
            mobileMenu.classList.add('hidden');
            mobileMenu.classList.remove('flex');
            if (hamburgerIcon) hamburgerIcon.textContent = 'menu';
            hamburgerBtn.setAttribute('aria-expanded', 'false');
        }
    });
});

// ============================================================
// 3. SETTINGS PANEL (TEMA LIGHT/DARK)
// ============================================================
function openSettings() {
    document.getElementById('settings-overlay')?.classList.remove('hidden');
    document.getElementById('settings-panel')?.classList.remove('translate-x-full');
}

function closeSettings() {
    document.getElementById('settings-overlay')?.classList.add('hidden');
    document.getElementById('settings-panel')?.classList.add('translate-x-full');
}

function setTheme(mode) {
    const html = document.documentElement;
    const lightBtn = document.getElementById('theme-light-btn');
    const darkBtn = document.getElementById('theme-dark-btn');

    if (mode === 'dark') {
        html.classList.remove('light');
        html.classList.add('dark');
        localStorage.setItem('sk-theme', 'dark');
    } else {
        html.classList.remove('dark');
        html.classList.add('light');
        localStorage.setItem('sk-theme', 'light');
    }

    [lightBtn, darkBtn].forEach(btn => {
        if (!btn) return;
        btn.classList.remove('border-primary', 'bg-primary-container/20');
        btn.classList.add('border-outline-variant');
    });

    const activeBtn = mode === 'dark' ? darkBtn : lightBtn;
    if (activeBtn) {
        activeBtn.classList.remove('border-outline-variant');
        activeBtn.classList.add('border-primary', 'bg-primary-container/20');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('sk-theme') || 'light';
    setTheme(savedTheme);
});

// ============================================================
// 4. MODAL BANTUAN — OPEN/CLOSE
// ============================================================
function openHelpModal() {
    closeSettings();
    const overlay = document.getElementById('help-modal-overlay');
    if (overlay) {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
    }
}

function closeHelpModal(event) {
    if (event && event.target.id !== 'help-modal-overlay') return;
    const overlay = document.getElementById('help-modal-overlay');
    if (overlay) {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
    }
}

// ============================================================
// 5. MODAL BANTUAN — SUBMIT FORM
// ============================================================
async function submitHelpForm() {
    const name = document.getElementById('help-name').value.trim();
    const email = document.getElementById('help-email').value.trim();
    const message = document.getElementById('help-message').value.trim();
    const errorEl = document.getElementById('help-error');

    if (!name || !email || !message) {
        errorEl.textContent = 'Semua field wajib diisi.';
        errorEl.classList.remove('hidden');
        return;
    }

    try {
        const res = await fetch('/bantuan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ name, email, message }),
        });

        if (!res.ok) {
            const err = await res.json();
            errorEl.textContent = err.message || 'Gagal mengirim pesan.';
            errorEl.classList.remove('hidden');
            return;
        }

        document.getElementById('help-form-state').classList.add('hidden');
        document.getElementById('help-success-state').classList.remove('hidden');
    } catch (e) {
        errorEl.textContent = 'Terjadi kesalahan jaringan.';
        errorEl.classList.remove('hidden');
    }
}

function resetHelpForm() {
    document.getElementById('help-name').value = '';
    document.getElementById('help-email').value = '';
    document.getElementById('help-message').value = '';
    document.getElementById('help-error')?.classList.add('hidden');
    document.getElementById('help-form-state').classList.remove('hidden');
    document.getElementById('help-success-state').classList.add('hidden');
    closeHelpModal();
}

// ============================================================
// 6. FAQ ACCORDION (dipakai di /panduan#faq)
// ============================================================
function toggleFaq(buttonEl) {
    const faqItem = buttonEl.closest('.faq-item');
    if (!faqItem) return;

    const body = faqItem.querySelector('.faq-body');
    const icon = buttonEl.querySelector('.material-symbols-outlined');
    const isOpen = !body.classList.contains('hidden');

    body.classList.toggle('hidden', isOpen);
    if (icon) icon.style.transform = isOpen ? '' : 'rotate(180deg)';
}

function switchSubTab(tab) {
    const dashboard = document.getElementById('tab-dashboard');
    const info = document.getElementById('tab-info');

    if (!dashboard || !info) return;

    if (tab === 'info') {
        dashboard.classList.add('hidden');
        dashboard.classList.remove('block');

        info.classList.remove('hidden');
        info.classList.add('block');

    } else {

        info.classList.add('hidden');
        info.classList.remove('block');

        dashboard.classList.remove('hidden');
        dashboard.classList.add('block');

        if (typeof loadDashboard === 'function') {
            loadDashboard();
        }

        if (typeof map !== "undefined" && map) {
            setTimeout(() => {
                map.invalidateSize();
            }, 100);
        }
    }

    // tutup dropdown
    document.getElementById('desktop-dropdown')?.classList.add('hidden');

    document.getElementById('mobile-menu')?.classList.add('hidden');
    document.getElementById('mobile-menu')?.classList.remove('flex');

    document.getElementById('data-arrow')?.classList.remove('rotate-180');

    if(document.getElementById('hamburger-icon')){
        document.getElementById('hamburger-icon').textContent='menu';
    }
}
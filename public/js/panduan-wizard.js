// panduan-wizard.js
// Wizard panduan SensorKita: navigasi step, switch paket (Basic / Plus), salin kode, dan FAQ.

let currentPackage = 'basic';
let currentStep = 1;
let visibleSteps = [];

function getAllStepEls() {
    return Array.from(document.querySelectorAll('.step-content'));
}

function normalizePkg(value) {
    return (value || '').toString().trim().toLowerCase();
}

function isVisibleForPackage(rawPkg) {
    const pkg = normalizePkg(rawPkg) || 'common';
    return pkg === 'common' || pkg === 'all' || pkg === currentPackage;
}

function recomputeVisibleSteps() {
    visibleSteps = getAllStepEls().filter((el) => isVisibleForPackage(el.dataset.package));
}

function applyPackageVisibility() {
    // Step utama
    getAllStepEls().forEach((el) => {
        const show = isVisibleForPackage(el.dataset.package);
        if (!show) el.classList.add('hidden');
    });

    // Indikator titik di atas
    document.querySelectorAll('.step-ind').forEach((ind) => {
        const show = isVisibleForPackage(ind.dataset.package);
        ind.style.display = show ? '' : 'none';
    });

    // Item alat & bahan, video, diagram, baris wiring — semua elemen yang ditandai data-package
    document.querySelectorAll('[data-package]:not(.step-content):not(.step-ind)').forEach((el) => {
        const show = isVisibleForPackage(el.dataset.package);
        el.style.display = show ? '' : 'none';
    });
}

function showStep(stepNumber) {
    if (!visibleSteps.length) return;

    visibleSteps.forEach((el) => el.classList.add('hidden'));

    const clamped = Math.min(Math.max(stepNumber, 1), visibleSteps.length);
    const target = visibleSteps[clamped - 1];
    if (!target) return;

    target.classList.remove('hidden');
    currentStep = clamped;

    const titleEl = document.getElementById('step-title');
    const counterEl = document.getElementById('step-counter');
    if (titleEl) titleEl.textContent = target.dataset.title || '';
    if (counterEl) counterEl.textContent = `${clamped} dari ${visibleSteps.length}`;

    // Highlight indikator yang aktif
    const allSteps = getAllStepEls();
    document.querySelectorAll('.step-ind').forEach((ind) => {
        ind.classList.remove('bg-primary');
        ind.classList.add('bg-surface-container');
    });
    const originalIndex = allSteps.indexOf(target);
    const activeInd = document.getElementById('ind-' + (originalIndex + 1));
    if (activeInd) {
        activeInd.classList.add('bg-primary');
        activeInd.classList.remove('bg-surface-container');
    }

    // Tombol navigasi
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    if (prevBtn) prevBtn.disabled = clamped === 1;
    if (nextBtn) {
        nextBtn.innerHTML = clamped === visibleSteps.length
            ? 'Selesai <span class="material-symbols-outlined text-sm sm:text-base">check</span>'
            : 'Selanjutnya <span class="material-symbols-outlined text-sm sm:text-base">chevron_right</span>';
    }

    const wizardSection = document.getElementById('wizard-section');
    if (wizardSection) {
        window.scrollTo({ top: wizardSection.offsetTop - 80, behavior: 'smooth' });
    }
}

function changeStep(delta) {
    showStep(currentStep + delta);
}

function goToStep(originalStepNumber) {
    // originalStepNumber = posisi step di urutan penuh (sesuai render Blade)
    const allSteps = getAllStepEls();
    const el = allSteps[originalStepNumber - 1];
    if (!el) return;

    const visIndex = visibleSteps.indexOf(el);
    if (visIndex === -1) return; // step ini tidak tampil di paket yang sedang aktif

    showStep(visIndex + 1);
}

function switchPackage(pkg) {
    if (pkg === currentPackage) return;
    currentPackage = pkg;

    document.querySelectorAll('.package-switch-btn').forEach((btn) => {
        const active = btn.dataset.pkg === pkg;
        btn.classList.toggle('bg-primary', active);
        btn.classList.toggle('text-on-primary', active);
        btn.classList.toggle('text-on-surface-variant', !active);
    });

    applyPackageVisibility();
    recomputeVisibleSteps();
    showStep(1);
}

function scrollToWizard() {
    const wizardSection = document.getElementById('wizard-section');
    if (wizardSection) wizardSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function copyCode(elId, btn) {
    const codeEl = document.getElementById(elId);
    if (!codeEl) return;

    navigator.clipboard.writeText(codeEl.textContent).then(() => {
        const original = btn.innerHTML;
        btn.innerHTML = '<span class="material-symbols-outlined text-xs sm:text-sm">check</span><span class="hidden sm:inline">Disalin!</span>';
        setTimeout(() => {
            btn.innerHTML = original;
        }, 2000);
    });
}

function toggleFaq(btn) {
    const body = btn.nextElementSibling;
    const icon = btn.querySelector('.material-symbols-outlined');
    if (!body) return;

    const isOpen = !body.classList.contains('hidden');
    body.classList.toggle('hidden');
    if (icon) icon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}

document.addEventListener('DOMContentLoaded', () => {
    applyPackageVisibility();
    recomputeVisibleSteps();
    showStep(1);
});
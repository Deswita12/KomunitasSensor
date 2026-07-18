// community.js — slideshow foto + form testimoni (halaman Komunitas)

let galleryIndex = 0;

function gallerySlideGoTo(index) {
    const slides = document.querySelectorAll('.gallery-slide');
    const dots = document.querySelectorAll('.gallery-dot');
    if (!slides.length) return;

    slides[galleryIndex]?.classList.remove('opacity-100');
    slides[galleryIndex]?.classList.add('opacity-0');
    dots[galleryIndex]?.classList.remove('bg-white', 'w-4');
    dots[galleryIndex]?.classList.add('bg-white/50');

    galleryIndex = (index + slides.length) % slides.length;

    slides[galleryIndex]?.classList.remove('opacity-0');
    slides[galleryIndex]?.classList.add('opacity-100');
    dots[galleryIndex]?.classList.remove('bg-white/50');
    dots[galleryIndex]?.classList.add('bg-white', 'w-4');

    const captionEl = document.querySelector('.gallery-caption');
    const activeSlide = slides[galleryIndex];
    if (captionEl && activeSlide) {
        captionEl.textContent = activeSlide.getAttribute('alt') || 'Dokumentasi Komunitas';
    }
}

async function submitTestimonial() {
    const name = document.getElementById('testimonial-name').value.trim();
    const message = document.getElementById('testimonial-message').value.trim();
    const errorEl = document.getElementById('testimonial-error');

    if (!name || !message) {
        errorEl.textContent = 'Nama dan cerita wajib diisi.';
        errorEl.classList.remove('hidden');
        return;
    }

    try {
        const res = await fetch('/komunitas/testimoni', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ author_name: name, message }),
        });

        if (!res.ok) {
            const err = await res.json();
            errorEl.textContent = err.message || 'Gagal mengirim komentar.';
            errorEl.classList.remove('hidden');
            return;
        }

        document.getElementById('testimonial-form-state').classList.add('hidden');
        document.getElementById('testimonial-success-state').classList.remove('hidden');
    } catch (e) {
        errorEl.textContent = 'Terjadi kesalahan jaringan.';
        errorEl.classList.remove('hidden');
    }
}

function gallerySlideNext() { gallerySlideGoTo(galleryIndex + 1); }
function gallerySlidePrev() { gallerySlideGoTo(galleryIndex - 1); }

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('gallery-slideshow');
    if (container) {
        const total = Number(container.dataset.total || 0);
        if (total > 1) setInterval(gallerySlideNext, 5000); // auto-geser tiap 5 detik
    }
});
// SEBELUM (hardcode array, harus edit kode tiap ada kit baru):
// const SMART_DEVICES = ["19684", "19682"];

// SESUDAH (dibaca dari database via Filament, lewat tag JSON yang di-render Blade):
const SMART_DEVICES = JSON.parse(
    document.getElementById('device-ids-data')?.textContent || '[]'
);

// Dropdown "Mode Sensor" juga dibangun dinamis dari daftar yang sama,
// supaya tidak perlu edit HTML manual setiap admin tambah/hapus kit di Filament.
function populateDeviceModeSelect() {
    const select = document.getElementById('deviceMode');
    if (!select) return;

    // Buang semua <option> kecuali "Semua Device" yang sudah ada di HTML asli
    select.querySelectorAll('option:not([value="all"])').forEach(opt => opt.remove());

    SMART_DEVICES.forEach(id => {
        const opt = document.createElement('option');
        opt.value = id;
        opt.textContent = `Device ${id}`;
        select.appendChild(opt);
    });
}
document.addEventListener('DOMContentLoaded', populateDeviceModeSelect);

// SEBELUM (langsung ke API eksternal + expose key):
// const SMART_API = "https://api.smartcitizen.me/v0/devices/";
// async function fetchSmartDevice(id) {
//     const res = await fetch(SMART_API + id, {...});
// }

// SESUDAH (lewat proxy backend):
async function fetchSmartDevice(id) {
    const res = await fetch(`/api/proxy/smart-citizen/${id}`);
    if (!res.ok) throw new Error("Proxy Smart Citizen error " + id);
    const json = await res.json();
    // ... sisa logic parsing sensor (getSensors, findSensor, dst) TETAP SAMA seperti kode asli
}

async function fetchBmkg(adm4) {
    const res = await fetch(`/api/proxy/bmkg?adm4=${encodeURIComponent(adm4)}`);
    if (!res.ok) throw new Error("Proxy BMKG error");
    return await res.json();
}

// Ganti seluruh fungsi analyzeDeviceAI — HAPUS GEMINI_KEY dan panggilan langsung
async function analyzeDeviceAI(device) {
    try {
        const res = await fetch('/api/proxy/ai-analysis', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken, // didefinisikan di layouts/app.blade.php
            },
            body: JSON.stringify({
                temp: device.temp,
                rh: device.rh,
                iaq: device.iaq,
                pressure: device.pressure,
                location: device.location.city,
            }),
        });
        if (!res.ok) return 'Analisis tidak tersedia saat ini.';
        const data = await res.json();
        return data.analysis;
    } catch (e) {
        console.error('analyzeDeviceAI error:', e);
        return 'Gagal mengambil analisis AI.';
    }
}
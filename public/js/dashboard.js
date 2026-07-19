// dashboard.js

const SMART_DEVICES = JSON.parse(
    document.getElementById('device-ids-data')?.textContent || '[]'
);

// ✅ Deklarasi sekali di sini sebagai let kosong
let el = {};
let map, markers = [];

function populateDeviceModeSelect() {
    const select = document.getElementById('deviceMode');
    if (!select) return;
    select.querySelectorAll('option:not([value="all"])').forEach(o => o.remove());
    SMART_DEVICES.forEach(id => {
        const opt = document.createElement('option');
        opt.value = id;
        opt.textContent = 'Device ' + id;
        select.appendChild(opt);
    });
}

// ✅ TIDAK ada switchSubTab di sini — ada di main.js

function fmt(value) {
    if (value === null || value === undefined || value === "") return "--";
    const n = Number(value);
    return Number.isNaN(n) ? "--" : n.toFixed(2);
}

function avg(values) {
    const nums = values.filter(v => v !== null && !Number.isNaN(Number(v))).map(Number);
    return nums.length ? nums.reduce((a, b) => a + b, 0) / nums.length : null;
}

function getSensors(json) {
    if (json.data && Array.isArray(json.data.sensors)) return json.data.sensors;
    if (Array.isArray(json.sensors)) return json.sensors;
    return [];
}
function sensorText(sensor) {
    return [sensor.name, sensor.description, sensor.unit, sensor.measurement?.name, sensor.measurement?.description]
        .filter(Boolean).join(" ").toLowerCase();
}
function findSensor(sensors, keywords) {
    return sensors.find(sensor => keywords.some(k => sensorText(sensor).includes(k)));
}
function valueOf(sensor) { return sensor ? Number(sensor.value) : null; }
function getLocation(json) {
    const loc = json.location || {};
    const lat = loc.latitude ?? loc.lat ?? loc.geo_lat ?? null;
    const lon = loc.longitude ?? loc.lng ?? loc.lon ?? loc.geo_lon ?? null;
    return {
        city: loc.city || "Kabupaten Tangerang",
        country: loc.country || "Indonesia",
        latitude: lat !== null ? Number(lat) : null,
        longitude: lon !== null ? Number(lon) : null
    };
}

async function fetchSmartDevice(id) {
    const res = await fetch(`/api/proxy/smart-citizen/${id}`);
    if (!res.ok) throw new Error("Proxy Smart Citizen error " + id);
    const json = await res.json();
    const sensors = getSensors(json);

    // Cek kapan terakhir device kirim data
    const lastRecorded = json.last_reading_at ? new Date(json.last_reading_at) : null;
    const minutesSinceUpdate = lastRecorded ? (Date.now() - lastRecorded.getTime()) / 60000 : Infinity;
    const STALE_THRESHOLD_MINUTES = 30; // anggap offline kalau gak update > 30 menit, sesuaikan sesuai kebutuhan

    const rawState = json.state || "-";
    const effectiveState = minutesSinceUpdate > STALE_THRESHOLD_MINUTES ? "offline" : rawState;

    return {
        id,
        name: json.name || "Device " + id,
        state: effectiveState,
        update: lastRecorded ? lastRecorded.toLocaleString("id-ID") : "-",
        location: getLocation(json),
        temp: valueOf(findSensor(sensors, ["temperature", "air temperature", "°c", "ºc"])),
        rh: valueOf(findSensor(sensors, ["humidity", "relative humidity"])),
        iaq: valueOf(findSensor(sensors, ["iaq", "air quality", "air-freshness"])),
        pressure: valueOf(findSensor(sensors, ["pressure", "barometric"]))
    };
}

async function fetchBmkg(adm4) {
    const res = await fetch(`/api/proxy/bmkg?adm4=${encodeURIComponent(adm4)}`);
    if (!res.ok) throw new Error("Proxy BMKG error");
    return await res.json();
}

async function analyzeDeviceAI(device) {
    try {
        const res = await fetch('/api/proxy/ai-analysis', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
            body: JSON.stringify({
                temp: device.temp, rh: device.rh, iaq: device.iaq,
                pressure: device.pressure, location: device.location.city
            })
        });
        if (!res.ok) return 'Analisis tidak tersedia saat ini.';
        const data = await res.json();
        return data.analysis;
    } catch (e) {
        console.error('analyzeDeviceAI error:', e);
        return 'Gagal mengambil analisis AI.';
    }
}

function flattenWeather(json) {
    const rows = [];
    function walk(node) {
        if (Array.isArray(node)) { node.forEach(walk); }
        else if (node && typeof node === "object") {
            if (node.local_datetime || node.utc_datetime || node.weather_desc || node.weather_desc_en || node.t !== undefined || node.hu !== undefined) rows.push(node);
            Object.values(node).forEach(walk);
        }
    }
    walk(json);
    return rows;
}

function analyzeBmkg(json) {
    const rows = flattenWeather(json).slice(0, 24);
    const rainRows = rows.filter(r => /hujan|rain/.test(String(r.weather_desc || r.weather_desc_en || "").toLowerCase()));
    const humidRows = rows.filter(r => Number(r.hu) >= 70);
    return {
        rows,
        rainCount: rainRows.length,
        humidCount: humidRows.length,
        avgTemp: avg(rows.map(r => Number(r.t))),
        avgHum: avg(rows.map(r => Number(r.hu)))
    };
}

function computeRisk(devices, bmkg) {
    const sensorTemp = avg(devices.map(d => d.temp));
    const sensorRh = avg(devices.map(d => d.rh));
    let dengueScore = 0;
    if (sensorTemp !== null && sensorTemp >= 25 && sensorTemp <= 30) dengueScore += 2;
    else if (sensorTemp !== null && sensorTemp >= 24 && sensorTemp <= 34) dengueScore += 1;
    if (sensorRh !== null && sensorRh >= 70) dengueScore += 2;
    else if (sensorRh !== null && sensorRh >= 60) dengueScore += 1;
    if (bmkg.rainCount >= 3) dengueScore += 2;
    else if (bmkg.rainCount >= 1) dengueScore += 1;
    if (bmkg.humidCount >= 6) dengueScore += 1;
    const dengue = dengueScore >= 5 ? "HIGH" : dengueScore >= 3 ? "MEDIUM" : "LOW";
    const mold = sensorRh !== null && sensorRh > 70 ? "HIGH" : sensorRh !== null && sensorRh >= 60 ? "MEDIUM" : "LOW";
    const malaria = sensorTemp !== null && sensorTemp >= 24 && sensorTemp <= 32 && sensorRh !== null && sensorRh >= 70 && bmkg.rainCount >= 3 ? "MEDIUM" : "LOW";
    const iaqAvg = avg(devices.map(d => d.iaq));
    const air = iaqAvg !== null && iaqAvg > 200 ? "HIGH" : iaqAvg !== null && iaqAvg > 100 ? "MEDIUM" : "LOW";
    let score = 100;
    if (dengue === "HIGH") score -= 25; if (dengue === "MEDIUM") score -= 12;
    if (mold === "HIGH") score -= 20; if (mold === "MEDIUM") score -= 10;
    if (air === "HIGH") score -= 20; if (air === "MEDIUM") score -= 10;
    if (bmkg.rainCount >= 3) score -= 10;
    return { dengue, mold, malaria, air, sensorTemp, sensorRh, score: Math.max(0, Math.min(100, score)) };
}

function riskClass(level) { return level === "LOW" ? "low" : level === "MEDIUM" ? "medium" : "high"; }

function riskCard(title, level, text) {
    return `
        <div class="risk ${riskClass(level)} p-5 rounded-2xl flex flex-col justify-between shadow-sm transition-shadow hover:shadow-md">
            <div>
                <h3 class="text-sm font-bold tracking-tight mb-1 text-on-surface">${title}</h3>
                <div class="level inline-block px-3 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider mb-3">${level}</div>
                <p class="text-xs sm:text-sm text-on-surface-variant leading-relaxed">${text}</p>
            </div>
        </div>`;
}

function renderRisk(risk, bmkg) {
    el.riskGrid.innerHTML = `
        ${riskCard("Dengue / DBD Early Warning", risk.dengue, risk.dengue === "HIGH" ? "Kombinasi suhu, kelembapan, dan potensi hujan mendukung kewaspadaan tinggi DBD. Perkuat PSN 3M Plus dalam 7–10 hari ke depan." : risk.dengue === "MEDIUM" ? "Kondisi cukup mendukung vektor DBD. Lakukan pemeriksaan genangan dan edukasi warga." : "Kondisi saat ini belum menunjukkan kombinasi risiko tinggi DBD.")}
        ${riskCard("Malaria Environmental Watch", risk.malaria, "Indikator ini bukan deteksi malaria. Digunakan sebagai kewaspadaan lingkungan berbasis suhu, kelembapan, dan hujan, terutama untuk kasus impor atau wilayah reseptif.")}
        ${riskCard("Mold & Respiratory Health", risk.mold, risk.mold === "HIGH" ? "RH rata-rata tinggi. Tingkatkan ventilasi, cek area lembap, kamar tidur, pakaian, dinding, dan ruang tertutup." : risk.mold === "MEDIUM" ? "RH berada pada zona pantau. Perhatikan ruangan tertutup dan waktu malam hari." : "RH relatif terkendali untuk risiko mold.")}
        ${riskCard("Indoor Air Freshness", risk.air, risk.air === "HIGH" ? "IAQ/VOC proxy menunjukkan kualitas kesegaran udara kurang baik. Cek sumber VOC seperti memasak, pembersih, dupa, atau ventilasi buruk." : risk.air === "MEDIUM" ? "IAQ perlu dipantau sebagai indikator kesegaran udara dalam ruang." : "IAQ relatif masih dalam kondisi baik.")}
        ${riskCard("Rainfall & Urban Resilience", bmkg.rainCount >= 3 ? "MEDIUM" : "LOW", bmkg.rainCount >= 3 ? "Prakiraan hujan muncul pada beberapa slot. Perlu perhatian pada drainase, genangan, dan tempat perkembangbiakan nyamuk." : "Belum banyak slot prakiraan hujan yang terdeteksi.")}
        ${riskCard("Urban Resilience Score", risk.score < 60 ? "HIGH" : risk.score < 80 ? "MEDIUM" : "LOW", "Skor ketahanan lingkungan hari ini: " + risk.score + "/100. Semakin tinggi skor, semakin baik kondisi lingkungan dan kesiapsiagaan.")}
    `;
}

function renderRecommendation(risk, bmkg) {
    const items = [];
    if (risk.dengue === "HIGH") {
        items.push("Prioritaskan PSN 3M Plus: menguras, menutup, mendaur ulang/mengubur barang bekas, dan memeriksa talang, pot tanaman, ember, dispenser, serta wadah air.");
        items.push("Gunakan prakiraan hujan sebagai pengingat pemeriksaan genangan selama 7–10 hari berikutnya.");
        items.push("Koordinasikan surveilans jentik, edukasi warga, dan pemantauan wilayah sekitar node sensor dengan RH tinggi.");
    } else if (risk.dengue === "MEDIUM") {
        items.push("Lakukan pemeriksaan genangan air secara berkala dan perkuat edukasi PSN 3M Plus.");
    }
    if (risk.mold !== "LOW") items.push("Untuk mold dan kesehatan pernapasan: buka ventilasi saat kelembapan turun, gunakan exhaust fan/dehumidifier, dan cek area lembap di kamar tidur.");
    if (risk.air !== "LOW") items.push("Untuk IAQ/VOC: tingkatkan ventilasi, cek aktivitas memasak, pembersih, dupa, cat, atau furnitur baru yang dapat meningkatkan VOC.");
    if (bmkg.rainCount > 0) items.push("BMKG menunjukkan indikasi hujan pada beberapa slot prakiraan. Perkuat pembersihan saluran, wadah air terbuka, dan titik genangan.");
    items.push("Dashboard ini hanya indikator kewaspadaan lingkungan. Keputusan kesehatan publik tetap mengikuti arahan Dinas Kesehatan dan surveilans resmi.");
    el.recommendationBox.innerHTML = "<ol class='list-decimal pl-4 space-y-1.5'>" + items.map(i => `<li>${i}</li>`).join("") + "</ol>";
}

async function renderDevices(devices) {
    el.deviceTable.innerHTML = devices.map(d => {
        const online = d.state === "online" || d.state === "has_published";
        const coord = d.location.latitude !== null && d.location.longitude !== null
            ? `${fmt(d.location.latitude)}, ${fmt(d.location.longitude)}` : '-';
        const safeName = String(d.name).replace(/'/g, "\\'");
        return `
            <tr class="hover:bg-surface-container-low/50 transition-colors" id="row-${d.id}">
                <td class="px-5 py-3.5">
                    <button type="button" onclick="openDeviceHistory('${d.id}', '${safeName}')" class="font-bold text-on-surface hover:text-primary hover:underline text-left">${d.name}</button>
                    <br><span class="text-xs text-outline font-mono">${d.id}</span>
                </td>
                <td class="px-5 py-3.5"><span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full ${online ? 'bg-primary-fixed text-on-primary-container border border-primary/20' : 'bg-surface-container-highest text-on-surface-variant border border-outline-variant'}">${online ? 'ONLINE' : 'OFFLINE'}</span></td>
                <td class="px-5 py-3.5 font-mono font-semibold">${fmt(d.temp)} °C</td>
                <td class="px-5 py-3.5 font-mono font-semibold">${fmt(d.rh)} %</td>
                <td class="px-5 py-3.5 font-mono">${fmt(d.iaq)}</td>
                <td class="px-5 py-3.5 text-xs">${coord}</td>
                <td class="px-5 py-3.5 text-xs">${d.update}</td>
                <td class="px-5 py-3.5 text-xs text-on-surface-variant max-w-[260px]" id="ai-${d.id}">
                    ${online ? `
                    <div class="flex items-center gap-1.5 text-outline animate-pulse">
                        <span class="material-symbols-outlined text-sm">auto_awesome</span> Menganalisis...
                    </div>` : `<span class="text-outline">Device offline — analisis tidak tersedia.</span>`}
                </td>
            </tr>`;
    }).join('');

    for (const d of devices) {
        const online = d.state === "online" || d.state === "has_published";
        if (!online) continue;
        const analysis = await analyzeDeviceAI(d);
        const cell = document.getElementById(`ai-${d.id}`);
        if (cell) {
            cell.innerHTML = `
                <div class="space-y-1">
                    <div class="flex items-center gap-1 text-[10px] font-bold text-primary mb-1">
                        <span class="material-symbols-outlined text-xs">auto_awesome</span> AI Analysis
                    </div>
                    <p class="text-[11px] sm:text-xs leading-relaxed text-on-surface-variant">${analysis}</p>
                </div>`;
        }
    }
}


function initMap() {
    if (map) return;
    map = L.map("map").setView([-6.18, 106.63], 11);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19, attribution: "&copy; OpenStreetMap"
    }).addTo(map);
}

function renderMap(devices) {
    initMap();
    markers.forEach(m => map.removeLayer(m));
    markers = [];
    const valid = devices.filter(d => d.location.latitude !== null && d.location.longitude !== null);
    valid.forEach(d => {
        const marker = L.marker([d.location.latitude, d.location.longitude])
            .addTo(map)
            .bindPopup(`<strong>${d.name}</strong><br>Device ID: ${d.id}<br>Temp: ${fmt(d.temp)} °C<br>RH: ${fmt(d.rh)} %<br>IAQ: ${fmt(d.iaq)}`);
        markers.push(marker);
    });
    if (valid.length) map.fitBounds(L.featureGroup(markers).getBounds().pad(.25));
}

function renderBmkg(bmkg) {
    const rows = bmkg.rows.slice(0, 8);
    if (!rows.length) {
        el.bmkgBox.innerHTML = "<div class='text-error font-semibold'>Data BMKG belum terbaca. Periksa kode ADM4.</div>";
        return;
    }
    el.bmkgBox.innerHTML = `
        <div class="space-y-3">
            <p class="font-bold text-primary text-sm flex items-center gap-1"><span class="material-symbols-outlined text-sm">cloud_sync</span> Ringkasan BMKG:</p>
            <div class="text-xs sm:text-sm space-y-1 bg-surface-container/40 p-3 rounded-xl border border-outline-variant/30 font-medium">
                <p>Rata-rata Suhu: <b class="text-on-surface">${fmt(bmkg.avgTemp)} °C</b></p>
                <p>Rata-rata Kelembapan: <b class="text-on-surface">${fmt(bmkg.avgHum)} %</b></p>
                <p>Slot Indikasi Hujan: <b class="text-primary">${bmkg.rainCount} slot</b></p>
                <p>Slot Kelembapan Tinggi (≥70%): <b class="text-secondary">${bmkg.humidCount} slot</b></p>
            </div>
            <p class="font-bold text-on-surface text-xs sm:text-sm border-t border-outline-variant/40 pt-2">Timeline Cuaca Terdekat:</p>
            <div class="text-[11px] sm:text-xs font-mono space-y-1.5 opacity-90 overflow-y-auto max-h-[160px] pr-1">
                ${rows.map(r => `<div>• ${r.local_datetime || r.utc_datetime || "-"} | ${r.weather_desc || r.weather_desc_en || "-"} | ${r.t ?? "-"}°C | RH ${r.hu ?? "-"}%</div>`).join("")}
            </div>
        </div>`;
}

function drawSensorChart(devices) {
    const canvas = el.sensorChart;
    if (!canvas) return;
    const ctx = canvas.getContext("2d");
    const w = canvas.width, h = canvas.height;
    ctx.clearRect(0, 0, w, h);
    const values = [...devices.map(d => d.temp), ...devices.map(d => d.rh)].filter(v => v !== null && !Number.isNaN(v));
    if (!values.length) {
        ctx.fillStyle = "#6b7280"; ctx.font = "14px Inter, Arial";
        ctx.fillText("Data sensor belum tersedia untuk chart.", 40, 60);
        return;
    }
    const maxValue = Math.max(100, ...values);
    const left = 60, top = 45, bottom = 55, right = 30;
    const chartW = w - left - right, chartH = h - top - bottom;
    const groupW = chartW / devices.length, barW = Math.min(44, groupW / 4);
    ctx.strokeStyle = "#f0eded"; ctx.lineWidth = 1;
    for (let i = 0; i <= 5; i++) {
        const y = top + chartH * i / 5;
        ctx.beginPath(); ctx.moveTo(left, y); ctx.lineTo(w - right, y); ctx.stroke();
        ctx.fillStyle = "#424941"; ctx.font = "11px monospace, Arial";
        ctx.fillText((maxValue - maxValue * i / 5).toFixed(0), 24, y + 4);
    }
    devices.forEach((d, i) => {
        const x = left + groupW * i + groupW / 2;
        const tempH = d.temp !== null ? (d.temp / maxValue) * chartH : 0;
        const rhH = d.rh !== null ? (d.rh / maxValue) * chartH : 0;
        ctx.fillStyle = "#416744"; ctx.fillRect(x - barW - 6, top + chartH - tempH, barW, tempH);
        ctx.fillStyle = "#695d46"; ctx.fillRect(x + 6, top + chartH - rhH, barW, rhH);
        ctx.fillStyle = "#1b1c1c"; ctx.font = "bold 12px 'Plus Jakarta Sans', Arial";
        ctx.fillText(d.id, x - 18, h - 24);
    });
    ctx.fillStyle = "#416744"; ctx.fillRect(w - 265, 18, 12, 12);
    ctx.fillStyle = "#1b1c1c"; ctx.font = "12px Inter, Arial"; ctx.fillText("Temperature °C", w - 247, 28);
    ctx.fillStyle = "#695d46"; ctx.fillRect(w - 135, 18, 12, 12);
    ctx.fillStyle = "#1b1c1c"; ctx.fillText("Humidity %", w - 117, 28);
}

let historyDeviceId = null;  

function openDeviceHistory(deviceId, deviceName) {
    historyDeviceId = deviceId;
    const titleEl = document.getElementById('history-modal-title');
    if (titleEl) titleEl.textContent = `Riwayat Data — ${deviceName}`;

    const overlay = document.getElementById('history-modal-overlay');
    if (!overlay) return;
    overlay.classList.remove('hidden');
    overlay.classList.add('flex');

    document.querySelectorAll('.history-range-btn').forEach(btn => {
        btn.classList.toggle('bg-primary', btn.dataset.range === '7');
        btn.classList.toggle('text-on-primary', btn.dataset.range === '7');
    });

    loadDeviceHistory(7);
}

function closeDeviceHistory(event) {
    if (event && event.target.id !== 'history-modal-overlay') return;
    const overlay = document.getElementById('history-modal-overlay');
    if (!overlay) return;
    overlay.classList.add('hidden');
    overlay.classList.remove('flex');
    historyDeviceId = null;
}

async function loadDeviceHistory(days) {
    if (!historyDeviceId) return;

    document.querySelectorAll('.history-range-btn').forEach(btn => {
        const active = Number(btn.dataset.range) === days;
        btn.classList.toggle('bg-primary', active);
        btn.classList.toggle('text-on-primary', active);
        btn.classList.toggle('text-on-surface-variant', !active);
    });

    const tbody = document.getElementById('history-table-body');
    if (!tbody) return;
    tbody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-on-surface-variant">Memuat...</td></tr>`;

    try {
        const res = await fetch(`/api/proxy/smart-citizen/${historyDeviceId}/history?range=${days}d`);
        if (!res.ok) throw new Error('Gagal memuat riwayat');
        const json = await res.json();
        const readings = json.readings || [];

        if (!readings.length) {
            tbody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-on-surface-variant">Belum ada data riwayat untuk periode ini.</td></tr>`;
            return;
        }

        tbody.innerHTML = readings.slice().reverse().map(r => `
            <tr>
                <td class="px-4 py-2 text-xs">${r.recorded_at ? new Date(r.recorded_at).toLocaleString('id-ID') : '-'}</td>
                <td class="px-4 py-2 font-mono">${fmt(r.temp)} °C</td>
                <td class="px-4 py-2 font-mono">${fmt(r.rh)} %</td>
                <td class="px-4 py-2 font-mono">${fmt(r.iaq)}</td>
                <td class="px-4 py-2 text-xs">${r.state || '-'}</td>
            </tr>`).join('');
    } catch (e) {
        console.error('loadDeviceHistory error:', e);
        tbody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-error">Gagal memuat riwayat.</td></tr>`;
    }
}

async function loadDashboard() {
    try {
        if (el.deviceTable) el.deviceTable.innerHTML = `<tr><td colspan="8" class="p-5"><div class="p-3 bg-orange-50 text-amber-800 rounded-xl font-semibold">Memuat data sensor...</div></td></tr>`;
        if (el.riskGrid) el.riskGrid.innerHTML = `<div class="p-4 bg-orange-50 text-amber-800 border border-orange-200 font-bold rounded-2xl text-sm">Menghitung matriks risiko...</div>`;
        if (el.bmkgBox) el.bmkgBox.innerHTML = "Mengambil data prakiraan BMKG...";
        const selected = el.deviceMode?.value || "all";
        const deviceIds = selected === "all" ? SMART_DEVICES : [selected];
        const smartResults = await Promise.allSettled(deviceIds.map(fetchSmartDevice));
         const devices = smartResults.map((r, i) => {
            if (r.status === "fulfilled") return r.value;
            return {
                id: deviceIds[i],
                name: 'Device ' + deviceIds[i],
                state: 'offline',
                update: '-',
                location: { city: '-', country: '-', latitude: null, longitude: null },
                temp: null,
                rh: null,
                iaq: null,
                pressure: null
            };
        });
        const bmkgJson = await fetchBmkg(el.adm4Input?.value?.trim() || "36.03.03.1001");
        const bmkg = analyzeBmkg(bmkgJson);
        const risk = computeRisk(devices, bmkg);
        if (el.avgTemp) el.avgTemp.textContent = fmt(risk.sensorTemp);
        if (el.avgHum) el.avgHum.textContent = fmt(risk.sensorRh);
        if (el.rainWatch) el.rainWatch.textContent = bmkg.rainCount;
        if (el.dengueLevel) el.dengueLevel.textContent = risk.dengue;
        if (el.resilienceScore) el.resilienceScore.textContent = risk.score;
        await renderDevices(devices);
        renderMap(devices);
        renderBmkg(bmkg);
        renderRisk(risk, bmkg);
        renderRecommendation(risk, bmkg);
        drawSensorChart(devices);
    } catch (error) {
        console.error(error);
        if (el.riskGrid) el.riskGrid.innerHTML = `<div class="p-4 bg-red-50 text-red-800 border border-red-200 font-bold rounded-2xl text-sm lg:col-span-3">Dashboard gagal dimuat. Periksa koneksi API internet Anda atau kode ADM4 BMKG.</div>`;
        if (el.bmkgBox) el.bmkgBox.innerHTML = "<div class='text-error font-semibold'>Gagal menyinkronkan data prakiraan cuaca spasial.</div>";
    }
}

// ✅ Satu-satunya DOMContentLoaded, el di-assign di sini
document.addEventListener('DOMContentLoaded', () => {
    el = {
        deviceMode: document.getElementById("deviceMode"),
        adm4Input: document.getElementById("adm4Input"),
        refreshBtn: document.getElementById("refreshBtn"),
        avgTemp: document.getElementById("avgTemp"),
        avgHum: document.getElementById("avgHum"),
        rainWatch: document.getElementById("rainWatch"),
        dengueLevel: document.getElementById("dengueLevel"),
        resilienceScore: document.getElementById("resilienceScore"),
        riskGrid: document.getElementById("riskGrid"),
        recommendationBox: document.getElementById("recommendationBox"),
        bmkgBox: document.getElementById("bmkgBox"),
        deviceTable: document.getElementById("deviceTable"),
        sensorChart: document.getElementById("sensorChart")
    };

    populateDeviceModeSelect();
    if (el.refreshBtn) el.refreshBtn.addEventListener("click", loadDashboard);
    if (el.deviceMode) el.deviceMode.addEventListener("change", loadDashboard);
    setInterval(loadDashboard, 300000);
    if (document.getElementById('map')) loadDashboard();
});
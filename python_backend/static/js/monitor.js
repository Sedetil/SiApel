/**
 * SIABSEN POLINELA — Local QR Scanner Monitor Script
 * Khusus untuk Alat Scanner QR (USB HID Keyboard Emulation & Web Serial COM)
 * Tanpa YOLO dan Tanpa CCTV.
 */

const PYTHON_API_URL = '/api/python';
const LOCAL_STORAGE_KEY = 'siapel_local_sync_data';
const COOLDOWN_MS = 30 * 1000;

let currentAttendanceList = [];
let currentMonitorTab = 'pending';

// ===== JAM REAL-TIME =====
function updateClock() {
    const now = new Date();
    const clockEl = document.getElementById('clock');
    if (clockEl) clockEl.textContent = now.toLocaleTimeString('id-ID', { hour12: false }).replace(/\./g, ':');

    const dateEl = document.getElementById('date-label');
    const opts = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    if (dateEl) dateEl.textContent = now.toLocaleDateString('id-ID', opts);
}
setInterval(updateClock, 1000);
updateClock();

// ===== BUNYI BEEP OTOMATIS (WEB AUDIO API SYNTHESIZER) =====
function playBeep(type = 'success') {
    try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return;
        const ctx = new AudioCtx();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();

        osc.connect(gain);
        gain.connect(ctx.destination);

        if (type === 'success') {
            osc.type = 'sine';
            osc.frequency.setValueAtTime(1046.5, ctx.currentTime); // C6
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            osc.start();
            osc.stop(ctx.currentTime + 0.14);
        } else if (type === 'warning') {
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
            gain.gain.setValueAtTime(0.25, ctx.currentTime);
            osc.start();
            osc.stop(ctx.currentTime + 0.25);
        } else {
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(220, ctx.currentTime); // A3
            gain.gain.setValueAtTime(0.25, ctx.currentTime);
            osc.start();
            osc.stop(ctx.currentTime + 0.35);
        }
    } catch (e) {
        // Ignore audio context restriction
    }
}

// ===== EFEK VISUAL SAAT ALAT SCANNER QR MENEMBAK =====
function flashScannerRing() {
    const ring = document.getElementById('scanner-visual-ring');
    if (!ring) return;
    ring.classList.add('scan-active');
    setTimeout(() => ring.classList.remove('scan-active'), 400);
}

// ===== SUBMIT DARI INPUT FORM MANUAL / SCANNER =====
function handleManualQrSubmit(e) {
    e.preventDefault();
    const input = document.getElementById('manual-qr-input');
    if (!input) return;
    const code = input.value.trim();
    if (!code) return;

    input.value = '';
    flashScannerRing();
    recordAttendance(code);
}

// ===== PROSES PRESENSI KE PYTHON LOKAL =====
async function recordAttendance(qrCodeValue) {
    const cleanCode = String(qrCodeValue || '').trim();
    if (!cleanCode) return;

    try {
        const kegiatanSelect = document.getElementById('kegiatan-select');
        const kegiatanId = kegiatanSelect && kegiatanSelect.value ? kegiatanSelect.value : null;

        const res = await fetch(`${PYTHON_API_URL}/attendance`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                mahasiswa_id: cleanCode,
                status: 'hadir',
                kegiatan_id: kegiatanId
            })
        });

        const data = await res.json();

        if (res.ok && data.success) {
            saveToLocalSync(data);
            renderLastScanCard(data);

            const status = data.result ? data.result.status : '';
            const isLate = data.result ? data.result.is_late : false;
            const mahasiswaName = data.mahasiswa ? data.mahasiswa.name : 'Peserta';

            if (status === 'checked_in') {
                playBeep(isLate ? 'warning' : 'success');
                showToast(
                    isLate
                        ? `${mahasiswaName} tercatat TERLAMBAT (${data.result.late_duration} menit)`
                        : `${mahasiswaName} berhasil presensi masuk (Tepat Waktu)`,
                    isLate ? '#F5A623' : '#22C55E'
                );
            } else if (status === 'checked_out') {
                playBeep('success');
                showToast(`${mahasiswaName} berhasil presensi keluar`, '#2D56FF');
            } else {
                playBeep('warning');
                showToast(data.alert_text || data.message || 'Pemindaian diabaikan', '#F5A623');
            }
        } else {
            playBeep('error');
            if (data.show_alert && typeof Swal !== 'undefined') {
                Swal.fire({
                    title: data.alert_title || 'Presensi Ditolak',
                    text: data.alert_text || data.message,
                    icon: data.alert_type || 'error',
                    timer: 3500,
                    showConfirmButton: true,
                    confirmButtonColor: '#2D56FF'
                });
            } else {
                showToast(data.message || 'Gagal memproses QR Code', '#FF0004');
            }
        }
    } catch (err) {
        console.error('Attendance recording error:', err);
        playBeep('error');
        showToast('Gagal menghubungi server Python lokal (port 5000)', '#FF0004');
    }
}

// ===== TAMPILKAN KARTU HASIL SCAN TERAKHIR =====
function renderLastScanCard(data) {
    const body = document.getElementById('last-scan-body');
    const timeEl = document.getElementById('last-scan-time');
    if (!body || !data || !data.mahasiswa) return;

    const mhs = data.mahasiswa;
    const res = data.result || {};
    if (timeEl) timeEl.textContent = res.time || new Date().toLocaleTimeString('id-ID', { hour12: false });

    let pillClass = 'hadir';
    let pillText = 'HADIR TEPAT WAKTU';

    if (res.status === 'checked_out' || res.status === 'already_checked_out') {
        pillClass = 'keluar';
        pillText = 'PRESENSI KELUAR';
    } else if (res.is_late) {
        pillClass = 'terlambat';
        pillText = `TERLAMBAT (${res.late_duration || 0} MENIT)`;
    }

    body.innerHTML = `
        <div class="scan-detail-box">
            <div>
                <div class="scan-detail-name">${escapeHtml(mhs.name)}</div>
                <div class="scan-detail-id">NPM / ID: ${escapeHtml(mhs.id)}</div>
            </div>
            <div class="scan-detail-meta">
                <div class="meta-item">
                    <span>Kelas / Kompi</span>
                    <strong>${escapeHtml(mhs.kompi || '-')}</strong>
                </div>
                <div class="meta-item">
                    <span>Program Studi</span>
                    <strong>${escapeHtml(mhs.jurusan || '-')}</strong>
                </div>
            </div>
            <div class="scan-status-pill ${pillClass}">
                <span class="material-symbols-outlined" style="font-size: 16px;">verified</span>
                <span>${pillText}</span>
            </div>
        </div>
    `;
}

// ===== LOCAL STORAGE ANTREAN SYNC =====
function getLocalSyncData() {
    try {
        const raw = localStorage.getItem(LOCAL_STORAGE_KEY);
        return raw ? JSON.parse(raw) : [];
    } catch {
        return [];
    }
}

function getLocalTime() {
    const now = new Date();
    const pad = n => (n < 10 ? '0' + n : n);
    return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
}

function saveToLocalSync(responseData) {
    if (!responseData || !responseData.result || !responseData.mahasiswa) return;

    const validActions = ['checked_in', 'checked_out', 'already_checked_in', 'already_checked_out'];
    if (!validActions.includes(responseData.result.status)) {
        return;
    }

    const data = getLocalSyncData();
    const actionTime = responseData.result.time || getLocalTime();
    const kegiatanSelect = document.getElementById('kegiatan-select');
    const kegiatanId = kegiatanSelect && kegiatanSelect.value ? kegiatanSelect.value : null;

    const newRecord = {
        id: 'local_' + Date.now() + '_' + Math.random().toString(36).substring(2, 6),
        mahasiswa_id: responseData.mahasiswa.id,
        name: responseData.mahasiswa.name,
        kompi: responseData.mahasiswa.kompi,
        jurusan: responseData.mahasiswa.jurusan,
        kegiatan_id: kegiatanId,
        check_in: ['checked_in', 'already_checked_in'].includes(responseData.result.status) ? actionTime : null,
        check_out: ['checked_out', 'already_checked_out'].includes(responseData.result.status) ? actionTime : null,
        action: responseData.result.status,
        is_late: responseData.result.is_late || false,
        late_duration: responseData.result.late_duration || 0,
        synced: false
    };

    const existingIdx = data.findIndex(d => d.mahasiswa_id === newRecord.mahasiswa_id && d.kegiatan_id === kegiatanId);
    if (existingIdx >= 0) {
        if (newRecord.action === 'checked_out' || newRecord.action === 'already_checked_out') {
            data[existingIdx].check_out = newRecord.check_out || actionTime;
            data[existingIdx].action = newRecord.action;
            data[existingIdx].synced = false;
        }
    } else {
        data.unshift(newRecord);
    }

    localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(data));
    currentAttendanceList = data;
    renderRecentScans(currentAttendanceList);
    updateStats(currentAttendanceList);
}

// ===== PUSH DATA LOKAL KE SERVER ONLINE LARAVEL =====
async function syncDataToServer() {
    const data = getLocalSyncData();
    const unsynced = data.filter(d => !d.synced);

    if (unsynced.length === 0) {
        Swal.fire({
            title: 'Tidak Ada Antrean',
            text: 'Seluruh data presensi lokal sudah tersinkronisasi ke server.',
            icon: 'info',
            confirmButtonColor: '#2D56FF'
        });
        return;
    }

    const currentServerUrl = getLaravelBaseUrl();
    const confirmSync = await Swal.fire({
        title: `Push ${unsynced.length} Presensi ke Server?`,
        html: `
            <div style="text-align:left; font-size:13px; color:#6B7A90;">
                <p style="margin-bottom:12px;">Sistem akan membuat file <strong>Backup Excel Lokal</strong> terlebih dahulu, lalu mengirim <strong>${unsynced.length} data presensi</strong> ke database Server Laravel:</p>
                <label style="display:block; margin-bottom:6px; font-weight:600; color:#0F1729;">URL Server Tujuan:</label>
                <input type="text" id="push-server-url-input" value="${currentServerUrl}" style="width:100%; padding:10px; border:1px solid #D9D9D9; border-radius:8px; font-size:13px;" />
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Backup & Push Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#2D56FF'
    });

    if (!confirmSync.isConfirmed) return;

    const urlInput = document.getElementById('push-server-url-input');
    const targetBaseUrl = setLaravelBaseUrl(urlInput ? urlInput.value : currentServerUrl);

    const btn = document.getElementById('sync-button');
    if (btn) btn.innerHTML = '<span>Menyinkronkan...</span>';

    try {
        // 1. Buat Backup Excel di lokal Python
        await fetch(`${PYTHON_API_URL}/backup`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ data: unsynced })
        });

        // 2. Push ke endpoint /api/sync di Server Laravel
        const res = await fetch(`${targetBaseUrl}/api/sync`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ data: unsynced })
        });

        const resData = await res.json();
        if (!res.ok || !resData.success) {
            throw new Error(resData.message || `HTTP Error ${res.status}`);
        }

        const rejectedCount = resData.rejected_count || 0;
        const syncedCount = resData.synced_count || 0;
        const rejectionReasons = resData.rejection_reasons || [];
        const syncedIds = resData.synced_ids || [];

        data.forEach(item => {
            if (!item.synced) {
                if (syncedIds.length > 0) {
                    if (syncedIds.includes(item.id)) item.synced = true;
                } else if (rejectedCount === 0) {
                    item.synced = true;
                }
            }
        });

        localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(data));
        currentAttendanceList = data;
        renderRecentScans(currentAttendanceList);
        updateStats(currentAttendanceList);

        if (rejectedCount > 0) {
            Swal.fire({
                title: 'Sinkronisasi Selesai',
                html: `
                    <div style="text-align:left; font-size:13px;">
                        <p><strong style="color:#22C55E;">✓ Berhasil di-push:</strong> ${syncedCount} data</p>
                        <p><strong style="color:#FF0004;">✗ Ditolak:</strong> ${rejectedCount} data</p>
                        ${rejectionReasons.length > 0 ? `
                            <div style="margin-top:10px; padding:10px; background:#FEF2F2; border-radius:8px; max-height:180px; overflow-y:auto;">
                                <ul style="padding-left:18px; color:#991B1B;">
                                    ${rejectionReasons.map(r => `<li>${escapeHtml(r)}</li>`).join('')}
                                </ul>
                            </div>
                        ` : ''}
                    </div>
                `,
                icon: 'warning',
                confirmButtonColor: '#2D56FF'
            });
        } else {
            Swal.fire({
                title: 'Berhasil Di-Push!',
                text: `${syncedCount} data presensi berhasil disimpan ke database server.`,
                icon: 'success',
                confirmButtonColor: '#22C55E'
            });
        }
    } catch (err) {
        console.error('Push sync error:', err);
        Swal.fire({
            title: 'Gagal Push ke Server',
            html: `<p style="font-size:13px;">Tidak dapat mengirim data ke <code>${escapeHtml(targetBaseUrl)}/api/sync</code>:</p>
                   <p style="font-size:12px; color:#FF0004; margin-top:8px; font-family:monospace;">${escapeHtml(err.message)}</p>`,
            icon: 'error',
            confirmButtonColor: '#FF0004'
        });
    } finally {
        if (btn) {
            btn.innerHTML = `<span class="material-symbols-outlined" style="font-size: 16px;">cloud_upload</span><span>Sync ke Server</span>`;
        }
    }
}

// ===== CLEAR DATA LOKAL =====
function clearLocalData() {
    Swal.fire({
        title: 'Bersihkan Data Lokal?',
        text: 'Seluruh daftar antrean presensi lokal pada browser dan state memori Python akan di-reset.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#FF0004',
        cancelButtonColor: '#6B7A90',
        confirmButtonText: 'Ya, Bersihkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            executeClearLocalData();
        }
    });
}

function executeClearLocalData() {
    localStorage.removeItem(LOCAL_STORAGE_KEY);
    currentAttendanceList = [];

    fetch(`${PYTHON_API_URL}/reset-state`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({})
    }).catch(() => {});

    renderRecentScans([]);
    updateStats([]);
    showToast('Antrean presensi lokal telah dibersihkan', '#22C55E');
}

// ===== RENDER TAB & DAFTAR LOG =====
function switchMonitorTab(tab) {
    currentMonitorTab = tab;
    const btnPending = document.getElementById('tab-pending');
    const btnHistory = document.getElementById('tab-history');

    if (btnPending && btnHistory) {
        btnPending.classList.toggle('active', tab === 'pending');
        btnHistory.classList.toggle('active', tab === 'history');
    }

    renderRecentScans(currentAttendanceList);
}

function getInitials(name) {
    return String(name || 'P')
        .split(' ')
        .slice(0, 2)
        .map(w => w[0] || '')
        .join('')
        .toUpperCase();
}

function renderRecentScans(attendanceData) {
    const container = document.getElementById('recent-scans');
    const countEl = document.getElementById('log-count');
    const pendingBadge = document.getElementById('pending-count-badge');
    const syncedBadge = document.getElementById('synced-count-badge');

    const allData = attendanceData || [];
    const pendingList = allData.filter(d => !d.synced);
    const syncedList = allData.filter(d => d.synced);

    if (pendingBadge) pendingBadge.textContent = pendingList.length;
    if (syncedBadge) syncedBadge.textContent = syncedList.length;
    if (countEl) countEl.textContent = pendingList.length;

    const filteredData = currentMonitorTab === 'pending' ? pendingList : syncedList;

    if (filteredData.length === 0) {
        const emptyMsg = currentMonitorTab === 'pending'
            ? 'Belum ada presensi baru yang menunggu sinkronisasi'
            : 'Belum ada riwayat presensi yang sudah di-sync';
        container.innerHTML = `
            <div class="empty-state">
                <span class="material-symbols-outlined" style="font-size: 40px; opacity: 0.35;">qr_code_scanner</span>
                <p>${emptyMsg}</p>
            </div>`;
        return;
    }

    container.innerHTML = filteredData.map(record => {
        const isCheckout = !!record.check_out;
        const isLate = !!record.is_late && !isCheckout;
        const timeRaw = isCheckout ? record.check_out : record.check_in;
        const initials = getInitials(record.name);

        let badgeClass = 'masuk';
        let badgeLabel = 'HADIR';
        if (isCheckout) {
            badgeClass = 'keluar';
            badgeLabel = 'KELUAR';
        } else if (isLate) {
            badgeClass = 'terlambat';
            badgeLabel = `TERLAMBAT ${record.late_duration || 0}m`;
        }

        return `
            <div class="log-item ${isCheckout ? 'checkout' : (isLate ? 'late' : '')}">
                <div class="log-avatar">${escapeHtml(initials)}</div>
                <div class="log-body">
                    <div class="mhs-name">${escapeHtml(record.name)}</div>
                    <div class="mhs-info">
                        <span>${escapeHtml(record.mahasiswa_id)}</span>
                        <span>&bull;</span>
                        <span>${escapeHtml(record.kompi || '-')}</span>
                        <span class="action-badge ${badgeClass}">${badgeLabel}</span>
                    </div>
                </div>
                <div class="scan-time">${escapeHtml(timeRaw || '--:--:--')}</div>
            </div>
        `;
    }).join('');
}

function updateStats(data) {
    const list = data || [];
    const total = list.length;
    const terlambat = list.filter(r => r.is_late).length;
    const tepat = Math.max(0, total - terlambat);

    const elTotal = document.getElementById('stat-total');
    const elTepat = document.getElementById('stat-tepat');
    const elTerlambat = document.getElementById('stat-terlambat');

    if (elTotal) elTotal.textContent = total;
    if (elTepat) elTepat.textContent = tepat;
    if (elTerlambat) elTerlambat.textContent = terlambat;
}

function showToast(message, bgColor) {
    const toast = document.getElementById('toast-notification');
    const toastText = document.getElementById('toast-text');
    if (toast && toastText) {
        toastText.textContent = message;
        toast.style.background = bgColor || '#22C55E';
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3200);
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, m => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[m]));
}

// ===== MUAT INFO JADWAL LOKAL & SESI APEL =====
async function loadLocalScheduleInfo() {
    const bannerText = document.getElementById('active-schedule-text');
    if (!bannerText) return;

    try {
        const res = await fetch(`${PYTHON_API_URL}/debug/schedule`);
        const data = await res.json();
        if (data.success && data.today_schedule) {
            const sch = data.today_schedule;
            const title = sch.title || `Jadwal Apel #${sch.id}`;
            const start = String(sch.check_in_start || '').substring(0, 5);
            const late = String(sch.check_in_end || '').substring(0, 5);
            const end = String(sch.attendance_end || '').substring(0, 5);
            bannerText.innerHTML = `Jadwal Lokal Aktif: <strong>${escapeHtml(title)}</strong> (${sch.tanggal}) &bull; Buka: <strong>${start}</strong> &bull; Batas Tepat Waktu: <strong>${late}</strong>${end ? ` &bull; Tutup: <strong>${end}</strong>` : ''}`;
        } else {
            bannerText.innerHTML = `Belum ada jadwal apel hari ini di database lokal. Klik <strong>Sync Master Data</strong> atau pilih Sesi Apel pada dropdown.`;
        }
    } catch (e) {
        bannerText.textContent = 'Server Python lokal sedang memeriksa jadwal apel...';
    }
}

async function loadKegiatan() {
    const select = document.getElementById('kegiatan-select');
    if (!select) return;

    try {
        const res = await fetch(`${PYTHON_API_URL}/kegiatan`);
        if (res.ok) {
            const data = await res.json();
            select.innerHTML = '<option value="">Otomatis: Jadwal Apel Aktif Hari Ini</option>';
            if (data.success && Array.isArray(data.data)) {
                data.data.forEach(k => {
                    const opt = document.createElement('option');
                    opt.value = k.id;
                    opt.textContent = `${k.nama} (${k.tanggal_pelaksanaan})`;
                    select.appendChild(opt);
                });
            }
        }
    } catch (e) {
        console.warn('Gagal memuat daftar sesi apel lokal:', e);
    }
}

// ===== INTEGRASI ALAT SCANNER QR FISIK (USB KEYBOARD EMULATION & WEB SERIAL) =====
let barcodeBuffer = '';
let lastKeyTime = Date.now();
let serialPort = null;
let serialReader = null;

async function connectSerialScanner() {
    if (!('serial' in navigator)) {
        Swal.fire({
            title: 'Mode USB Keyboard Aktif',
            text: 'Alat Scanner QR USB standar bekerja otomatis tanpa konfigurasi. Web Serial COM hanya tersedia di Chrome/Edge.',
            icon: 'info',
            confirmButtonColor: '#2D56FF'
        });
        return;
    }

    try {
        serialPort = await navigator.serial.requestPort();
        await serialPort.open({ baudRate: 9600 });

        const txt = document.getElementById('scanner-status-text');
        if (txt) txt.textContent = 'Scanner Terhubung (Serial COM)';
        showToast('Alat Scanner QR (Serial COM) berhasil terhubung!', '#22C55E');

        readSerialData();
    } catch (err) {
        console.warn('Serial connection cancelled/failed:', err);
    }
}

async function readSerialData() {
    const decoder = new TextDecoderStream();
    serialPort.readable.pipeTo(decoder.writable);
    serialReader = decoder.readable.getReader();

    let buffer = '';
    try {
        while (true) {
            const { value, done } = await serialReader.read();
            if (done) break;
            buffer += value;

            if (buffer.includes('\n') || buffer.includes('\r')) {
                const parts = buffer.split(/[\r\n]+/);
                buffer = parts.pop() || '';
                for (const part of parts) {
                    const code = part.trim();
                    if (code) {
                        flashScannerRing();
                        recordAttendance(code);
                    }
                }
            }
        }
    } catch (err) {
        console.error('Serial read error:', err);
        const txt = document.getElementById('scanner-status-text');
        if (txt) txt.textContent = 'Scanner USB Aktif (Klik untuk Serial COM)';
    }
}

// Listener otomatis untuk Alat Scanner QR USB (Keyboard Emulation)
document.addEventListener('keydown', function (e) {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
        return;
    }

    const currentTime = Date.now();
    if (currentTime - lastKeyTime > 300) {
        barcodeBuffer = '';
    }
    lastKeyTime = currentTime;

    if (e.key === 'Enter') {
        if (barcodeBuffer.length > 0) {
            const scannedCode = barcodeBuffer.trim();
            barcodeBuffer = '';
            e.preventDefault();
            flashScannerRing();
            recordAttendance(scannedCode);
        }
    } else if (e.key.length === 1) {
        barcodeBuffer += e.key;
    }
});

// ===== INISIALISASI HALAMAN MONITOR =====
(function initMonitor() {
    setLaravelBaseUrl(getLaravelBaseUrl());
    currentAttendanceList = getLocalSyncData();
    renderRecentScans(currentAttendanceList);
    updateStats(currentAttendanceList);
    loadLocalScheduleInfo();
    loadKegiatan();
})();


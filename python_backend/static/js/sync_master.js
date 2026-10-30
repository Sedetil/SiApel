/**
 * Sync Master Data Functions — SIABSEN POLINELA
 * Menarik data master (Peserta/Mahasiswa & Jadwal Apel) dari Server Laravel Online
 * ke database MySQL lokal Python.
 */

function getLaravelBaseUrl() {
    return (localStorage.getItem('siapel_laravel_url') || 'http://127.0.0.1:8000').replace(/\/+$/, '');
}

function setLaravelBaseUrl(url) {
    const clean = (url || 'http://127.0.0.1:8000').trim().replace(/\/+$/, '');
    localStorage.setItem('siapel_laravel_url', clean);
    const label = document.getElementById('target-server-label');
    if (label) label.textContent = clean;
    return clean;
}

function showSyncMasterDataDialog() {
    const currentUrl = getLaravelBaseUrl();

    Swal.fire({
        title: 'Sinkronisasi Master Data',
        html: `
            <div style="text-align: left; margin-bottom: 16px;">
                <p style="margin-bottom: 12px; color: #6B7A90; font-size: 13px;">
                    Tarik data master terbaru dari Server Laravel (Online/Lokal) ke database MySQL lokal Python agar Alat Scanner QR dapat memvalidasi presensi secara instan:
                </p>
                <ul style="margin-left: 20px; color: #0F1729; line-height: 1.8; font-size: 13px; font-weight: 500;">
                    <li>Data Peserta / Mahasiswa & Kode QR</li>
                    <li>Jadwal Apel & Aturan Batas Keterlambatan</li>
                    <li>Daftar Sesi Kegiatan Apel Aktif</li>
                </ul>
                <div style="margin-top: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #0F1729; font-size: 13px;">
                        URL Server Laravel (Online / Lokal):
                    </label>
                    <input
                        type="text"
                        id="laravel-url-input"
                        value="${currentUrl}"
                        placeholder="http://127.0.0.1:8000 atau https://domain-kampus.ac.id"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #D9D9D9; border-radius: 8px; font-size: 14px;"
                    />
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Mulai Sinkronisasi',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#2D56FF',
        cancelButtonColor: '#6B7A90',
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            const urlInput = document.getElementById('laravel-url-input');
            const laravelUrl = setLaravelBaseUrl(urlInput ? urlInput.value : currentUrl);
            return await syncAllMasterData(laravelUrl);
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            showSyncResults(result.value);
            if (typeof loadLocalScheduleInfo === 'function') {
                loadLocalScheduleInfo();
            }
            if (typeof loadKegiatan === 'function') {
                loadKegiatan();
            }
        }
    });
}

async function syncAllMasterData(laravelUrl) {
    try {
        const response = await fetch(`/api/python/sync/all?laravel_url=${encodeURIComponent(laravelUrl)}`, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });

        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || 'Sinkronisasi gagal');
        }
        return data;
    } catch (error) {
        Swal.showValidationMessage(`Sinkronisasi gagal: ${error.message}`);
        return null;
    }
}

function showSyncResults(data) {
    if (!data.success) {
        Swal.fire({
            title: 'Sinkronisasi Gagal',
            html: `<p>${data.message}</p>`,
            icon: 'error',
            confirmButtonColor: '#FF0004'
        });
        return;
    }

    const results = data.results || {};
    let resultHtml = '<div style="text-align: left;">';

    if (results.mahasiswa) {
        const mhs = results.mahasiswa;
        resultHtml += `
            <div style="margin-bottom: 12px; padding: 12px; background: #ECFDF5; border-radius: 8px; border-left: 4px solid #22C55E;">
                <div style="font-weight: 700; color: #15803D; margin-bottom: 4px;">
                    Data Peserta / Mahasiswa
                </div>
                <div style="font-size: 13px; color: #166534;">
                    ${mhs.inserted || 0} ditambahkan, ${mhs.updated || 0} diperbarui (Total: ${mhs.total || 0})
                </div>
            </div>
        `;
    }

    if (results.schedules) {
        const sch = results.schedules;
        resultHtml += `
            <div style="margin-bottom: 12px; padding: 12px; background: #EEF2FF; border-radius: 8px; border-left: 4px solid #2D56FF;">
                <div style="font-weight: 700; color: #1B37B9; margin-bottom: 4px;">
                    Jadwal Apel & Aturan Presensi
                </div>
                <div style="font-size: 13px; color: #1E40AF;">
                    ${sch.inserted || 0} ditambahkan, ${sch.updated || 0} diperbarui (Total: ${sch.total || 0})
                </div>
            </div>
        `;
    }

    resultHtml += '</div>';

    Swal.fire({
        title: 'Sinkronisasi Berhasil!',
        html: resultHtml,
        icon: 'success',
        confirmButtonText: 'Siap Scan QR',
        confirmButtonColor: '#2D56FF'
    });
}


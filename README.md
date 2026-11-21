# OnTrack Project Intelligence

Aplikasi pendukung keputusan manajemen yang mengubah informasi proyek yang tersebar (CSV + JSON) menjadi **temuan yang dapat dijelaskan**, **prioritas perhatian (attention)**, dan **tindak lanjut yang dapat ditelusuri**, dengan penutupan masalah berbasis evidence.

Stack: PHP 8.3 · Laravel 12 · MySQL 8 · Livewire 3 (+ Alpine.js bawaan) · Tailwind CSS 3 · Chart.js · PHPUnit 11.
Tanggal acuan data (as-of): **2026-10-06**, bukan tanggal hari ini.

## 1. Interpretasi bisnis

Direktur tidak membutuhkan dashboard yang menyalin laporan PIC. Aplikasi menjawab lima pertanyaan produk: proyek apa yang berjalan, mana yang perlu perhatian dan mengapa, bukti apa yang mendukung, apa yang belum diketahui atau belum terverifikasi, serta tindakan apa yang diambil, oleh siapa, dan bagaimana statusnya.

Tiga hal yang selalu dibedakan dan ditampilkan berbeda:

| Jenis | Asal | Tampilan |
|---|---|---|
| **Dilaporkan** | kolom Project Master dan pesan Project Updates (PIC, PM, tim, vendor) | lencana 👤 berborder putus-putus |
| **Evidence operasional** | observasi sistem lain pada Production Data JSON (atau bukti PIC yang disetujui Director) | lencana 🛡 hijau |
| **Interpretasi** | kesimpulan aplikasi (temuan dan attention), selalu menaut ke evidence | panel temuan + penjelasan attention |

Prinsip kunci: *data hilang ≠ aman*, *perbedaan sumber ≠ kesalahan*, dan *tindakan selesai ≠ masalah selesai*. Skor attention adalah prioritas manajemen, bukan probabilitas proyek gagal.

Hasil pada dataset asli (6 proyek, 23 update, 12 record produksi): PRJ-001 MEDIUM 26.2 · PRJ-002 MEDIUM 48.5 · PRJ-003 MEDIUM 10.5 · PRJ-004 MEDIUM 36.3 · PRJ-005 HIGH 68.1 · PRJ-006 LOW 0.0. Angka ini keluaran aturan generik, bukan nilai yang dipaksakan.

## 2. Menjalankan secara lokal

Prasyarat: PHP ≥ 8.2 (ext: mbstring, xml, curl, zip, pdo_mysql, bcmath), Composer 2, MySQL 8 (atau MariaDB 10.6+). Node.js hanya perlu untuk membangun ulang CSS (hasil build sudah disertakan).

```bash
composer install
cp .env.example .env && php artisan key:generate

mysql -u root -e "CREATE DATABASE ontrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                  CREATE DATABASE ontrack_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                  CREATE USER 'ontrack'@'%' IDENTIFIED BY 'ontrack_local';
                  GRANT ALL ON ontrack.* TO 'ontrack'@'%'; GRANT ALL ON ontrack_test.* TO 'ontrack'@'%';"

php artisan migrate --seed      # tabel + 2 demo user + impor 3 file dataset asli
php artisan serve               # http://127.0.0.1:8000
npm install && npm run build    # opsional: rebuild Tailwind
```

Bila database sudah pernah dimigrasi sebelumnya, jalankan `php artisan migrate` lagi untuk migrasi terbaru.
Impor lewat CLI (pipeline yang sama dengan UI): `php artisan ontrack:import file1.csv file2.json --as-of="2026-10-06 23:59:59"`.

### Tes (D3)

```bash
composer test        # = vendor/bin/phpunit, memakai database MySQL `ontrack_test`
```

Hasil terakhir: **semua lulus.** Cakupan: normalisasi dan interpretasi, aturan tanpa `project_id` hard-coded, project context, alur follow-up (HTTP), RESOLVED → UNVERIFIED/VERIFIED/REOPENED, re-import tanpa duplikat, refresh yang mempertahankan follow-up, konflik evidence, data hilang, rollback batch gagal, komponen Livewire, input manual, laporan PIC, dan pengajuan bukti dengan persetujuan Director.

## 3. Arsitektur dan alur data

```
Rantai produk:  SOURCE → PROJECT CONTEXT → EVIDENCE → INTERPRETATION → ATTENTION → FOLLOW-UP

CSV / JSON / form Director / laporan PIC / bukti PIC (disetujui)
        │  semua lewat satu jalur
        ▼
DataImportService ── DataValidationService (field wajib, tanggal, angka, rentang, referensi)
        │  1 transaksi per batch (gagal = rollback penuh)
        ├─► simpan record (duplikat dilewati, perubahan = revisi, lebih lama = riwayat)
        ├─► EvidenceService       → evidence baku (Dilaporkan / Operasional), flag current/stale/duplicate/conflict
        ├─► ProjectContextService → konteks proyek (dikelompokkan lewat project_id, kronologis)
        ├─► InterpretationService → 11 aturan generik → temuan + rujukan evidence
        ├─► AttentionService      → skor 5 kriteria + level + penjelasan (kriteria tanpa data = UNKNOWN)
        └─► IssueVerificationService → verifikasi ulang follow-up RESOLVED
        ▼
Dashboard / Project Detail / Attention / Follow-up (dibaca dari data tersimpan) + ringkasan dampak sebelum–sesudah per batch
```

Struktur kode: `app/Services` (logika), `app/Services/Interpretation/Rules` (aturan), `app/Livewire` (tabel interaktif dan upload), `app/Http/{Controllers,Requests}` (tipis), `config/ontrack.php` (as-of, ambang, bobot, katalog metrik, leksikon), `config/ontrack_decisions.php` (decision log). Penjelasan rinci: `docs/ARCHITECTURE.md`, `docs/SDD.md`.

## 4. Struktur data

Skema lengkap di `docs/DATA_DICTIONARY.md`. Ringkasan:

| Kelompok | Tabel | Catatan |
|---|---|---|
| Sumber | `projects`, `project_updates`, `production_records` | `revision` + `is_current`; `fingerprint` untuk deteksi duplikat; payload produksi JSON karena skema berbeda per sistem |
| Kualitas data | `data_import_batches`, `data_validation_errors` | per file/input; status, hitungan, dampak sebelum–sesudah |
| Evidence | `evidence_records` | baku: kelas, kategori, metrik, subjek, nilai, `source_ref`, timestamp asli, revisi, flag |
| Intelijen | `findings`, `finding_evidence`, `attention_assessments` | temuan punya identitas stabil (`rule_key` + subjek) sehingga follow-up tetap terhubung setelah refresh |
| Tindak lanjut | `follow_ups`, `follow_up_histories`, `issue_verifications`, `evidence_submissions` | status tindakan terpisah dari status issue; riwayat dan verifikasi append-only |
| Lain | `activity_logs`, `users`, `settings` | as-of date di `settings` |

Relasi inti: `projects 1─* project_updates / production_records / evidence_records / findings / follow_ups`; `findings *─* evidence_records`; `findings 1─* follow_ups`; `findings 1─* issue_verifications`.

## 5. Antarmuka HTTP utama (pengganti "API")

Aplikasi berbasis halaman server-side (Blade + Livewire), tanpa JSON API terpisah. Rute utamanya:

| Method + path | Fungsi | Akses |
|---|---|---|
| `GET /dashboard`, `GET /projects` | Portofolio, KPI, grafik, filter/cari/urut | semua |
| `GET /projects/{id}` | Detail: master, timeline, evidence, temuan, attention, follow-up | semua |
| `GET /projects/{id}/attention`, `GET /attention` | Penjelasan attention per proyek / semua | semua |
| `GET/POST /projects/create`, `PUT/DELETE /projects/{id}` | Tambah, ubah, hapus proyek | Director |
| `GET/POST /projects/{id}/report` | Laporan PIC → timeline (Dilaporkan) | PIC proyek, Director |
| `GET/POST /projects/{id}/evidence…` | Kirim bukti operasional | PIC proyek, Director |
| `GET /evidence-submissions`, `POST …/{id}/approve|reject` | Tinjauan bukti | lihat: PIC (proyeknya) / Director; putuskan: Director |
| `GET /follow-ups`, `GET /follow-ups/{id}` | Daftar dan detail (PM hanya melihat miliknya di daftar) | semua |
| `POST /follow-ups` | Buat follow-up (judul, proyek, temuan, PIC, prioritas, due date) | Director |
| `PUT /follow-ups/{id}`, `POST …/status`, `…/notes`, `…/verify` | Ubah, status, catatan, verifikasi ulang | PIC yang ditugaskan, Director |
| `GET /data-management`, `…/imports`, `…/imports/{id}` | Upload (Livewire), riwayat, detail + dampak + laporan kualitas | lihat: semua; upload/hitung ulang: Director |
| `GET /activity`, `GET/POST /settings` | Riwayat aktivitas; as-of, ambang, decision log | lihat: semua; ubah: Director |

Semua form memakai Form Request (validasi + `authorize`) dan proteksi CSRF.

## 6. Perilaku pemrosesan data (P5, P6)

* **Duplikat identik** (fingerprint sama): dilewati, dihitung dan dicatat (`DUPLICATE_RECORD`). Re-import file yang sama tidak menambah baris.
* **ID sama, isi berbeda**: disimpan sebagai revisi baru; versi lama tetap ada (`is_current = false`) dan tampil sebagai riwayat; ditandai `REVISED_RECORD`.
* **Record lebih lama dari yang tersimpan**: disimpan sebagai riwayat dan tidak menjadi nilai terkini (`OLDER_RECORD`).
* **Dua sumber berbeda, nilai berbeda**: dipertahankan keduanya; ditandai *Conflicting* bila berselisih pada instan yang sama atau dari sistem berbeda dalam jendela 24 jam. Perubahan status dalam sistem yang sama (mis. scanner OUT_OF_SERVICE lalu LIMITED) dibaca sebagai riwayat, bukan konflik.
* **Field hilang / nilai tidak valid**: record ditolak dan dilaporkan dengan kode, field, dan pesan; field opsional kosong menjadi peringatan; tidak pernah diubah menjadi 0 atau "sukses".
* **Kegagalan saat menyimpan**: seluruh batch di-rollback dan ditandai `FAILED`; data valid sebelumnya tidak berubah.
* Setiap batch menyimpan **dampak sebelum–sesudah** (level/skor dan temuan yang dibuka, ditutup, atau berubah).

## 7. Perjalanan pengguna

* **Director**: Overview → proyek bernilai HIGH → Project Detail → periksa evidence → Attention Analysis → *Buat follow-up* dari temuan → tetapkan PIC → pantau. Mengunggah data dan menyetujui bukti.
* **Project Manager**: hanya melihat/mengubah follow-up yang ditugaskan kepadanya → status & catatan → **RESOLVED** → sistem mengevaluasi evidence → issue `VERIFIED`, `UNVERIFIED`, atau `REOPENED`. Dapat melapor update dan mengirim bukti untuk proyeknya.
* **Verifikasi issue**: RESOLVED hanya berarti tindakan selesai. Issue baru VERIFIED bila ada evidence operasional yang lebih baru dari baseline temuan, cukup segar, tidak saling bertentangan, dan aturan asal tidak lagi mendeteksi kondisinya. Laporan manusia tidak pernah cukup.
* Pemilih *demo user* ada di header.

## 8. Role dan hak akses pada produk nyata

**Yang ada sekarang (demo).** Dua demo user tanpa login (Director, Project Manager). Pembatasan *tulis* sudah ditegakkan di server: pembuatan follow-up/proyek, unggah data, pengaturan, dan persetujuan bukti hanya Director; PM hanya mengubah follow-up yang PIC-nya sama dengan `pic_name`-nya serta melapor/mengirim bukti untuk proyek yang ia pimpin. Akses *baca* masih terbuka untuk kedua demo user. PIC hanyalah nama/tim pada follow-up, bukan akun.

**Rancangan untuk produk nyata.**

| Aspek | Rancangan |
|---|---|
| Autentikasi | SSO perusahaan (OIDC/SAML), MFA dari IdP, sesi dengan masa berlaku; akun layanan untuk integrasi sumber data |
| Peran | *Director/Management* (portofolio, menugaskan, menyetujui bukti) · *Project Manager* (proyek yang ia kelola) · *PIC/Contributor* (hanya tindakan yang ditugaskan, melapor) · *Data Steward* (impor, katalog metrik, ambang) · *Auditor/Read-only* · *System* (service account ingest) |
| Cakupan data | Keanggotaan proyek (`project_members`: user, proyek, peran) dan cakupan portofolio; kueri selalu dibatasi pada proyek yang boleh dilihat, bukan hanya disembunyikan di UI |
| Otorisasi | Laravel Policies/Gates per model; titik yang kini dipusatkan (`FollowUpService::canModify`, `ProjectReportService::canReport`, `EvidenceSubmissionService::canSubmit`, `authorize()` pada Form Request) diganti menjadi Policy tanpa mengubah alur |
| Pemisahan tugas | Pengaju bukti ≠ penyetuju; Director yang menginput bukti sendiri untuk menutup issue CRITICAL memerlukan penyetuju kedua; PIC tidak dapat memverifikasi pekerjaannya sendiri (sudah berlaku untuk laporan) |
| Konfigurasi sensitif | Ambang, bobot, katalog metrik, dan as-of hanya Data Steward/Director, dengan riwayat perubahan dan alasan |
| Audit | Riwayat follow-up, verifikasi, import, dan persetujuan sudah append-only; ditambah IP/aktor otentik dan retensi |
| Integrasi | Token berskala minimum untuk pengiriman data otomatis, menggantikan unggahan manual |

## 9. Asumsi

1. As-of = 2026-10-06 (dapat diubah di Settings, tercatat per batch); aplikasi tidak memakai tanggal hari ini.
2. Timestamp sumber tanpa zona waktu dibaca sebagai waktu lokal aplikasi (Asia/Jakarta).
3. File CSV/JSON adalah input otoritatif; workbook Excel hanya referensi.
4. Ambang batas (usang 72 jam, toleransi progress 3 poin, jendela konflik 24 jam, dll.), bobot, dan nilai status "sehat" adalah keputusan desain yang dapat dikonfigurasi, bukan fakta data. `CONFIGURED` sebagai nilai sehat koneksi tidak muncul pada dataset dan merupakan asumsi.
5. Progress observed: metrik kuantitas = nilai ÷ target; metrik bertahap memakai tahap paling hilir (development → konfigurasi → teruji di production).
6. Update teks dibaca dengan leksikon sederhana berbahasa Indonesia; hasilnya hanya menjadi bahan temuan "dilaporkan" dan tidak menentukan skor sendirian.
7. Dataset sintetis; tidak ada data tambahan yang ditebak.

## 10. Keterbatasan yang diketahui

* Tanpa autentikasi nyata; akses baca terbuka (lihat bagian 8).
* Pemrosesan sinkron dalam satu request: pembaruan massal (ratusan proyek sekaligus) memakan beberapa detik hingga puluhan detik (lihat `docs/Jawaban_Refleksi.pdf`, pertanyaan 3).
* Dashboard menghitung grafik progress dari konteks semua proyek; ini akan menjadi hambatan pada riwayat evidence yang besar.
* Leksikon sinyal teks sederhana (negasi dan sarkasme dapat salah dibaca).
* Bukti PIC berupa keterangan teks; belum ada unggah file lampiran dan persetujuan hanya satu tingkat.
* Definisi konflik dan bobot attention belum dikalibrasi dengan umpan balik manajemen sungguhan; skor PRJ-005 (54,5 setelah skenario perubahan) tepat di bawah ambang HIGH (55).
* Tidak ada notifikasi, forecasting, integrasi AI, maupun JSON API terpisah (di luar lingkup brief).

## 11. Prioritas, trade-off, dan langkah berikutnya

**Prioritas pengerjaan** (sesuai brief): (1) kebenaran pemrosesan data, keterlacakan evidence, alur follow-up; (2) persistence, tes, UX jelas; (3) visual dan fitur tambahan.

**Trade-off utama**: skor tertimbang transparan; satu jalur pemrosesan untuk semua input (konsisten, tetapi sinkron); evidence append-only (jejak lengkap, tetapi tabel membesar); bukti PIC hanya dihitung setelah disetujui (aman, tetapi menambah langkah).

**Langkah berikutnya (berurutan)**: (1) kalibrasi ambang/bobot dan tampilan *what-if* bersama manajemen; (2) pemrosesan antrian + hitung ulang bertahap + ringkasan untuk dashboard; (3) autentikasi SSO + RBAC berbasis keanggotaan proyek; (4) lampiran bukti dan persetujuan berjenjang; (5) arsip evidence lama; (6) notifikasi dan JSON API untuk integrasi.

## 12. Jawaban lima pertanyaan refleksi (D6)

Lihat `docs/D6_Jawaban_Refleksi.docx`.

## 13. Alokasi waktu (diisi oleh kandidat)

Review brief	30 menit
Analisis permasalahan	45 menit
Desain sistem	45 menit
Implementasi sistem (coding)	150 menit
Evaluasi sistem	30 menit
Penyusunan PPT	30 menit
Total	330 menit (5 jam 30 menit)

## Struktur repositori

```
app/Services/  app/Livewire/  app/Http/  app/Models/     config/ontrack.php, ontrack_decisions.php
database/migrations/ (16 tabel)  database/data/  database/seeders/
resources/views/  public/ (CSS hasil build)  tests/  routes/web.php
docs/
```

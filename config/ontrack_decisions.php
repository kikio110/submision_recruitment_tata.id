<?php

/*
 | Decision log — single source for the Settings page and docs/CHANGE_READINESS.md.
 | Each decision states why it was chosen, how uncertainty is handled, and when it must be revisited.
 */
return [
    'threshold_labels' => [
        'stale_hours' => ['Batas evidence usang', 'jam sebelum tanggal acuan'],
        'progress_tolerance_pct' => ['Toleransi selisih progress', 'poin persen antara laporan dan observasi'],
        'internal_pct_tolerance' => ['Toleransi konsistensi master', 'poin persen antara progress dan aktual/target'],
        'time_gap_hours' => ['Selisih waktu observasi yang menurunkan keyakinan', 'jam'],
        'conflict_window_hours' => ['Jendela konflik antar sumber', 'jam'],
        'schedule_gap_pct' => ['Selisih jadwal sebelum dianggap tertinggal', 'poin persen'],
        'pending_share_pct' => ['Porsi menunggu verifikasi yang dianggap relevan', 'persen dari total'],
        'failed_login_ratio' => ['Rasio login gagal yang dianggap sistemik', 'pecahan (0,2 = 20%)'],
        'reported_issue_days' => ['Jendela kendala yang dilaporkan', 'hari sebelum tanggal acuan'],
    ],

    'decisions' => [
        [
            'topic' => 'Cara menentukan prioritas perhatian',
            'decision' => 'Skor tertimbang dari lima kriteria (Deadline Pressure, Operational Blockage, Progress Deviation, Evidence Reliability, Data Quality) ditambah aturan lantai level: temuan CRITICAL → HIGH, temuan HIGH/MEDIUM → minimal MEDIUM.',
            'why' => 'Rumus yang dapat dibaca dan dijelaskan kepada manajemen tanpa kotak hitam; setiap poin menaut ke temuan dan evidence. Aturan lantai mencegah satu kriteria yang ringan menutupi masalah serius.',
            'uncertainty' => 'Skor adalah prioritas manajemen, bukan peluang proyek gagal. Kriteria tanpa data ditandai UNKNOWN, tidak masuk rata-rata, dan menurunkan tingkat keyakinan.',
            'review' => 'Tinjau bobot dan ambang bila manajemen merasa urutan prioritas tidak sesuai kenyataan, bila ada kriteria baru, atau setelah cukup banyak proyek untuk membandingkan skor dengan hasil akhir.',
        ],
        [
            'topic' => 'Ketidakpastian dan data yang hilang',
            'decision' => 'Data hilang menghasilkan ketidakpastian yang tampil eksplisit: kriteria UNKNOWN, temuan “evidence tidak cukup”, atau progress observed “n/a” — tidak pernah nol, “aman”, atau “selesai”.',
            'why' => 'Menganggap kosong sebagai aman membuat proyek tanpa data tampak paling sehat. Itu kesalahan yang paling merugikan bagi pengambil keputusan.',
            'uncertainty' => 'Level tidak boleh LOW bila keyakinan data di bawah 60%. Setiap penjelasan menyebut kriteria mana yang tidak diketahui.',
            'review' => 'Tinjau bila sebuah sumber data memang secara resmi mengosongkan field tertentu (mis. nilai “tidak berlaku”) sehingga kosong punya arti yang pasti.',
        ],
        [
            'topic' => 'Perbedaan antar sumber bukan otomatis kesalahan',
            'decision' => 'Laporan manusia dan observasi sistem disimpan berdampingan dengan kelas berbeda (Dilaporkan vs Operasional). Selisih progress dinilai dengan toleransi dan memperhitungkan jarak waktu observasi; selisih yang waktunya berjauhan diturunkan severity-nya dan diberi catatan. Nilai berbeda dari sumber berbeda dalam jendela waktu pendek ditandai Conflicting, kedua nilai dipertahankan.',
            'why' => 'Angka berbeda sering berarti definisi atau waktu ukur berbeda (mis. terverifikasi vs menunggu cek, snapshot malam vs laporan pagi), bukan salah satunya keliru.',
            'uncertainty' => 'Konflik ditampilkan sebagai temuan “perlu verifikasi”, bukan memilih pemenang. Perubahan status dalam sumber yang sama (mis. OUT_OF_SERVICE lalu LIMITED) dibaca sebagai riwayat, bukan konflik.',
            'review' => 'Tinjau toleransi, jendela konflik, dan definisi “sumber berbeda” bila ada sumber baru dengan frekuensi atau cara ukur yang berbeda jauh.',
        ],
        [
            'topic' => 'Model data: evidence terstandar yang hanya bertambah',
            'decision' => 'Semua sumber (master, update, produksi, laporan PIC) dibakukan menjadi evidence dengan sumber, rujukan asal, timestamp asli, nilai, dan revisi. Isi yang berubah menjadi revisi baru; versi lama tetap ada dan ditandai riwayat. Record yang lebih lama dari yang tersimpan menjadi riwayat, bukan data terbaru.',
            'why' => 'Input baru atau berubah tidak membutuhkan aturan baru, dan setiap kesimpulan dapat ditelusuri ke asalnya. Mengganti data lama akan menghapus jejak yang dibutuhkan untuk menjelaskan perubahan.',
            'uncertainty' => 'Kolom produksi baru yang belum dikenal tetap diserap sebagai evidence kategori “lainnya” dan terlihat di timeline, hanya belum dipakai aturan sampai didaftarkan.',
            'review' => 'Tinjau bila volume data membuat riwayat terlalu besar (perlu pengarsipan) atau bila sumber mengirim koreksi retroaktif secara rutin.',
        ],
        [
            'topic' => 'Tanggal acuan (as-of), bukan jam sistem',
            'decision' => 'Seluruh interpretasi memakai tanggal acuan yang tercatat pada setiap import dan dapat diubah di Settings. Mengubahnya menghitung ulang semua analisis.',
            'why' => 'Hasil harus dapat diulang dan dibandingkan; data sintetis bertanggal 2026-10-06 tidak boleh terlihat “usang” hanya karena jam sistem berbeda.',
            'uncertainty' => 'Timestamp sumber tanpa zona waktu dibaca sebagai waktu lokal aplikasi (Asia/Jakarta); asumsi ini dicatat.',
            'review' => 'Tinjau saat aplikasi dipakai dengan data langsung: tanggal acuan sebaiknya mengikuti waktu import terakhir yang valid, dan zona waktu per sumber perlu eksplisit.',
        ],
        [
            'topic' => 'Siapa boleh menyatakan masalah selesai',
            'decision' => 'Status tindakan (RESOLVED) dipisah dari status issue (VERIFIED/UNVERIFIED/REOPENED). Issue hanya VERIFIED bila ada evidence operasional yang lebih baru dari baseline, cukup segar, tidak saling bertentangan, dan aturan asal tidak lagi mendeteksi kondisinya. Laporan PIC/PM selalu tercatat sebagai “dilaporkan” dan tidak dapat memverifikasi. PIC boleh mengirim bukti operasional, tetapi hanya yang disetujui Director yang dihitung.',
            'why' => 'Penutupan tugas adalah klaim manusia; pemulihan kondisi adalah fakta yang diamati. Menyamakan keduanya menghasilkan dashboard hijau yang salah.',
            'uncertainty' => 'Tidak ada evidence baru → UNVERIFIED dengan alasan tertulis. Evidence baru yang menunjukkan masalah masih ada → REOPENED. Hasil disimpan sebagai riwayat dan dapat diulang (Verify again).',
            'review' => 'Tinjau bila ada sumber operasional yang sangat jarang memperbarui data (verifikasi akan selalu tertunda), atau bila manajemen ingin menerima pengesahan manual dari peran tertentu dengan jejak audit.',
        ],
        [
            'topic' => 'Aturan generik tanpa kondisi per proyek',
            'decision' => 'Aturan membaca kategori evidence, metrik, dan ambang dari konfigurasi; tidak ada ID proyek di kode aturan (diuji otomatis). Proyek baru, kolom baru, atau nilai status baru diproses oleh aturan yang sama.',
            'why' => 'Logika per proyek tidak dapat diskalakan dan tidak dapat dijelaskan; perubahan data tidak boleh menuntut perubahan kode.',
            'uncertainty' => 'Nilai status yang belum dikenal diperlakukan sebagai tidak sehat dengan severity sedang (bukan diabaikan), dan nilai “sehat” per atribut adalah asumsi terdokumentasi di konfigurasi.',
            'review' => 'Tinjau katalog metrik dan daftar nilai sehat setiap ada sumber atau jenis proyek baru; tambahkan kelas aturan baru hanya bila kategori kondisinya memang baru.',
        ],
        [
            'topic' => 'Pengiriman bukti operasional oleh PIC',
            'decision' => 'PIC/PM mengajukan observasi terstruktur (apa yang diamati, nilai, waktu observasi, sistem asal, dan asal bukti). Pengajuan berstatus menunggu dan tidak masuk interpretasi maupun verifikasi sampai Director menyetujuinya; penolakan wajib beralasan. Yang disetujui masuk lewat jalur yang sama dengan file produksi dan membawa jejak pengaju serta penyetuju. Bukti yang dikirim Director langsung disetujui.',
            'why' => 'PIC adalah pihak yang paling dekat dengan sistem, tetapi juga pihak yang ingin pekerjaannya dinyatakan selesai. Pemisahan pengaju dan penyetuju mencegah PIC memverifikasi pekerjaannya sendiri, sementara PIC tidak harus menunggu unggahan file dari orang lain.',
            'uncertainty' => 'Waktu observasi tidak boleh melewati tanggal acuan; bukti yang bertentangan dengan sumber lain tetap ditandai konflik setelah disetujui; bukti usang tidak cukup untuk VERIFIED. Penyetuju menilai kewajaran, bukan kebenaran mutlak, sehingga verifikasi tetap dievaluasi ulang oleh aturan.',
            'review' => 'Tinjau bila antrean persetujuan menjadi hambatan (mis. beri hak persetujuan ke peran lain atau sumber tepercaya), bila perlu lampiran file bukti, atau bila ada integrasi langsung ke sistem sumber yang membuat pengajuan manual tidak diperlukan.',
        ],
        [
            'topic' => 'Pelaporan PIC dan penutupan follow-up',
            'decision' => 'PIC/PM melapor lewat form (atau dari catatan follow-up). Laporan masuk lewat jalur yang sama dengan impor file, sehingga timeline, evidence, temuan, dan attention diperbarui otomatis, dan dampaknya tercatat sebelum-sesudah.',
            'why' => 'Satu jalur memastikan perilaku yang sama untuk input manual dan file, dan setiap perubahan analisis dapat dijelaskan.',
            'uncertainty' => 'Laporan yang tidak didukung observasi sistem tetap ditampilkan sebagai laporan; ia dapat menimbulkan selisih terhadap observasi yang kemudian diberi tanda, bukan dihapus.',
            'review' => 'Tinjau bila PIC perlu melampirkan bukti (foto, dokumen) atau bila perlu persetujuan atasan sebelum laporan mengubah progres.',
        ],
    ],
];

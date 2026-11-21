<?php

/*
 | OnTrack Project Intelligence — design decisions (SRS A-04: thresholds are
 | configurable design choices, not facts from the data).
 */
return [
    // Dataset reference date (SRS A-01). Overridable at runtime from Settings.
    'as_of' => env('ONTRACK_AS_OF', '2026-10-06 23:59:59'),

    'upload' => ['max_kb' => 5120, 'extensions' => ['csv', 'json', 'txt']],

    'thresholds' => [
        'stale_hours' => 72,              // evidence older than this vs as-of is stale
        'progress_tolerance_pct' => 3.0,  // |reported - observed| beyond this is a discrepancy
        'internal_pct_tolerance' => 1.0,  // reported pct vs reported_actual/target
        'time_gap_hours' => 6,            // differing observation times lower confidence
        'conflict_window_hours' => 24,    // same metric, different values inside window
        'schedule_gap_pct' => 5.0,        // elapsed% - progress% before schedule deviation
        'pending_share_pct' => 3.0,       // pending quantity share worth flagging
        'failed_login_ratio' => 0.2,      // share of failed logins considered systemic
        'reported_issue_days' => 3,       // window for reported blocker signals
    ],

    // Attention criteria weights (sum = 1). Score = weighted mean of KNOWN criteria.
    'weights' => [
        'deadline_pressure' => 0.25,
        'operational_blockage' => 0.25,
        'progress_deviation' => 0.20,
        'evidence_reliability' => 0.15,
        'data_quality' => 0.15,
    ],
    'levels' => ['HIGH' => 55, 'MEDIUM' => 30],
    'severity_points' => ['CRITICAL' => 100, 'HIGH' => 80, 'MEDIUM' => 50, 'LOW' => 20, 'INFO' => 0],

    // Statuses considered healthy per attribute (SRS A-05).
    'healthy' => [
        'scanner_status' => ['RUNNING'],
        'connection_status' => ['CONNECTED', 'CONFIGURED', 'ONLINE'],
        'application_status' => ['ONLINE', 'RUNNING'],
        'deployment_status' => ['COMPLETED', 'DEPLOYED'],
    ],

    // Metric catalogue: how operational payload fields become standard evidence.
    // kind: quantity (progress numerator), stage (ordered pipeline), status, quality, usage
    'metrics' => [
        'verified_quantity' => ['kind' => 'quantity', 'category' => 'PRODUCTION_OUTPUT', 'label' => 'Kuantitas terverifikasi'],
        'pending_internal_check_quantity' => ['kind' => 'pending', 'category' => 'PRODUCTION_OUTPUT', 'label' => 'Kuantitas menunggu cek internal'],
        'pages_processed_total' => ['kind' => 'quantity', 'category' => 'PRODUCTION_OUTPUT', 'label' => 'Halaman diproses (kumulatif)'],
        'processed_boxes' => ['kind' => 'quantity', 'category' => 'PRODUCTION_OUTPUT', 'label' => 'Box diproses'],
        'records_verified' => ['kind' => 'quantity', 'category' => 'PRODUCTION_OUTPUT', 'label' => 'Record terverifikasi'],
        'records_skipped_missing_region' => ['kind' => 'quality', 'category' => 'DATA_QUALITY', 'label' => 'Record dilewati (wilayah kosong)'],
        'developed_endpoints' => ['kind' => 'stage', 'stage' => 1, 'category' => 'INTEGRATION', 'label' => 'Endpoint development selesai'],
        'configured_endpoints' => ['kind' => 'stage', 'stage' => 2, 'category' => 'INTEGRATION', 'label' => 'Endpoint terkonfigurasi'],
        'production_tested_endpoints' => ['kind' => 'stage', 'stage' => 3, 'category' => 'INTEGRATION', 'label' => 'Endpoint teruji di production'],
        'target_endpoints' => ['kind' => 'target', 'category' => 'INTEGRATION', 'label' => 'Target endpoint'],
        'connection_status' => ['kind' => 'status', 'category' => 'ENVIRONMENT', 'label' => 'Status koneksi'],
        'application_status' => ['kind' => 'status', 'category' => 'DEPLOYMENT', 'label' => 'Status aplikasi'],
        'deployment_status' => ['kind' => 'status', 'category' => 'DEPLOYMENT', 'label' => 'Status deployment'],
        'active_users_last_24h' => ['kind' => 'usage', 'category' => 'USAGE', 'label' => 'User aktif 24 jam'],
        'successful_logins_last_24h' => ['kind' => 'usage', 'category' => 'USAGE', 'label' => 'Login berhasil 24 jam'],
        'failed_logins_last_24h' => ['kind' => 'usage', 'category' => 'USAGE', 'label' => 'Login gagal 24 jam'],
    ],
    // Fields that are never evidence values themselves.
    'meta_fields' => ['source_record_id', 'project_id', 'source_system', 'record_type', 'timestamp', 'unit', 'workflow_state', 'environment', 'scanners', 'last_successful_connection', 'submission_id', 'submitted_by', 'approved_by', 'source_note'],

    // Lexicon turning free-text updates into signals (Bahasa Indonesia).
    'lexicon' => [
        'BLOCKER' => ['tidak dapat', 'tidak bisa', 'belum dapat', 'belum bisa', 'error', 'kendala teknis yang', 'mengalami', 'belum tersedia'],
        'WAITING' => ['menunggu', 'belum diterima', 'follow up', 'koordinasi kembali'],
        'HOLD' => ['hold', 'ditahan', 'dilewati'],
        'QUALITY' => ['dicek kembali', 'perbedaan interpretasi', 'revisi', 'kosong'],
        'RECOVERY' => ['dapat digunakan kembali', 'dipakai kembali', 'berhasil login', 'sudah normal', 'pulih'],
        'NO_ISSUE' => ['tidak ada kendala', 'berjalan normal', 'sesuai rencana', 'seperti biasa', 'berjalan sesuai'],
    ],
];

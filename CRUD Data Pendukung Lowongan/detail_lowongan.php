<?php
require_once 'config.php';

// Simulasi login mahasiswa (Menggunakan ID 3 sesuai data dummy Bayu)
$mahasiswa_id = 3; 

// Ambil ID lowongan dari URL
$lowongan_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query ambil data detail lowongan dan perusahaannya
$query = "SELECT l.*, p.nama_perusahaan, p.logo, p.alamat AS alamat_perusahaan 
          FROM lowongan l 
          JOIN perusahaan p ON l.perusahaan_id = p.id 
          WHERE l.id = :lowongan_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['lowongan_id' => $lowongan_id]);
$lowongan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lowongan) {
    die("Lowongan tidak ditemukan.");
}

// --- LOGIKA PROSES KIRIM LAMARAN ---
$pesan = "";
$tipe_pesan = "";

if (isset($_POST['lamar_sekarang'])) {
    // 1. Cek apakah mahasiswa sudah pernah melamar di lowongan ini sebelumnya
    $cek_query = "SELECT COUNT(*) FROM lamaran WHERE mahasiswa_id = :mhs_id AND lowongan_id = :lowongan_id";
    $cek_stmt = $pdo->prepare($cek_query);
    $cek_stmt->execute(['mhs_id' => $mahasiswa_id, 'lowongan_id' => $lowongan_id]);
    
    if ($cek_stmt->fetchColumn() > 0) {
        $pesan = "Kamu sudah pernah mengirimkan lamaran ke lowongan ini!";
        $tipe_pesan = "danger";
    } else {
        // 2. Jika belum, masukkan data baru ke tabel lamaran dengan status default 'pending'
        $insert_query = "INSERT INTO lamaran (mahasiswa_id, lowongan_id, tanggal_lamaran, status) 
                         VALUES (:mhs_id, :lowongan_id, :tanggal, 'pending')";
        $insert_stmt = $pdo->prepare($insert_query);
        $sukses = $insert_stmt->execute([
            'mhs_id'      => $mahasiswa_id,
            'lowongan_id' => $lowongan_id,
            'tanggal'     => date('Y-m-d')
        ]);
        
        if ($sukses) {
            $pesan = "Berhasil! Lamaran kamu telah dikirim ke perusahaan.";
            $tipe_pesan = "success";
        } else {
            $pesan = "Gagal mengirim lamaran. Silakan coba lagi.";
            $tipe_pesan = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Lowongan - <?= htmlspecialchars($lowongan['judul']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <a href="dashboard_mahasiswa.php" class="btn btn-secondary mb-4">&larr; Kembali ke Dashboard</a>

            <?php if (!empty($pesan)): ?>
                <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show" role="alert">
                    <?= $pesan ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary text-white rounded p-3 me-3 fw-bold">LOGO</div>
                    <div>
                        <h2 class="fw-bold mb-0"><?= htmlspecialchars($lowongan['judul']) ?></h2>
                        <p class="text-muted mb-0"><?= htmlspecialchars($lowongan['nama_perusahaan']) ?> &bull; <?= htmlspecialchars($lowongan['lokasi']) ?></p>
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-dark">Deskripsi Pekerjaan</h5>
                <p class="text-secondary"><?= nl2br(htmlspecialchars($lowongan['deskripsi'])) ?></p>

                <div class="row my-4 bg-light p-3 rounded mx-0">
                    <div class="col-6 col-sm-3 mb-2">
                        <small class="text-muted d-block">Durasi</small>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($lowongan['durasi']) ?></span>
                    </div>
                    <div class="col-6 col-sm-3 mb-2">
                        <small class="text-muted d-block">Kuota</small>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($lowongan['kuota']) ?> Orang</span>
                    </div>
                    <div class="col-6 col-sm-3 mb-2">
                        <small class="text-muted d-block">Batas Pendaftaran</small>
                        <span class="fw-semibold text-danger"><?= htmlspecialchars($lowongan['batas_pendaftaran']) ?></span>
                    </div>
                    <div class="col-6 col-sm-3 mb-2">
                        <small class="text-muted d-block">Alamat Perusahaan</small>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($lowongan['alamat_perusahaan']) ?></span>
                    </div>
                </div>

                <form action="" method="POST" class="d-grid mt-3">
                    <button type="submit" name="lamar_sekarang" class="btn btn-primary btn-lg fw-bold">
                        🚀 Lamar Magang Sekarang
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
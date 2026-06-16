<?php
require_once 'config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT l.*, p.nama_perusahaan, p.industri, p.alamat, p.deskripsi AS deskripsi_perusahaan, p.website, p.logo 
        FROM lowongan l
        JOIN perusahaan p ON l.perusahaan_id = p.id
        WHERE l.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$detail = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$detail) {
    die("<div class='container mt-5'><h3>Lowongan tidak ditemukan.</h3><a href='dashboard_mahasiswa.php'>Kembali</a></div>");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail: <?= htmlspecialchars($detail['judul']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    <a href="dashboard_mahasiswa.php" class="btn btn-secondary mb-3">&larr; Kembali ke Dashboard</a>
    
    <div class="card shadow border-0">
        <div class="card-body p-5">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="text-primary fw-bold"><?= htmlspecialchars($detail['judul']) ?></h2>
                    <h4 class="text-secondary"><?= htmlspecialchars($detail['nama_perusahaan']) ?></h4>
                    <span class="badge bg-info text-dark mb-4"><?= htmlspecialchars($detail['industri']) ?></span>
                    
                    <h5 class="fw-bold">Deskripsi Pekerjaan</h5>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($detail['deskripsi'])) ?></p>
                    
                    <h5 class="fw-bold mt-4">Informasi Lowongan</h5>
                    <table class="table table-sm table-borderless text-muted">
                        <tr><td width="30%"><strong>Lokasi</strong></td><td>: <?= htmlspecialchars($detail['lokasi']) ?></td></tr>
                        <tr><td><strong>Durasi</strong></td><td>: <?= htmlspecialchars($detail['durasi']) ?></td></tr>
                        <tr><td><strong>Kuota Tersedia</strong></td><td>: <?= htmlspecialchars($detail['kuota']) ?> mahasiswa</td></tr>
                        <tr><td><strong>Batas Pendaftaran</strong></td><td class="text-danger">: <?= date('d F Y', strtotime($detail['batas_pendaftaran'])) ?></td></tr>
                    </table>

                    <h5 class="fw-bold mt-4">Tentang Perusahaan</h5>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($detail['deskripsi_perusahaan'])) ?></p>
                    <p class="text-muted"><strong>Alamat:</strong> <?= htmlspecialchars($detail['alamat']) ?></p>
                </div>
                
                <div class="col-md-4 text-center border-start pt-3">
                    <p class="text-muted small">Website Resmi: <br> <a href="<?= htmlspecialchars($detail['website']) ?>" target="_blank"><?= htmlspecialchars($detail['website']) ?></a></p>
                    <hr>
                    <button class="btn btn-success btn-lg w-100 shadow-sm mt-3">Lamar Posisi Ini</button>
                    <p class="small text-muted mt-2">Pastikan CV dan Portfolio Anda sudah diperbarui di profil.</p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
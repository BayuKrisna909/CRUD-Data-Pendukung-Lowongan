<?php
require_once 'config.php';

// Menangkap parameter Pencarian dan Filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$lokasi = isset($_GET['lokasi']) ? trim($_GET['lokasi']) : '';

// Mengambil Data Lokasi untuk Dropdown Filter
$stmt_lokasi = $pdo->query("SELECT DISTINCT lokasi FROM lowongan WHERE lokasi != '' ORDER BY lokasi ASC");
$list_lokasi = $stmt_lokasi->fetchAll(PDO::FETCH_COLUMN);

// Query Utama: Menampilkan lowongan beserta data pendukung perusahaan
$sql = "SELECT l.*, p.nama_perusahaan, p.industri, p.logo 
        FROM lowongan l
        JOIN perusahaan p ON l.perusahaan_id = p.id
        WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (l.judul LIKE :search OR p.nama_perusahaan LIKE :search)";
    $params[':search'] = "%$search%";
}
if (!empty($lokasi)) {
    $sql .= " AND l.lokasi = :lokasi";
    $params[':lokasi'] = $lokasi;
}

$sql .= " ORDER BY l.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$lowongan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4 font-weight-bold">Eksplorasi Lowongan Magang & Kerja</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Cari posisi atau nama perusahaan..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-5">
                    <select name="lokasi" class="form-select">
                        <option value="">Semua Lokasi</option>
                        <?php foreach ($list_lokasi as $loc): ?>
                            <option value="<?= htmlspecialchars($loc) ?>" <?= ($lokasi == $loc) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($loc) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <?php if (count($lowongan) > 0): ?>
            <?php foreach ($lowongan as $row): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <span class="badge bg-secondary mb-2"><?= htmlspecialchars($row['industri']) ?></span>
                            <h5 class="card-title text-primary font-weight-bold"><?= htmlspecialchars($row['judul']) ?></h5>
                            <h6 class="card-subtitle mb-3 text-muted"><?= htmlspecialchars($row['nama_perusahaan']) ?></h6>
                            
                            <ul class="list-unstyled small mb-3">
                                <li>📍 <strong>Lokasi:</strong> <?= htmlspecialchars($row['lokasi']) ?></li>
                                <li>⏱️ <strong>Durasi:</strong> <?= htmlspecialchars($row['durasi']) ?></li>
                                <li>👥 <strong>Kuota:</strong> <?= htmlspecialchars($row['kuota']) ?> Orang</li>
                            </ul>
                            
                            <p class="card-text text-danger small"><strong>Batas Daftar:</strong> <?= date('d M Y', strtotime($row['batas_pendaftaran'])) ?></p>
                        </div>
                        <div class="card-footer bg-white border-0 pb-3">
                            <a href="detail_lowongan.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary w-100">Lihat Detail Lengkap</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12"><div class="alert alert-warning">Tidak ada lowongan yang sesuai dengan pencarian.</div></div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
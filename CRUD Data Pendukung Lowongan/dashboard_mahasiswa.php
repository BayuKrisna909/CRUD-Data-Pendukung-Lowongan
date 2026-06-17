<?php
require_once 'config.php';

// Simulasi login mahasiswa (Menggunakan ID 3)
$mahasiswa_id = 3;

// 1. PROSES GET DATA UNTUK EXPLORE LOWONGAN
$search = isset($_GET['search']) ? $_GET['search'] : '';
$lokasi = isset($_GET['lokasi']) ? $_GET['lokasi'] : '';

$query_lowongan = "SELECT l.*, p.nama_perusahaan, p.logo 
                   FROM lowongan l 
                   JOIN perusahaan p ON l.perusahaan_id = p.id 
                   WHERE (l.judul LIKE :search OR p.nama_perusahaan LIKE :search)";

if (!empty($lokasi)) {
    $query_lowongan .= " AND l.lokasi = :lokasi";
}
$query_lowongan .= " ORDER BY l.id DESC";

$stmt = $pdo->prepare($query_lowongan);
$params = ['search' => "%$search%"];
if (!empty($lokasi)) {
    $params['lokasi'] = $lokasi;
}
$stmt->execute($params);
$lowongans = $stmt->fetchAll(PDO::FETCH_ASSOC);


// 2. PROSES GET DATA STATUS/RIWAYAT LAMARAN MAHASISWA INI
$query_lamaran = "SELECT lm.tanggal_lamaran, lm.status, lw.judul, pr.nama_perusahaan 
                  FROM lamaran lm
                  JOIN lowongan lw ON lm.lowongan_id = lw.id
                  JOIN perusahaan pr ON lw.perusahaan_id = pr.id
                  WHERE lm.mahasiswa_id = :mhs_id
                  ORDER BY lm.id DESC";
$stmt_lamaran = $pdo->prepare($query_lamaran);
$stmt_lamaran->execute(['mhs_id' => $mahasiswa_id]);
$riwayat_lamaran = $stmt_lamaran->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="fw-bold text-dark mb-2">Halo, Selamat Datang Mahasiswa 👋</h1>
    <p class="text-muted mb-5">Cari lowongan magang terbaik dan pantau terus perkembangan seleksimu di sini.</p>

    <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-index btn btn-outline-primary active me-2" id="tab-jelajah" data-bs-toggle="pill" data-bs-target="#panel-jelajah" type="button" role="tab">🔍 Jelajah Lowongan</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="btn btn-outline-success" id="tab-status" data-bs-toggle="pill" data-bs-target="#panel-status" type="button" role="tab">💼 Status Lamaran Saya (<?= count($riwayat_lamaran) ?>)</button>
        </li>
    </ul>

    <div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom">
        <div>
            <h1 class="fw-bold text-dark mb-1">Halo, Selamat Datang Mahasiswa 👋</h1>
            <p class="text-muted mb-0">Cari lowongan magang terbaik dan pantau terus perkembangan seleksimu di sini.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="index.php" class="btn btn-outline-secondary fw-semibold">
                🔄 Ganti Role / Keluar Portal
            </a>
        </div>
    </div>

    <div class="tab-content" id="pills-tabContent">
        
        <div class="tab-pane fade show active" id="panel-jelajah" role="tabpanel" aria-labelledby="tab-jelajah">
            <form action="" method="GET" class="row g-3 mb-4">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Cari posisi magang atau perusahaan..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-4">
                    <select name="lokasi" class="form-select">
                        <option value="">Semua Lokasi</option>
                        <option value="Denpasar" <?= $lokasi == 'Denpasar' ? 'selected' : '' ?>>Denpasar</option>
                        <option value="Badung" <?= $lokasi == 'Badung' ? 'selected' : '' ?>>Badung</option>
                        <option value="Gianyar" <?= $lokasi == 'Gianyar' ? 'selected' : '' ?>>Gianyar</option>
                        <option value="Kuta" <?= $lokasi == 'Kuta' ? 'selected' : '' ?>>Kuta</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">Filter</button>
                </div>
            </form>

            <div class="row">
                <?php if (count($lowongans) > 0): ?>
                    <?php foreach ($lowongans as $row): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm border-0 p-3">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title fw-bold text-dark mb-1"><?= htmlspecialchars($row['judul']) ?></h5>
                                    <p class="text-primary fw-medium small mb-3"><?= htmlspecialchars($row['nama_perusahaan']) ?></p>
                                    <p class="card-text text-secondary text-truncate-2 small mb-4"><?= htmlspecialchars($row['deskripsi']) ?></p>
                                    
                                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                        <span class="badge bg-secondary"><?= htmlspecialchars($row['lokasi']) ?></span>
                                        <a href="detail_lowongan.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary fw-semibold">Detail Info</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center text-muted py-5">Lowongan kerja tidak ditemukan.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="tab-pane fade" id="panel-status" role="tabpanel" aria-labelledby="tab-status">
            <div class="card shadow-sm border-0 p-4">
                <h4 class="fw-bold mb-4 text-dark">Riwayat Lamaran Magang</h4>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Posisi Lowongan</th>
                                <th>Perusahaan</th>
                                <th>Tanggal Submit</th>
                                <th class="text-center">Status Kelulusan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($riwayat_lamaran) > 0): ?>
                                <?php $no = 1; foreach ($riwayat_lamaran as $lamaran): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td class="fw-semibold text-dark"><?= htmlspecialchars($lamaran['judul']) ?></td>
                                        <td><?= htmlspecialchars($lamaran['nama_perusahaan']) ?></td>
                                        <td><?= date('d M Y', strtotime($lamaran['tanggal_lamaran'])) ?></td>
                                        <td class="text-center">
                                            <?php if ($lamaran['status'] == 'pending'): ?>
                                                <span class="badge bg-warning text-dark px-3 py-2 text-uppercase">⌛ Pending</span>
                                            <?php elseif ($lamaran['status'] == 'diterima'): ?>
                                                <span class="badge bg-success px-3 py-2 text-uppercase">🎉 Diterima</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger px-3 py-2 text-uppercase">❌ Ditoliak</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Kamu belum pernah melamar ke lowongan manapun.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
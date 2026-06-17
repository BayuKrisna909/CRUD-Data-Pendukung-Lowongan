<?php
require_once 'config.php';

$pesan = '';

// Proses Hapus Data
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $stmt = $pdo->prepare("DELETE FROM lowongan WHERE id = :id");
    if ($stmt->execute([':id' => $id_hapus])) {
        $pesan = "<div class='alert alert-success'>Data lowongan berhasil dihapus!</div>";
    }
}

// Proses Simpan / Update Data
if (isset($_POST['simpan'])) {
    $id = $_POST['id'] ?? '';
    $perusahaan_id = $_POST['perusahaan_id'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $lokasi = $_POST['lokasi'];
    $durasi = $_POST['durasi'];
    $kuota = $_POST['kuota'];
    $batas_pendaftaran = $_POST['batas_pendaftaran'];

    if (empty($id)) {
        $sql = "INSERT INTO lowongan (perusahaan_id, judul, deskripsi, lokasi, durasi, kuota, batas_pendaftaran) 
                VALUES (:perusahaan_id, :judul, :deskripsi, :lokasi, :durasi, :kuota, :batas_pendaftaran)";
        $stmt = $pdo->prepare($sql);
        $pesan_sukses = "Data lowongan baru berhasil ditambahkan!";
    } else {
        $sql = "UPDATE lowongan SET perusahaan_id = :perusahaan_id, judul = :judul, deskripsi = :deskripsi, 
                lokasi = :lokasi, durasi = :durasi, kuota = :kuota, batas_pendaftaran = :batas_pendaftaran 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $pesan_sukses = "Data lowongan berhasil diperbarui!";
    }

    $params = [
        ':perusahaan_id' => $perusahaan_id, ':judul' => $judul, ':deskripsi' => $deskripsi,
        ':lokasi' => $lokasi, ':durasi' => $durasi, ':kuota' => $kuota, ':batas_pendaftaran' => $batas_pendaftaran
    ];
    if (!empty($id)) $params[':id'] = $id;

    if($stmt->execute($params)) {
        $pesan = "<div class='alert alert-success'>$pesan_sukses</div>";
    }
}

// Ambil Data untuk Form Edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $stmt_edit = $pdo->prepare("SELECT * FROM lowongan WHERE id = :id");
    $stmt_edit->execute([':id' => intval($_GET['edit'])]);
    $edit_data = $stmt_edit->fetch(PDO::FETCH_ASSOC);
}

// Ambil Data Perusahaan untuk Dropdown
$perusahaan = $pdo->query("SELECT id, nama_perusahaan FROM perusahaan")->fetchAll(PDO::FETCH_ASSOC);

// Ambil Seluruh Data Lowongan untuk Tabel (Dengan Search)
$search_admin = isset($_GET['search_admin']) ? $_GET['search_admin'] : '';
$sql_tampil = "SELECT l.*, p.nama_perusahaan FROM lowongan l 
               JOIN perusahaan p ON l.perusahaan_id = p.id 
               WHERE l.judul LIKE :search OR p.nama_perusahaan LIKE :search 
               ORDER BY l.created_at DESC";
$stmt_tampil = $pdo->prepare($sql_tampil);
$stmt_tampil->execute([':search' => "%$search_admin%"]);
$data_lowongan = $stmt_tampil->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CRUD Data Lowongan - Recruiter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
 <div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom">
        <div>
            <h1 class="fw-bold text-success mb-1">💼 Panel Kelola Data Pendukung Lowongan</h1>
            <p class="text-muted mb-0">Halaman khusus Recruiter / Perusahaan untuk memanipulasi data magang.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="index.php" class="btn btn-outline-secondary fw-semibold">
                🔄 Ganti Role / Keluar Portal
            </a>
        </div>
    </div>
<div class="container-fluid p-4">
    <h3 class="mb-4">Manajemen Data Pendukung Lowongan</h3>
    <?= $pesan ?>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><?= $edit_data ? 'Edit Lowongan' : 'Tambah Lowongan' ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="crud_data_pendukung.php">
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Perusahaan Perekrut</label>
                            <select name="perusahaan_id" class="form-select" required>
                                <option value="">-- Pilih Perusahaan --</option>
                                <?php foreach($perusahaan as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= (isset($edit_data['perusahaan_id']) && $edit_data['perusahaan_id'] == $p['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['nama_perusahaan']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Judul Posisi</label>
                            <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($edit_data['judul'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi Tugas</label>
                            <textarea name="deskripsi" class="form-control" rows="4" required><?= htmlspecialchars($edit_data['deskripsi'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Lokasi Penempatan</label>
                                <input type="text" name="lokasi" class="form-control" value="<?= htmlspecialchars($edit_data['lokasi'] ?? '') ?>" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Durasi (Bulan)</label>
                                <input type="text" name="durasi" class="form-control" placeholder="Contoh: 3 Bulan" value="<?= htmlspecialchars($edit_data['durasi'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Kuota Maksimal</label>
                                <input type="number" name="kuota" class="form-control" value="<?= htmlspecialchars($edit_data['kuota'] ?? '') ?>" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Batas Pendaftaran</label>
                                <input type="date" name="batas_pendaftaran" class="form-control" value="<?= htmlspecialchars($edit_data['batas_pendaftaran'] ?? '') ?>" required>
                            </div>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-<?= $edit_data ? 'warning' : 'primary' ?> w-100 fw-bold">
                            <?= $edit_data ? 'Perbarui Data' : 'Simpan Lowongan' ?>
                        </button>
                        
                        <?php if($edit_data): ?>
                            <a href="crud_data_pendukung.php" class="btn btn-secondary w-100 mt-2">Batal Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark fw-bold">Daftar Data Pendukung Lowongan</h5>
                    <form method="GET" action="crud_data_pendukung.php" class="d-flex">
                        <input type="text" name="search_admin" class="form-control form-control-sm me-2" placeholder="Cari Lowongan..." value="<?= htmlspecialchars($search_admin) ?>">
                        <button type="submit" class="btn btn-outline-dark btn-sm">Cari</button>
                    </form>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Posisi</th>
                                <th>Perusahaan</th>
                                <th>Lokasi</th>
                                <th>Kuota</th>
                                <th>Deadline</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($data_lowongan) > 0): ?>
                                <?php $no = 1; foreach($data_lowongan as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="fw-bold text-primary"><?= htmlspecialchars($row['judul']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_perusahaan']) ?></td>
                                    <td><?= htmlspecialchars($row['lokasi']) ?></td>
                                    <td><?= htmlspecialchars($row['kuota']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['batas_pendaftaran'])) ?></td>
                                    <td class="text-center">
                                        <a href="crud_data_pendukung.php?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="crud_data_pendukung.php?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus lowongan ini secara permanen?')">Hapus</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center py-4">Belum ada data lowongan yang terdaftar.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
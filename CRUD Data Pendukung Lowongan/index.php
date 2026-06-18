<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Lowongan Magang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .portal-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <h2 class="mb-4 fw-bold text-primary">Selamat Datang di Portal Magang</h2>
            <p class="text-muted mb-5">Silakan pilih halaman yang ingin kamu akses di bawah ini:</p>
            
            <div class="card portal-card p-4 bg-white">
                <div class="d-grid gap-3">
                    <a href="dashboard_mahasiswa.php" class="btn btn-primary btn-lg py-3 fw-semibold">
                        👨‍🎓 Masuk Sebagai Dashboard Mahasiswa
                    </a>
                    <a href="crud_data_pendukung.php" class="btn btn-success btn-lg py-3 fw-semibold">
                        💼 Masuk Sebagai CRUD Recruiter / Perusahaan
                    </a>
                </div>
            </div>
            <p class="mt-4 text-secondary small">&copy; 2026 db_datapendukunglowongan Sistem</p>
        </div>
    </div>
</div>

</body>
</html>
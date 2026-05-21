<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$nama_user = htmlspecialchars($_SESSION['user_nama']);
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode  = trim($_POST['kode_unik_kendaraan'] ?? '');
    $nama  = trim($_POST['nama_kendaraan']      ?? '');
    $jenis = trim($_POST['jenis_kendaraan']     ?? '');
    $harga = $_POST['harga_per_hari']           ?? '';

    if (empty($kode) || empty($nama) || empty($jenis) || $harga === '') {
        $error = 'Semua field wajib diisi.';
    } elseif (!is_numeric($harga) || $harga < 0) {
        $error = 'Harga per hari harus berupa angka positif.';
    } else {
        $cek = mysqli_prepare($mysqli, "SELECT kode_unik_kendaraan FROM kendaraan WHERE kode_unik_kendaraan = ?");
        mysqli_stmt_bind_param($cek, 's', $kode);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {
            $error = 'Kode kendaraan sudah digunakan. Gunakan kode lain.';
        } else {
            // Proses Upload Gambar
            $gambar_nama = null;
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['gambar']['tmp_name'];
                $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
                $gambar_nama = time() . '_' . $kode . '.' . $ext;
                move_uploaded_file($tmp, 'uploads/' . $gambar_nama);
            }

            $stmt = mysqli_prepare($mysqli, "INSERT INTO kendaraan (kode_unik_kendaraan, nama_kendaraan, jenis_kendaraan, harga_per_hari, gambar) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sssds', $kode, $nama, $jenis, $harga, $gambar_nama);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: dashboard.php?msg=added');
                exit;
            } else {
                $error = 'Gagal menyimpan data. Coba lagi.';
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($cek);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Tambah Kendaraan — FTrans</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet"> 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        <?php include 'css/style.css'; ?>
        :root { --primary: #EB1616; --secondary: #191C24; --light: #6C7293; --dark: #000000; }
        body { background-color: var(--dark); color: var(--light); }
        .bg-secondary { background-color: var(--secondary) !important; }
        .form-control, .form-select { background-color: var(--dark); border: none; color: #fff; }
        .form-control:focus, .form-select:focus { background-color: var(--dark); color: #fff; box-shadow: 0 0 0 0.25rem rgba(235, 22, 22, 0.25); }
        
        /* FIX SIDEBAR MENU WRAPPING */
        .sidebar .navbar-nav .nav-link {
            white-space: nowrap;
            font-size: 15px; 
        }
    </style>
</head>
<body>
    <div class="container-fluid position-relative d-flex p-0">
        <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"><span class="sr-only">Loading...</span></div>
        </div>

        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="dashboard.php" class="navbar-brand mx-4 mb-3"><h3 class="text-primary"><i class="fa fa-car me-2"></i>FTrans</h3></a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px;"><?= strtoupper(substr($nama_user, 0, 1)) ?></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white"><?= $nama_user ?></h6>
                        <span>Operator</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="dashboard.php" class="nav-item nav-link"><i class="fa fa-table me-2"></i>Data Kendaraan</a>
                    <a href="add.php" class="nav-item nav-link active"><i class="fa fa-keyboard me-2"></i>Tambah Kendaraan</a>
                    <a href="logout.php" class="nav-item nav-link text-danger"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
                </div>
            </nav>
        </div>

        <div class="content">
            <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0" style="height: 64px;">
                <a href="#" class="sidebar-toggler flex-shrink-0 text-decoration-none"><i class="fa fa-bars text-primary"></i></a>
            </nav>

            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-8">
                        <div class="bg-secondary rounded h-100 p-4">
                            <h6 class="mb-4 text-white fs-5"><i class="fa fa-plus me-2 text-primary"></i>Form Data Kendaraan Baru</h6>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger bg-dark border-0 text-danger small">⚠ <?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>

                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label text-white">Kode Unik Kendaraan *</label>
                                    <input type="text" name="kode_unik_kendaraan" class="form-control" placeholder="Contoh: 1122" value="<?= htmlspecialchars($_POST['kode_unik_kendaraan'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-white">Nama Kendaraan *</label>
                                    <input type="text" name="nama_kendaraan" class="form-control" placeholder="Contoh: Toyota Avanza" value="<?= htmlspecialchars($_POST['nama_kendaraan'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-white">Jenis Kendaraan *</label>
                                    <select name="jenis_kendaraan" class="form-select" required>
                                        <option value="" disabled <?= empty($_POST['jenis_kendaraan']) ? 'selected' : '' ?>>-- Pilih Jenis Kendaraan --</option>
                                        <option value="Roda 2" <?= ($_POST['jenis_kendaraan'] ?? '') === 'Roda 2' ? 'selected' : '' ?>>Roda 2 (Motor)</option>
                                        <option value="Roda 4" <?= ($_POST['jenis_kendaraan'] ?? '') === 'Roda 4' ? 'selected' : '' ?>>Roda 4 (Mobil)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-white">Harga per Hari (Rp) *</label>
                                    <input type="number" name="harga_per_hari" class="form-control" placeholder="Contoh: 150000" min="0" value="<?= htmlspecialchars($_POST['harga_per_hari'] ?? '') ?>" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-white">Upload Gambar Kendaraan (Opsional)</label>
                                    <input type="file" name="gambar" class="form-control bg-dark" accept="image/*">
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Simpan</button>
                                    <a href="dashboard.php" class="btn btn-dark">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(window).on('load', function () { setTimeout(function () { if ($('#spinner').length > 0) { $('#spinner').removeClass('show'); } }, 100); });
        $('.sidebar-toggler').click(function () { $('.sidebar, .content').toggleClass("open"); return false; });
    </script>
</body>
</html>
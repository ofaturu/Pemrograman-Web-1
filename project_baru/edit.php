<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$kode  = trim($_GET['kode'] ?? '');
$error = '';
$nama_user = htmlspecialchars($_SESSION['user_nama']);

if (empty($kode)) {
    header('Location: dashboard.php');
    exit;
}

$stmt = mysqli_prepare($mysqli, "SELECT * FROM kendaraan WHERE kode_unik_kendaraan = ?");
mysqli_stmt_bind_param($stmt, 's', $kode);
mysqli_stmt_execute($stmt);
$result     = mysqli_stmt_get_result($stmt);
$kendaraan  = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$kendaraan) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_baru  = trim($_POST['nama_kendaraan']  ?? '');
    $jenis_baru = trim($_POST['jenis_kendaraan'] ?? '');
    $harga_baru = $_POST['harga_per_hari']       ?? '';
    $gambar_baru = $kendaraan['gambar'];

    if (empty($nama_baru) || empty($jenis_baru) || $harga_baru === '') {
        $error = 'Semua field wajib diisi.';
    } elseif (!is_numeric($harga_baru) || $harga_baru < 0) {
        $error = 'Harga per hari harus berupa angka positif.';
    } else {
        // Cek jika upload gambar baru
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['gambar']['tmp_name'];
            $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $nama_file_baru = time() . '_' . $kode . '.' . $ext;
            
            if (move_uploaded_file($tmp, 'uploads/' . $nama_file_baru)) {
                if (!empty($kendaraan['gambar']) && file_exists('uploads/' . $kendaraan['gambar'])) {
                    unlink('uploads/' . $kendaraan['gambar']);
                }
                $gambar_baru = $nama_file_baru;
            }
        }

        $upd = mysqli_prepare($mysqli, "UPDATE kendaraan SET nama_kendaraan = ?, jenis_kendaraan = ?, harga_per_hari = ?, gambar = ? WHERE kode_unik_kendaraan = ?");
        mysqli_stmt_bind_param($upd, 'ssdss', $nama_baru, $jenis_baru, $harga_baru, $gambar_baru, $kode);

        if (mysqli_stmt_execute($upd)) {
            header('Location: dashboard.php?msg=updated');
            exit;
        } else {
            $error = 'Gagal memperbarui data. Coba lagi.';
        }
        mysqli_stmt_close($upd);
    }
    $kendaraan['nama_kendaraan']  = $nama_baru;
    $kendaraan['jenis_kendaraan'] = $jenis_baru;
    $kendaraan['harga_per_hari']  = $harga_baru;
    $kendaraan['gambar']          = $gambar_baru;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Edit Kendaraan — FTrans</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet"> 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid position-relative d-flex p-0">
        <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"><span class="sr-only">Loading...</span></div>
        </div>

        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="dashboard.php" class="navbar-brand mx-4 mb-3"><h3 class="text-primary"><i class="fa fa-car me-2"></i>FTrans</h3></a>
                <div class="navbar-nav w-100">
                    <a href="dashboard.php" class="nav-item nav-link active"><i class="fa fa-table me-2"></i>Data Kendaraan</a>
                    <a href="add.php" class="nav-item nav-link"><i class="fa fa-keyboard me-2"></i>Tambah Kendaraan</a>
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
                            <h6 class="mb-4 text-white fs-5"><i class="fa fa-edit me-2 text-primary"></i>Edit Data Kendaraan</h6>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger bg-dark border-0 text-danger small">⚠ <?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>

                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label text-white">Kode Unik Kendaraan</label>
                                    <input type="text" class="form-control text-warning bg-dark border border-secondary" value="<?= htmlspecialchars($kode) ?>" readonly disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-white">Nama Kendaraan *</label>
                                    <input type="text" name="nama_kendaraan" class="form-control" value="<?= htmlspecialchars($kendaraan['nama_kendaraan']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-white">Jenis Kendaraan *</label>
                                    <select name="jenis_kendaraan" class="form-select" required>
                                        <?php $j_curr = strtolower(trim($kendaraan['jenis_kendaraan'])); ?>
                                        <option value="Roda 2" <?= $j_curr === 'roda 2' ? 'selected' : '' ?>>Roda 2 (Motor)</option>
                                        <option value="Roda 4" <?= $j_curr === 'roda 4' ? 'selected' : '' ?>>Roda 4 (Mobil)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-white">Harga per Hari (Rp) *</label>
                                    <input type="number" name="harga_per_hari" class="form-control" min="0" value="<?= htmlspecialchars($kendaraan['harga_per_hari']) ?>" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-white d-block">Gambar Saat Ini</label>
                                    <?php if (!empty($kendaraan['gambar']) && file_exists('uploads/' . $kendaraan['gambar'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($kendaraan['gambar']) ?>" alt="Mobil" class="rounded mb-2" style="width: 150px; height: 100px; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="badge bg-dark mb-2 py-2 px-3">Belum ada gambar</span>
                                    <?php endif; ?>
                                    <input type="file" name="gambar" class="form-control bg-dark" accept="image/*">
                                    <div class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-info text-white"><i class="fa fa-save me-1"></i> Simpan Perubahan</button>
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
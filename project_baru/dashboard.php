<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$nama_user = htmlspecialchars($_SESSION['user_nama']);

// Menangkap keyword pencarian dari URL
$search = trim($_GET['search'] ?? '');

if (!empty($search)) {
    // Logika Search: Menggunakan LIKE dengan % untuk mencari data yang mengandung keyword
    $search_param = "%" . $search . "%";
    
    $stmt = mysqli_prepare($mysqli, "SELECT * FROM kendaraan WHERE kode_unik_kendaraan LIKE ? OR nama_kendaraan LIKE ? OR jenis_kendaraan LIKE ? ORDER BY kode_unik_kendaraan ASC");
    mysqli_stmt_bind_param($stmt, 'sss', $search_param, $search_param, $search_param);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $kendaraan = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
} else {
    // Jika tidak ada pencarian, tampilkan semua data
    $result = mysqli_query($mysqli, "SELECT * FROM kendaraan ORDER BY kode_unik_kendaraan ASC");
    $kendaraan = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

$total = count($kendaraan);
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dashboard — FTrans</title>
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

        .sidebar .navbar-nav .nav-link {
            white-space: nowrap;
            font-size: 15px; 
        }
    </style>
</head>
<body>
    <div class="container-fluid position-relative d-flex p-0">
        <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="dashboard.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-car me-2"></i>FTrans</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px;">
                            <?= strtoupper(substr($nama_user, 0, 1)) ?>
                        </div>
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white"><?= $nama_user ?></h6>
                        <span>Operator</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="dashboard.php" class="nav-item nav-link active"><i class="fa fa-table me-2"></i>Data Kendaraan</a>
                    <a href="add.php" class="nav-item nav-link"><i class="fa fa-keyboard me-2"></i>Tambah Kendaraan</a>
                    <a href="logout.php" class="nav-item nav-link text-danger"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
                </div>
            </nav>
        </div>

        <div class="content">
            <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0" style="height: 64px;">
                <a href="#" class="sidebar-toggler flex-shrink-0 text-decoration-none">
                    <i class="fa fa-bars text-primary"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <span class="d-none d-lg-inline-flex fw-bold text-white"><?= $nama_user ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <a href="profile.php" class="dropdown-item">My Profile</a>
                            <a href="logout.php" class="dropdown-item text-danger">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid pt-4 px-4">
                <?php if ($msg === 'added'): ?>
                    <div class="alert alert-success bg-secondary border-0 text-success">✓ Kendaraan berhasil ditambahkan.</div>
                <?php elseif ($msg === 'updated'): ?>
                    <div class="alert alert-success bg-secondary border-0 text-success">✓ Data berhasil diperbarui.</div>
                <?php elseif ($msg === 'deleted'): ?>
                    <div class="alert alert-danger bg-secondary border-0 text-danger">🗑 Kendaraan berhasil dihapus.</div>
                <?php endif; ?>

                <div class="col-12">
                    <div class="bg-secondary rounded h-100 p-4">
                        
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-4 gap-3">
                            <h6 class="mb-0 text-white fs-5"><i class="fa fa-list me-2 text-primary"></i>Daftar Kendaraan</h6>
                            
                            <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                                <form action="dashboard.php" method="GET" class="d-flex">
                                    <input type="text" name="search" class="form-control form-control-sm bg-dark border-secondary text-white me-2" placeholder="Cari nama, kode, jenis..." value="<?= htmlspecialchars($search) ?>" style="min-width: 200px;">
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i></button>
                                    
                                    <?php if(!empty($search)): ?>
                                        <a href="dashboard.php" class="btn btn-danger btn-sm ms-1" title="Reset Pencarian"><i class="fa fa-times"></i></a>
                                    <?php endif; ?>
                                </form>

                                <a href="add.php" class="btn btn-primary btn-sm text-nowrap"><i class="fa fa-plus me-1"></i> Tambah</a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table text-start align-middle table-bordered table-hover mb-0">
                                <thead>
                                    <tr class="text-white">
                                        <th scope="col">No</th>
                                        <th scope="col">Gambar</th>
                                        <th scope="col">Kode Unik</th>
                                        <th scope="col">Nama Kendaraan</th>
                                        <th scope="col">Jenis</th>
                                        <th scope="col">Harga / Hari</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($total > 0): ?>
                                        <?php foreach ($kendaraan as $i => $k): ?>
                                        <tr>
                                            <td class="text-white"><?= $i + 1 ?></td>
                                            
                                            <td>
                                                <?php if (!empty($k['gambar']) && file_exists('uploads/' . $k['gambar'])): ?>
                                                    <img src="uploads/<?= htmlspecialchars($k['gambar']) ?>" alt="Kendaraan" class="rounded border border-secondary" style="width: 70px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <span class="text-muted small">No Image</span>
                                                <?php endif; ?>
                                            </td>

                                            <td><span class="badge bg-dark text-warning border border-warning px-2 py-1 fs-6"><?= htmlspecialchars($k['kode_unik_kendaraan']) ?></span></td>
                                            <td class="text-white fw-bold"><?= htmlspecialchars($k['nama_kendaraan']) ?></td>
                                            <td class="text-info fw-bold"><?= (strtolower($k['jenis_kendaraan']) === 'roda 2') ? 'Roda 2' : 'Roda 4' ?></td>
                                            <td class="text-white">Rp <?= number_format($k['harga_per_hari'], 0, ',', '.') ?></td>
                                            <td>
                                                <a class="btn btn-sm btn-info me-1" href="edit.php?kode=<?= urlencode($k['kode_unik_kendaraan']) ?>"><i class="fa fa-edit me-1"></i>Edit</a>
                                                <a class="btn btn-sm btn-primary" href="delete.php?kode=<?= urlencode($k['kode_unik_kendaraan']) ?>" onclick="return confirm('Yakin hapus kendaraan ini?')"><i class="fa fa-trash me-1"></i>Hapus</a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <?php if(!empty($search)): ?>
                                                    Kendaraan dengan kata kunci "<b><?= htmlspecialchars($search) ?></b>" tidak ditemukan.
                                                <?php else: ?>
                                                    Belum ada data kendaraan.
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid pt-4 px-4 mt-auto">
                <div class="bg-secondary rounded-top p-4">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start">
                            &copy; <a href="#" class="text-primary text-decoration-none">FTrans Management</a>, All Right Reserved. 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(window).on('load', function () {
            setTimeout(function () {
                if ($('#spinner').length > 0) {
                    $('#spinner').removeClass('show');
                }
            }, 100);
        });
        $('.sidebar-toggler').click(function () {
            $('.sidebar, .content').toggleClass("open");
            return false;
        });
    </script>
</body>
</html>
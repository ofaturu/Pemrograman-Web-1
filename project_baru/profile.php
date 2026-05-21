<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error   = '';
$success = '';

$stmt = mysqli_prepare($mysqli, "SELECT nama, email FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_baru  = trim($_POST['nama'] ?? '');
    $email_baru = trim($_POST['email'] ?? '');
    $pass_baru  = $_POST['password'] ?? '';

    if (empty($nama_baru) || empty($email_baru)) {
        $error = 'Nama dan Email wajib diisi.';
    } elseif (!filter_var($email_baru, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $cek_email = mysqli_prepare($mysqli, "SELECT id FROM users WHERE email = ? AND id != ?");
        mysqli_stmt_bind_param($cek_email, 'si', $email_baru, $user_id);
        mysqli_stmt_execute($cek_email);
        mysqli_stmt_store_result($cek_email);

        if (mysqli_stmt_num_rows($cek_email) > 0) {
            $error = 'Email tersebut sudah digunakan oleh akun lain.';
        } else {
            if (empty($pass_baru)) {
                $upd = mysqli_prepare($mysqli, "UPDATE users SET nama = ?, email = ? WHERE id = ?");
                mysqli_stmt_bind_param($upd, 'ssi', $nama_baru, $email_baru, $user_id);
            } else {
                if(strlen($pass_baru) < 6) {
                    $error = 'Password baru minimal 6 karakter.';
                } else {
                    $hashed = password_hash($pass_baru, PASSWORD_DEFAULT);
                    $upd = mysqli_prepare($mysqli, "UPDATE users SET nama = ?, email = ?, password = ? WHERE id = ?");
                    mysqli_stmt_bind_param($upd, 'sssi', $nama_baru, $email_baru, $hashed, $user_id);
                }
            }

            if (empty($error)) {
                if (mysqli_stmt_execute($upd)) {
                    $success = 'Profil berhasil diperbarui!';
                    $_SESSION['user_nama'] = $nama_baru;
                    $user['nama'] = $nama_baru;
                    $user['email'] = $email_baru;
                } else {
                    $error = 'Gagal memperbarui profil.';
                }
                mysqli_stmt_close($upd);
            }
        }
        mysqli_stmt_close($cek_email);
    }
}

$nama_user = htmlspecialchars($_SESSION['user_nama']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>My Profile — FTrans</title>
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
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white"><?= $nama_user ?></h6>
                        <span>Operator</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="dashboard.php" class="nav-item nav-link"><i class="fa fa-table me-2"></i>Data Kendaraan</a>
                    <a href="add.php" class="nav-item nav-link"><i class="fa fa-keyboard me-2"></i>Tambah Kendaraan</a>
                    <a href="logout.php" class="nav-item nav-link text-danger"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
                </div>
            </nav>
        </div>

        <div class="content">
            <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0" style="height: 64px;">
                <a href="#" class="sidebar-toggler flex-shrink-0 text-decoration-none"><i class="fa fa-bars text-primary"></i></a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <span class="d-none d-lg-inline-flex fw-bold text-white"><?= $nama_user ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <a href="profile.php" class="dropdown-item active">My Profile</a>
                            <a href="logout.php" class="dropdown-item text-danger">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid pt-4 px-4">
                <div class="row g-4 justify-content-center">
                    <div class="col-sm-12 col-xl-8">
                        <div class="bg-secondary rounded h-100 p-4">
                            <h6 class="mb-4 text-white fs-5"><i class="fa fa-user-edit me-2 text-primary"></i>My Profile</h6>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger bg-dark border-0 text-danger small">⚠ <?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>
                            <?php if ($success): ?>
                                <div class="alert alert-success bg-dark border-0 text-success small">✓ <?= htmlspecialchars($success) ?></div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label text-white">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-white">Email Address</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>
                                <hr class="border-secondary my-4">
                                <div class="mb-4">
                                    <label class="form-label text-white">Password Baru</label>
                                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                                    <div class="form-text text-muted">Hanya isi jika lu pengen ganti password akun ini.</div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Simpan Profil</button>
                                    <a href="dashboard.php" class="btn btn-dark">Kembali</a>
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
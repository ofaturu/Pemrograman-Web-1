<?php
require_once 'config.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $konfirm  = $_POST['konfirmasi']    ?? '';

    if (empty($nama) || empty($email) || empty($password) || empty($konfirm)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $konfirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $cek = mysqli_prepare($mysqli, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($cek, 's', $email);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {
            $error = 'Email sudah terdaftar. Gunakan email lain.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $ins = mysqli_prepare($mysqli, "INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($ins, 'sss', $nama, $email, $hashed);

            if (mysqli_stmt_execute($ins)) {
                $success = 'Akun berhasil dibuat! Silakan <a href="login.php" class="text-primary fw-bold">login di sini</a>.';
            } else {
                $error = 'Terjadi kesalahan. Coba lagi.';
            }
            mysqli_stmt_close($ins);
        }
        mysqli_stmt_close($cek);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Sign Up — FTrans</title>
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
        .form-control { background-color: var(--dark); border: none; color: #fff; }
        .form-control:focus { background-color: var(--dark); color: #fff; box-shadow: 0 0 0 0.25rem rgba(235, 22, 22, 0.25); }
    </style>
</head>
<body>
    <div class="container-fluid position-relative d-flex p-0">
        <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                    <div class="bg-secondary rounded p-4 p-sm-5 my-4 mx-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <a href="#" class="">
                                <h3 class="text-primary"><i class="fa fa-car me-2"></i>FTrans</h3>
                            </a>
                            <h3>Sign Up</h3>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger border-0 bg-dark text-danger small">⚠ <?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success border-0 bg-dark text-success small">✓ <?= $success ?></div>
                        <?php endif; ?>

                        <?php if (!$success): ?>
                        <form method="POST" action="">
                            <div class="form-floating mb-3">
                                <input type="text" name="nama" class="form-control" id="floatingText" placeholder="Nama Lengkap" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
                                <label for="floatingText">Full Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" name="email" class="form-control" id="floatingInput" placeholder="name@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                <label for="floatingInput">Email address</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                                <label for="floatingPassword">Password (Min. 6 chars)</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" name="konfirmasi" class="form-control" id="floatingConfirmPassword" placeholder="Konfirmasi Password" required>
                                <label for="floatingConfirmPassword">Confirm Password</label>
                            </div>
                            <button type="submit" class="btn btn-primary py-3 w-100 mb-4">Sign Up</button>
                        </form>
                        <?php endif; ?>
                        <p class="text-center mb-0">Already have an Account? <a href="login.php">Sign In</a></p>
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
    </script>
</body>
</html>
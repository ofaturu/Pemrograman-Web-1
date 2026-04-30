<!DOCTYPE html>
<html lang="en">
<!-- INI ADALAH HALAMAN HEAD -->
<?php include ("partials/head.php"); ?>

<body>
    <div id="app">
        <!-- INI ADALAH SIDEBAR -->
        <?php include ("partials/sidebar.php"); ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>Profile Statistics</h3>
            </div>
            <div class="page-content">
                <section class="row">
                <section id="basic-vertical-layouts">
                    <div class="row match-height">
                        <div class="col-md-6 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Vertical Form</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                    
                                            <form action="tambah.php" method="post" name="form1" class="form form-vertical">
                                            <div class="form-body">
                                                 <div class="form-group">
                                                            <label for="first-name-vertical">Nama Alat</label>
                                                            <input type="text" id="first-name-vertical"
                                                                class="form-control" name="nama_alat"
                                                                placeholder="Nama Alat">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="first-name-vertical">Tahun</label>
                                                            <input type="text" id="first-name-vertical"
                                                                class="form-control" name="tahun"
                                                                placeholder="Tahun">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="first-name-vertical">Merek</label>
                                                            <input type="text" id="first-name-vertical"
                                                                class="form-control" name="merek"
                                                                placeholder="Tuliskan Merek Alat">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="first-name-vertical">Lokasi</label>
                                                            <input type="text" id="first-name-vertical"
                                                                class="form-control" name="lokasi"
                                                                placeholder="Tuliskan Lokasi Alat">
                                                        </div>
                                                    <button type="submit"
                                                            class="btn btn-primary me-1 mb-1"  name="Submit" value="Add">Submit</button>
                                                    <button type="reset"
                                                            class="btn btn-light-secondary me-1 mb-1">Reset</button>    
    </form>

    <?php

    // Check If form submitted, insert form data into users table.
    if(isset($_POST['Submit'])) {
        $nama_alat= $_POST['nama_alat'];
        $tahun = $_POST['tahun'];
        $merek= $_POST['merek'];
        $lokasi = $_POST['lokasi'];

        // include database connection file
        include_once("config.php");

        // Insert user data into table
        $result = mysqli_query($mysqli, "INSERT INTO alat(nama_alat,tahun,merek,lokasi) VALUES('$nama_alat','$tahun','$merek','$lokasi')");

        // Show message when user added
        echo "User added successfully. <a href='alat.php'>View Alat</a>";
    }
    ?>
                </section>
            </div>

            
        </div>
    </div>
    <script src="assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <script src="assets/vendors/apexcharts/apexcharts.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>

    <script src="assets/js/main.js"></script>
</body>

</html>
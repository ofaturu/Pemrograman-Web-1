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
                <?php

include_once("config.php");


$result = mysqli_query($mysqli, "SELECT * FROM alat ORDER BY id DESC");
?>    
                <b>Data Alat Elektromedis</b><br>
<a href="tambah.php">Tambah Alat</a><br/><br/>

   <table class="table table-striped" id="table1">

    <tr class="header">
         <th>No</th><th>Nama Alat</th> <th>Tahun</th> <th>Merek</th><th>Lokasi</th> <th>Aksi</th>
    </tr>
    <?php  
    $i=1;
    while($user_data = mysqli_fetch_array($result)) {         
        echo "<tr>";
        echo "<td>".$i."</td>";
        echo "<td>".$user_data['nama_alat']."</td>";
        echo "<td>".$user_data['tahun']."</td>";
        echo "<td>".$user_data['merek']."</td>";    
        echo "<td>".$user_data['lokasi']."</td>";    
        echo "<td><a href='editfile.php?id=$user_data[id]'>Edit</a> | <a href='delete.php?id=$user_data[id]'>Delete</a></td></tr>"; 
        $i++;       
    }
    ?>
    </table>
                
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
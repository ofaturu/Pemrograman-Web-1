<?php
// include database connection file
include_once("config.php");

// Check if form is submitted for user update, then redirect to homepage after update
if(isset($_POST['update']))
{   
    $id = $_POST['id'];
   $nama_alat= $_POST['nama_alat'];
        $tahun = $_POST['tahun'];
        $merek= $_POST['merek'];
        $lokasi = $_POST['lokasi'];

    // update user data
    $result = mysqli_query($mysqli, "UPDATE alat SET nama_alat='$nama_alat',tahun='$tahun',merek='$merek',lokasi='$lokasi' WHERE id=$id");

    // Redirect to homepage to display updated user in list
    header("Location: alat.php");
}
?>
<?php
// Display selected user data based on id
// Getting id from url
$id = $_GET['id'];

// Fetech user data based on id
$result = mysqli_query($mysqli, "SELECT * FROM alat WHERE id=$id");

while($user_data = mysqli_fetch_array($result))
{
    $nama_alat = $user_data['nama_alat'];
    $tahun = $user_data['tahun'];
    $merek= $user_data['merek'];
    $lokasi = $user_data['lokasi'];
}
?>
<html>
<head>  
    <title>Edit User Data</title>
</head>


</html>
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
                                    <h4 class="card-title">Edit Alat</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form name="update_user" method="post" action="editfile.php" class="form form-vertical">
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label for="first-name-vertical">Nama Alat</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" name="nama_alat" value="<?php echo $nama_alat;?>" placeholder="Nama Alat">
                                                </div>
                                                <div class="form-group">
                                                    <label for="first-name-vertical">Tahun</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" name="tahun" value="<?php echo $tahun;?>" placeholder="Tahun">
                                                </div>
                                                <div class="form-group">
                                                    <label for="first-name-vertical">Merek</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" name="merek" value="<?php echo $merek;?>" placeholder="Tuliskan Merek Alat">
                                                </div>
                                                <div class="form-group">
                                                    <label for="first-name-vertical">Lokasi</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" name="lokasi" value="<?php echo $lokasi;?>" placeholder="Tuliskan Lokasi Alat">
                                                </div>
                                                <input type="hidden" name="id" value="<?php echo $_GET['id'];?>">
                                                <button type="submit" class="btn btn-primary me-1 mb-1" name="update" value="Update">Update</button>
                                                <button type="reset" class="btn btn-light-secondary me-1 mb-1">Reset</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

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
            </tr>
             <tr> 
                <td>Lokasi</td>
                <td><input type="text" name="lokasi" value=<?php echo $lokasi;?>></td>
            </tr>
            <tr>
                <td><input type="hidden" name="id" value=<?php echo $_GET['id'];?>></td>
                <td><input type="submit" name="update" value="Update"></td>
            </tr>
        </table>
    </form>
</body>
</html>
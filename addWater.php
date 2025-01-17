<?php
     session_start();
     function checkToken($token) {
         $url = "http://localhost:5000/protected"; // Endpoint Flask untuk verifikasi token
     
         // Buat cURL request
         $ch = curl_init($url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_HTTPHEADER, [
             'Authorization: Bearer ' . $token, // Kirim token di header
             'Content-Type: application/json'
         ]);
     
         $response = curl_exec($ch);
         $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
         curl_close($ch);
     
         return $httpcode === 200; // Token valid jika status code 200
     }
     
     
     // Ambil token dari session atau cookie
     $token = $_SESSION['token'] ?? null;
     
     if (!$token || !checkToken($token)) {
         // Token tidak valid atau tidak ditemukan
         header("Location: login.php"); // Arahkan ke halaman login
         exit;
     }
    
    // URL for the Flask API
    $urlWaterCreate = 'http://localhost:5000/api/sumber_air_create'; // Replace with actual URL
    $urlJenisSumberAir = 'http://localhost:5000/api/jensisSA';
    $urlUpaya = "http://localhost:5000/api/upaya"; // Endpoint GET all

    // Mengambil data Upaya dari API
    $r_upaya = json_decode(file_get_contents($urlUpaya), true);


    // Mengambil data Upaya dari API
    $r_jenis = json_decode(file_get_contents($urlJenisSumberAir), true);

    // Fungsi CREATE (POST) Water
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit-add'])) {
        // Get form data
        $namaSumberAir = $_POST['nama_sumber_air'];
        $kondisiSumberAir = $_POST['kondisi'];
        $suhu = $_POST['suhu'];
        $warna = $_POST['warna'];
        $ph = $_POST['pH'];
        $kelayakan = $_POST['layak_minum'];
        $idJenisSumberAir = $_POST['jenis_sumber_air'];
        $idKabupaten = $_POST['kabupaten'];
    
        // Handle file upload
        $fotoSumberAir = "default.png"; // Default if no file uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Get file details
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileSize = $_FILES['image']['size'];
            $fileType = $_FILES['image']['type'];
            
            // Specify upload directory
            $uploadDir = 'images/foto_sumber_air/';
            $destFilePath = $uploadDir . basename($fileName);
    
            // Move the uploaded file to the target directory
            if (move_uploaded_file($fileTmpPath, $destFilePath)) {
                $fotoSumberAir = $fileName; // Set the path to the uploaded file
            } else {
                // Handle file upload error
                echo "There was an error uploading the file.";
            }
        }
    
        // Handle multiple checkboxes for upaya_peningkatan
        $upayaPeningkatan = isset($_POST['listUpaya']) ? $_POST['listUpaya'] : [];
    
        // Prepare data for POST request
        $data = [
            "nama_sumber_air" => $namaSumberAir,
            "kondisi_sumber_air" => $kondisiSumberAir,
            "suhu" => $suhu,
            "warna" => $warna,
            "ph" => $ph,
            "kelayakan" => $kelayakan,
            "id_jenis_sumber_air" => $idJenisSumberAir,
            "id_kabupaten" => $idKabupaten,
            "foto_sumber_air" => $fotoSumberAir,  // Path to uploaded image file
            "upaya_peningkatan" => $upayaPeningkatan,  // Array of upaya selected
        ];
    
        // Set the options for the HTTP request
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
            ],
        ];
    
        // Create the stream context
        $context  = stream_context_create($options);
    
        // Send the POST request to the API
        $response = file_get_contents($urlWaterCreate, false, $context);
    
        // Handle API response, for example check if the creation was successful
        if ($response) {
            // Optionally, decode the response if it's JSON
            $responseData = json_decode($response, true);
            if ($responseData) {
                // Redirect back to admin page or show a success message
                header("Location: admin.php");
                exit;
            } else {
                echo "Failed to add water source.";
            }
        } else {
            echo "Error in API request.";
        }
    }
    // include('function.php');
    // $listSumberAir = readSumberAir();
    // $r_wilayah = readTable('wilayah');
    // echo '<pre>';
    // print_r($r_jenis);

    // foreach($r_jenis as $jenis) {
    //     echo $jenis['nama_jenis_sumber_air'];
    // }

    // echo "<br>";


// if (isset($_POST['submit-add'])) {

//     $listUpaya = $_POST['listUpaya'];

//   // jalankan query tambah record baru
//   $isAddSucceed = addWater($_POST, $_FILES, $listUpaya);
  
  
//   if ($isAddSucceed > 0) {
//     // jika penambahan sukses, tampilkan alert
//     echo "
//           <script>
              
//               alert('Data Berhasil di ditambahkan');
//               document.location.href = 'admin.php';
//           </script>
//       ";
//   } 
// }

// print_r($r_jenis);
// echo $isAddSucceed;
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="description" content="">
        <meta name="author" content="">

        <title>Topic Listing Contact Page</title>

        <!-- CSS FILES -->        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Open+Sans&display=swap" rel="stylesheet">
                        
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <link href="css/bootstrap-icons.css" rel="stylesheet">

        <link href="css/templatemo-topic-listing.css" rel="stylesheet">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>

	    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

        <!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script> -->
<!--

TemplateMo 590 topic listing

https://templatemo.com/tm-590-topic-listing

-->
    </head>
    
    <body class="topics-listing-page" id="top">

        <main>

        <nav class="navbar navbar-expand-lg">
                <div class="container">
                    <a class="navbar-brand" href="index.php">
                        <i class="bi-back"></i>
                        <span>HydroCulus</span>
                    </a>

                    <div class="d-lg-none ms-auto me-4">
                        <a href="#top" class="navbar-icon bi-person smoothscroll"></a>
                    </div>
    
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
    
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-lg-5 me-lg-auto">
                            <li class="nav-item">
                                <a class="nav-link click-scroll" href="index.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " href="dashboard.php#chart">Dashboard</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link " href="topics-listing.php" >List Sumber Air</a>
                            </li>

                            <li class="nav-item" >
                                <a class="nav-link"  href="upaya-listing.php" >List Upaya Pelestarian</a>
                            </li>
                        </ul>

                        <div class="d-none d-lg-block">
                            <a href="login.php" class="navbar-icon bi-person smoothscroll"></a>
                        </div>
                    </div>
                </div>
            </nav>


            <header class="site-header d-flex flex-column justify-content-center align-items-center">
                <div class="container">
                    <div class="row align-items-center">

                        <div class="col-lg-5 col-12">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Homepage</a></li>

                                    <li class="breadcrumb-item active" aria-current="page">Add Sumber Air</li>
                                </ol>
                            </nav>

                            <h2 class="text-white">Add Sumber Air</h2>
                        </div>

                    </div>
                </div>
            </header>


            <section class="section-padding section-bg">
                <div class="container">
                    <div class="row">

                        <div class="col-lg-12 col-12">
                            <h3 class="mb-4 pb-2">Data Sumber Air Baru</h3>
                        </div>

                        <div class="col-lg-6 col-12">
                            <form action="#" method="post" class="custom-form contact-form" role="form" id="form-update" enctype="multipart/form-data">
                                 
                                <div class="row">
                                    <div class="col-lg-12 col-12">
                                        <div class="form-floating">
                                            <input type="text" name="nama_sumber_air" id="name" class="form-control" placeholder="" required>
                                            
                                            <label for="floatingInput">Nama Sumber Air</label>
                                        </div>
                                    </div>

                                    

                                    <h6>Wilayah</h6>
                                    <div class="col-lg-6 col-md-6 col-12"> 
                                        <div class="form-floating">
                                            <select name="provinsi" id="provinsi" class="form-select" style="padding-top: 0px;padding-bottom: 0px;margin-bottom: 30px;" aria-label="Default select example">
                                                    <option disabled selected>Provinsi</option>
                                            
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-12"> 
                                        <div class="form-floating">
                                            <select name="kabupaten" id="kabupaten" class="form-select" style="padding-top: 0px;padding-bottom: 0px;margin-bottom: 30px;" aria-label="Default select example">
                                                    <option disabled selected>Kabupaten</option>
                                            
                                            </select>
                                        </div>
                                    </div>

                                    <!-- <br> -->
                                    <div class="col-lg-6 col-md-6 col-12"> 
                                        <div class="form-floating">
                                            <select name="jenis_sumber_air" class="form-select" style="padding-top: 0px;padding-bottom: 0px;margin-bottom: 30px;" aria-label="Default select example">
                                                    <option disabled selected>Jenis Sumber Air</option>
                                            <?php
                                                    foreach($r_jenis as $jenis) {
                                                ?>
                                                    <option value="<?=$jenis['_id']?>"> - <?=$jenis['nama_jenis_sumber_air']?></option>
                                                <?php  
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-floating">
                                            <select name="kondisi" class="form-select" style="padding-top: 0px;padding-bottom: 0px;margin-bottom: 30px;" aria-label="Default select example" required>
                                                <option disabled selected>Kondisi Sumber Air</option>
                                                <option value="Baik">Baik</option>
                                                <option value="Rusak Sedang">Rusak Sedang</option>
                                                <option value="Rusak Parah">Rusak Parah</option>
                                                
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-floating" >
                                            <input name="suhu" type="range" class="form-range" min="0" max="1000" value="250" id="suhu" style="padding-bottom: 40px;" oninput="this.nextElementSibling.value = this.value" required>
                                            <label for="customRange2" class="form-label">Suhu : <span id="outputSuhu"></span> &#8451;
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-12"> 
                                        <div class="form-floating">
                                            <input name="pH" type="range" class="form-range" min="0" max="140" value="70" id="pH" style="padding-bottom: 40px;" oninput="this.nextElementSibling.value = this.value" required>
                                            <label for="customRange2" class="form-label">pH : <span id="outputpH"></span></label>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-12"> 
                                        
                                        <div class="form-check">
                                            <div style="display: inline-block; margin-top: 20px;">
                                                <div style="float:left; margin: 0px 155px 0px 5px">
                                                    <p>Warna</p>
                                                </div>
                                                <div style="float:left; margin: 3px 5px 0px 5px">
                                                    <input class="form-check-input" type="radio" name="warna" id="warna1" value="Bening"; required>
                                                    <label class="form-check-label" for="warna1">
                                                        Bening
                                                    </label>
                                                </div>
                                                <div style="float:left; margin: 3px 5px 0px 5px">
                                                    <div class="form-check" style="margin-left: 50px">
                                                        <input class="form-check-input" type="radio" name="warna" id="warna2" value="Keruh" >
                                                        <label class="form-check-label" for="warna2">
                                                            Keruh
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    

                                    <div class="col-lg-12 col-12" > 
                                        
                                        <div class="form-check">
                                            <div style="display: inline-block;">
                                                <div style="float:left; margin: 0px 50px 0px 5px">
                                                    <p>Kelayakan Minum</p>
                                                </div>
                                                <div style="float:left; margin: 3px 5px 0px 5px">
                                                    <input class="form-check-input" type="radio" name="layak_minum" id="layak_minum1" value="Layak" required>
                                                    <label class="form-check-label" for="layak_minum1">
                                                        Layak
                                                    </label>
                                                </div>
                                                <div style="float:left; margin: 3px 5px 0px 5px">
                                                    <div class="form-check" style="margin-left: 59px">
                                                        <input class="form-check-input" type="radio" name="layak_minum" id="layak_minum2" value="Tidak" required>
                                                        <label class="form-check-label" for="layak_minum2">
                                                            Tidak Layak
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-12">
                                        <p>Upaya</p>
                                        <?php
                                            foreach($r_upaya as $upaya) {
                                        ?>

                                        <div class="form-check form-check-inline">
                                        <input 
                                        class="form-check-input" 
                                        type="checkbox" id="inlineCheckbox1" 
                                        value="<?=$upaya['_id']?>" 
                                        name="listUpaya[]"
                                            
                                        >
                                        <label class="form-check-label" for="inlineCheckbox1"><?=$upaya['nama_upaya']?></label>
                                        </div>

                                    
                                        <?php  
                                            }
                                        ?>
                            
                                        

                                    </div>

                                    

                                    

                                   

                                    <div class="col-lg-12 col-12 ms-auto">
                                        <button form="form-update" type="submit" class="form-control" name="submit-add" id="submit-add" style="margin-top: 12px;">Submit</button>
                                    </div>

                                </div>

                                
                                
                            </div>
                                <div class="col-lg-5 col-12 mx-auto mt-5 mt-lg-0">
                                    <div class="topics-detail-block bg-white shadow-lg" id="imgBox">
                                        <img id="myImg" src="images/foto_sumber_air/default.png" class="topics-detail-block-image img-fluid">
                                    </div>
                                    <br>
                                    
                                        <input type="file" name="image" id="image" accept="image/*" id="file" onchange="onFileSelected(event)">
                                    
                                </div>
                            </form>
                        </div>
                </div>
            </section>
        </main>

        <footer class="site-footer section-padding">
            <div class="container">
                <div class="row">

                    <div class="col-lg-3 col-12 mb-4 pb-2">
                        <a class="navbar-brand mb-2" href="index.php">
                            <i class="bi-back"></i>
                            <span>HydroCulus</span>
                        </a>
                    </div>

                    <div class="col-lg-3 col-md-4 col-6">
                        <h6 class="site-footer-title mb-3">Resources</h6>

                        <ul class="site-footer-links">
                            <li class="site-footer-link-item">
                                <a href="index.php" class="site-footer-link">Home</a>
                            </li>

                            <li class="site-footer-link-item">
                                <a href="topics-listing.php" class="site-footer-link">List Sumber Air</a>
                            </li>

                            <li class="site-footer-link-item">
                                <a href="#" class="site-footer-link">Upaya Melestarikan Sumber Air</a>
                            </li>

                            <li class="site-footer-link-item">
                                <a href="admin.php" class="site-footer-link">Login</a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-md-4 col-6 mb-4 mb-lg-0">
                        <h6 class="site-footer-title mb-3">Information</h6>

                        <p class="text-white d-flex mb-1">
                            <a href="tel:" class="site-footer-link">
                                17-08-1945
                            </a>
                        </p>

                        <p class="text-white d-flex">
                            <a href="mailto:info@company.com" class="site-footer-link">
                                hydroculus@sumberair.com
                            </a>
                        </p>
                    </div>

                    <div class="col-lg-3 col-md-4 col-12 mt-4 mt-lg-0 ms-auto">
                        <!-- <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            English</button>

                            <ul class="dropdown-menu">
                                <li><button class="dropdown-item" type="button">Thai</button></li>

                                <li><button class="dropdown-item" type="button">Myanmar</button></li>

                                <li><button class="dropdown-item" type="button">Arabic</button></li>
                            </ul>
                        </div> -->

                        <p class="copyright-text mt-lg-5 mt-4">Copyright © 2023 HydroCulus. <br> All rights reserved.
                        <!-- <br><br>Design: <a rel="nofollow" href="https://templatemo.com" target="_blank">TemplateMo</a></p> -->
                        
                    </div>

                </div>
            </div>
        </footer>
        <!-- slider -->
        <script>
            var sliderSuhu = document.getElementById("suhu");
            var outputSuhu = document.getElementById("outputSuhu");
            outputSuhu.innerHTML = sliderSuhu.value / 10.0;

            sliderSuhu.oninput = function() {
            outputSuhu.innerHTML = this.value / 10.0;
            }

            var sliderpH = document.getElementById("pH");
            var outputpH = document.getElementById("outputpH");
            outputpH.innerHTML = sliderpH.value / 10.0;

            sliderpH.oninput = function() {
            outputpH.innerHTML = this.value / 10.0;
            }
        </script>

        <script>
            $(document).ready(function () {
                $('#submit-add').click(function() {
                    checked = $("input[type=checkbox]:checked").length;

                    if(!checked) {
                        alert("You must check at least one checkbox.");
                        return false;
                    }
                });
            });
        </script>

        <script>
            function onFileSelected(event) {
            var selectedFile = event.target.files[0];
            var reader = new FileReader();

            var imgtag = document.getElementById("myImg");
            imgtag.title = selectedFile.name;

            reader.onload = function(event) {
                imgtag.src = event.target.result;
            };

            reader.readAsDataURL(selectedFile);
            }
        </script>

            <!-- skrip bootstrap dan script jquery -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>


	<script type="text/javascript">
		$(document).ready(function() {
			// hilangkan label dan tag select yang belum dibutuhkan
			// $("#lbl_kabupaten").hide();
			$("#kabupaten").hide();
			//$("#kecamatan").hide();
			// $("#lbl_kecamatan").hide();
			// $("#kelurahan").hide();
			// $("#lbl_kelurahan").hide();
			
			// menambahkan option ke elemen yang memiliki id = “provinsi” dan mengosongkan element yang memiliki id = “kabupaten”.
			$("#provinsi").append('<option value="">Pilih</option>');
			$("#kabupaten").html('');

			// implementasi kode jquery
			url = 'get_provinsi.php';
			$.ajax({
				url: url,
				type: 'GET',
				dataType: 'json',
				success: function(result) {
					// apa yang akan dilakukan pada data json yang sudah digenerate
					for(var i=0; i<result.length; i++) 
						$("#provinsi").append('<option value="' + result[i].id_prov + '">' + result[i].nama + '</option>');
				}
			});
		});

		$("#provinsi").change(function() {
			$("#lbl_kabupaten").slideDown();
			$("#kabupaten").slideDown();

			// fetch id provinsi
			var id_prov = $("#provinsi").val();
			// masukkan id provinsi ke url kabupaten
			var url = 'get_kabupaten.php?id_prov=' + id_prov;

			$("#kabupaten").html('');
			$.ajax({
				url: url,
				type: 'GET',
				dataType: 'json',
				success: function(result) {
					$("#kabupaten").append('<option value="">Pilih</option>');
					for (var i = 0; i < result.length; i++) {
							// apa yang akan dilakukan pada data json yang sudah digenerate
							$("#kabupaten").append('<option value="' + result[i].id_kab + '">' + result[i].nama + '</option>');
					}
				}
			});
		});

		// jika nilai/value dari element yang memiliki id = “provinsi” muncul kecamatan dan kelurahan
		$("#kabupaten").change(function() {
			$("#kecamatan").slideDown();
			$("#lbl_kecamatan").slideDown();
			$("#kelurahan").slideDown();
			$("#lbl_kelurahan").slideDown();
		})
	</script>


        <!-- JAVASCRIPT FILES -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>
        <script src="js/jquery.sticky.js"></script>
        <script src="js/custom.js"></script>

    </body>
</html>
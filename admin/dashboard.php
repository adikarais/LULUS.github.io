<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- Bootstrap CSS -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
          integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
          
   <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

   <!-- Leaflet CSS -->
   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
          integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
          crossorigin="" />

   <style>
        /* Ukuran peta */
        #mapid {
            height: 100%;
        }

        .jumbotron {
            height: 100%;
            border-radius: 0;
        }

        body {
            background-color: #ebe7e1;
        }
         .container-fluid {
            width: 100%;
            padding-right: 0;
            padding-left: 15px;
            margin-right: 0;
            margin-left: auto;
         }
        
         .row {
             margin-right: 0;
         }

         .form-control {
            font-size: 1.5rem;
        }

        /* .orm-group {
            font-size: 2rem;
        } */

    </style>
</head>
<style>
   .leaflet-top, .leaflet-bottom {
    position: absolute;
    z-index: 900;
    pointer-events: none;
   }
</style>
<body>

<?php include '../components/admin_header.php'; ?>
   
<div class="container-fluid">
    <div class="row">
        <!-- Bagian kiri: Form -->
        <div class="col-md-4" style="padding-right: 0; padding-left: 0; height: 100vh;">
            <div class="jumbotron" style="font-size: large;">
                <h1 class="display-4">Add Location</h1>
                <hr class="my-4">
                <form action="proses.php" method="post">
                    <div class="form-group">
                        <label for="latlong">Latitude, Longitude</label>
                        <input type="text" class="form-control" id="latlong" name="latlong" placeholder="Klik pada peta untuk mendapatkan koordinat">
                    </div>
                    <div class="form-group">
                        <label for="link_sekolah">Link Web Sekolah</label>
                        <input type="text" class="form-control" name="link_sekolah" placeholder="Masukkan link web sekolah">
                    </div>
                    <div class="form-group">
                        <label for="link_lokasi">Link Lokasi Sekolah</label>
                        <input type="text" class="form-control" name="link_lokasi" placeholder="Masukkan Link lokasi sekolah">
                    </div>
                    <div class="form-group">
                        <label for="nama_tempat">Nama Sekolah</label>
                        <input type="text" class="form-control" name="nama_tempat" placeholder="Masukkan nama tempat">
                    </div>
                    <div class="form-group">
                        <label for="kategori">Kategori Sekolah</label>
                        <select class="form-control" name="kategori" id="kategori">
                            <option value="">-- Pilih Kategori Sekolah --</option>
                            <option value="SMA">SMA</option>
                            <option value="SMK">SMK</option>
                            <option value="MA">MA</option>
                        </select>
                    </div>
                    <div class="form-group">
                       <!-- Dropdown untuk memilih kecamatan di Kabupaten Klaten -->
                    <label for="nama_kecamatan">Kecamatan</label>
                    <select class="form-control" name="nama_kecamatan" id="nama_kecamatan">
                        <option value="">-- Pilih Kecamatan --</option> <!-- Opsi default kosong -->
                        <!-- Daftar lengkap kecamatan di Kabupaten Klaten -->
                        <option value="Bayat">Bayat</option>
                        <option value="Cawas">Cawas</option>
                        <option value="Ceper">Ceper</option>
                        <option value="Delanggu">Delanggu</option>
                        <option value="Gantiwarno">Gantiwarno</option>
                        <option value="Jatinom">Jatinom</option>
                        <option value="Jogonalan">Jogonalan</option>
                        <option value="Juwiring">Juwiring</option>
                        <option value="Kalikotes">Kalikotes</option>
                        <option value="Karanganom">Karanganom</option> 
                        <option value="Karangdowo">Karangdowo</option>
                        <option value="Karangnongko">Karangnongko</option>
                        <option value="Kebonarum">Kebonarum</option>
                        <option value="Kemalang">Kemalang</option>
                        <option value="Klaten Utara">Klaten Utara</option>
                        <option value="Klaten Tengah">Klaten Tengah</option>
                        <option value="Klaten Selatan">Klaten Selatan</option>
                        <option value="Manisrenggo">Manisrenggo</option>
                        <option value="Ngawen">Ngawen</option>
                        <option value="Pedan">Pedan</option>
                        <option value="Polanharjo">Polanharjo</option>
                        <option value="Prambanan">Prambanan</option>
                        <option value="Trucuk">Trucuk</option>
                        <option value="Tulung">Tulung</option>
                        <option value="Wedi">Wedi</option>
                        <option value="Wonosari">Wonosari</option>
                    </select>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control" name="keterangan" id="keterangan" cols="30" rows="5" placeholder="Masukkan keterangan"></textarea>
                    </div>
                    <button type="submit" class="btn btn-info btn-block">Add</button>
                </form>
            </div>
        </div>
        
        <!-- Bagian kanan: Peta -->
        <div class="col-md-8" style="padding-right: 0; padding-left: 0; height: 80vh;">
            
            <div id="mapid" style="position: relative;">
                <div style="position: absolute; bottom: 0; left: 0; width: 100%;">
                    <div id="filterControl" class="leaflet-control" style="position: relative; display: inline-block; margin: 10px; z-index: 1000; background: white; padding: 10px; border-radius: 8px; box-shadow: 0 1px 5px rgba(0,0,0,0.4);">
                        <label for="filterKategori">Filter Sekolah:</label>
                        <select id="filterKategori" class="form-control mt-1">
                            <option value="all">Semua</option>
                            <option value="SMA">SMA</option>
                            <option value="SMK">SMK</option>
                            <option value="MA">MA</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
        integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
        crossorigin=""></script>
<script>
    // Inisialisasi peta dengan lokasi awal (klaten)
    var mymap = L.map('mapid').setView([-7.705123, 110.601683], 12);

    // Tambahkan tile layer dari OpenStreetMap
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        maxZoom: 20
    }).addTo(mymap);

    // Tambahkan tombol untuk menampilkan lokasi pengguna   
    var locateButton = L.control({position: 'bottomright'});

    locateButton.onAdd = function (map) {
        var div = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
        div.innerHTML = '<button style="background-color: white; border: none; padding: 5px; cursor: pointer;">📍</button>';
        div.onclick = function () {
            map.locate({setView: true, maxZoom: 15});
        };
        return div;
    };

    locateButton.addTo(mymap);

    // Event untuk menangani lokasi pengguna
    // mymap.on('locationfound', function (e) {
    //     L.marker(e.latlng).addTo(mymap)
    //         .bindPopup("Anda berada di sini").openPopup();
    // });

    // Handle user location events
    mymap.on('locationfound', function (e) {
        var userIcon = L.divIcon({
            html: `<i class="fas fa-street-view" style="font-size: 48px; color: red;"></i>`,
            className: '',
            iconSize: [48, 48],
            iconAnchor: [24, 48]
        });

        L.marker(e.latlng, { icon: userIcon }).addTo(mymap)
            .bindPopup("Anda berada di sini").openPopup();
        showNearbyLocations(e.latlng.lat, e.latlng.lng);
    });

    mymap.on('locationerror', function (e) {
        alert("Lokasi tidak dapat ditemukan: " + e.message);
    });

    // Tambahkan elemen untuk daftar lokasi di bawah peta
    var locationList = document.createElement('div');
    locationList.id = 'locationList';
    locationList.style.marginTop = '10px';
    locationList.style.padding = '10px';
    locationList.style.backgroundColor = '#fff';
    locationList.style.border = '1px solid #ccc';
    locationList.style.borderRadius = '5px';
    locationList.style.maxHeight = '200px';
    locationList.style.height = '20vh';
    locationList.style.overflowY = 'auto';
    document.querySelector('.col-md-8').appendChild(locationList);

    // Fungsi untuk menghitung jarak antara dua koordinat
    function calculateDistance(lat1, lon1, lat2, lon2) {
        var R = 6371; // Radius bumi dalam kilometer
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLon = (lon2 - lon1) * Math.PI / 180;
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c; // Jarak dalam kilometer
    }

    // Fungsi untuk menampilkan lokasi di sekitar pengguna
    function showNearbyLocations(userLat, userLng) {
        var locations = <?php
            $mysqli = mysqli_connect('localhost', 'root', '', 'ta_wgis'); // Koneksi ke database
            $locations = [];
            $tampil = mysqli_query($mysqli, "SELECT * FROM lokasi");
            while ($hasil = mysqli_fetch_array($tampil)) {
                $locations[] = [
                    'id' => $hasil['id'],
                    'lat' => floatval(explode(',', $hasil['lat_long'])[0]),
                    'lng' => floatval(explode(',', $hasil['lat_long'])[1]),
                    'nama_tempat' => $hasil['nama_tempat'],
                    'kategori' => $hasil['kategori'],
                    'nama_kecamatan' => $hasil['nama_kecamatan'],
                    'keterangan' => $hasil['keterangan']
                ];
            }
            echo json_encode($locations);
        ?>;

        locationList.innerHTML = '<h5>Lokasi di Sekitar Anda:</h5>';
        var nearbyLocations = locations.filter(function (loc) {
            return calculateDistance(userLat, userLng, loc.lat, loc.lng) <= 5; // Radius 5 km
        });

        if (nearbyLocations.length === 0) {
            locationList.innerHTML += '<p>Tidak ada lokasi di sekitar Anda.</p>';
        } else {
            var ul = document.createElement('ul');
            ul.style.listStyleType = 'none';
            ul.style.padding = '0';
            nearbyLocations.forEach(function (loc) {
                var li = document.createElement('li');
                li.style.marginBottom = '10px';
                li.innerHTML = `
                    <strong>${loc.nama_tempat}</strong> (${loc.kategori})<br>
                    ${loc.keterangan}<br>
                    <small>Jarak: ${calculateDistance(userLat, userLng, loc.lat, loc.lng).toFixed(2)} km</small>
                `;
                ul.appendChild(li);
            });
            locationList.appendChild(ul);
        }
    }




    // Event untuk menampilkan lokasi di sekitar pengguna
    mymap.on('locationfound', function (e) {
        showNearbyLocations(e.latlng.lat, e.latlng.lng);
    });


    // Variabel untuk popup
    var popup = L.popup();

    // Fungsi untuk menampilkan popup dan mengisi koordinat ke input form
    function onMapClick(e) {
        popup
            .setLatLng(e.latlng)
            .setContent("Koordinat: " + e.latlng.toString())
            .openOn(mymap);
        document.getElementById('latlong').value = e.latlng.lat + ", " + e.latlng.lng;
    }

    // Event listener untuk klik pada peta
    mymap.on('click', onMapClick);

    // Menambahkan warna Persebaran sekolah di pojok kanan atas
      var legend = L.control({position: 'topright'});
      legend.onAdd = function (map) {
         var div = L.DomUtil.create('div', 'info legend');
         div.style.backgroundColor = 'white';
         div.style.padding = '10px';
         div.style.borderRadius = '5px';
         div.style.border = '1px solid #ccc';
         div.innerHTML = '<h4>Sekolah</h4>' +
            '<div style="display: flex; align-items: center; margin-bottom: 5px;"><div style="width: 20px; height: 20px; background-color: blue; margin-right: 10px; border: 1px solid #000;"></div><h4>SMA</h4></div>' +
            '<div style="display: flex; align-items: center; margin-bottom: 5px;"><div style="width: 20px; height: 20px; background-color: orange; margin-right: 10px; border: 1px solid #000;"></div><h4>SMK</h4></div>' +
            '<div style="display: flex; align-items: center; margin-bottom: 5px;"><div style="width: 20px; height: 20px; background-color: green; margin-right: 10px; border: 1px solid #000;"></div><h4>MA</h4></div>' +
            '<div style="display: flex; align-items: center; margin-bottom: 5px;"><div style="width: 20px; height: 20px; background-color: grey; margin-right: 10px; border: 1px solid #000;"></div><h4> ? </h4></div>';
         return div;
      };
      legend.addTo(mymap);

    // Display All Markers from DB with Filtering
   var allMarkers = [];

   <?php
   $tampil = mysqli_query($mysqli, "SELECT * FROM lokasi");
   while ($hasil = mysqli_fetch_array($tampil)) {
      $latLng = str_replace(['[', ']', 'LatLng', '(', ')'], '', $hasil['lat_long']);
      $kategori = $hasil['kategori'];
      $iconColor = $kategori === 'SMA' ? 'blue' : ($kategori === 'SMK' ? 'orange' : ($kategori === 'MA' ? 'green' : 'grey'));
   ?>
   var marker = L.marker([<?php echo $latLng; ?>], {
      icon: L.divIcon({
         html: `<i class="fa fa-map-marker-alt" style="color: <?php echo $iconColor; ?>; font-size: 30px;"></i>`,
         className: '',
         iconSize: [24, 24],
         iconAnchor: [12, 24]
      })
   }).bindPopup(`
   <strong style="font-size: 1.5rem;">Nama Sekolah : </strong> <span style="font-size: 1.5rem;"><?php echo htmlspecialchars($hasil['nama_tempat']); ?></span><br>
   <strong style="font-size: 1.5rem;">Kategori : </strong> <span style="font-size: 1.5rem;"><?php echo htmlspecialchars($hasil['kategori']); ?></span><br>
   <strong style="font-size: 1.5rem;">Alamat : </strong> <span style="font-size: 1.5rem;"><?php echo htmlspecialchars($hasil['keterangan']); ?></span><br>
   <div class="d-flex justify-content-between">
      <a href="detail.php?id=<?php echo urlencode($hasil['id']); ?>" class="btn btn-sm btn-primary mt-2 text-white">Detail</a>
      <a href="<?php echo htmlspecialchars($hasil['link_lokasi']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary mt-2 text-white">Lokasi</a>
   </div>
   <button onclick="deleteLocation(<?php echo $hasil['id']; ?>)" class="btn btn-sm btn-danger mt-2 w-100">Hapus</button>
`);
   marker.kategori = "<?php echo $hasil['kategori']; ?>";
   marker.addTo(mymap);
   allMarkers.push(marker);
   <?php } ?>

   // Filter Handler
   document.getElementById('filterKategori').addEventListener('change', function () {
      var selected = this.value;

      allMarkers.forEach(marker => {
         if (selected === 'all' || marker.kategori === selected) {
            marker.addTo(mymap);
         } else {
            mymap.removeLayer(marker);
         }
      });
   });
   
     // Fungsi untuk menghapus lokasi berdasarkan ID
     function deleteLocation(id) {
        if (confirm('Apakah Anda yakin ingin menghapus lokasi ini?')) {
            // Kirim permintaan AJAX untuk menghapus lokasi
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'delete_location.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert('Lokasi berhasil dihapus.');
                    location.reload(); // Muat ulang halaman
                }
            };
            xhr.send('id=' + id);
        }
    }
</script>


<script src="../js/admin_script.js"></script>

</body>
</html>
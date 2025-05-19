<?php
// // Menyertakan file koneksi database
// include 'components/connect.php';

// // Check if user is logged in
// if(!isset($_COOKIE['tutor_id'])){
//     header('location:login.php');
//     exit();
// }


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
   <!-- Meta tags untuk pengaturan dasar halaman web -->
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>

   <!-- Menyertakan CSS Bootstrap -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
         integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

   <!-- Menyertakan Font Awesome untuk ikon -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- CSS kustom -->
   <link rel="stylesheet" href="../css/admin_style.css">

   <!-- CSS Leaflet untuk peta -->
   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<style>
   .leaflet-popup-content {
    margin: 13px 24px 13px 20px;
    line-height: 1.3;
    font-size: 13px;
    font-size: 1.5rem;
    min-height: 1px;
}
</style>
<body>

<!-- Menyertakan header user -->
<?php include '../components/admin_header.php'; ?>

<!-- Container untuk menampilkan peta -->
<div id="mapid" style="height: 85vh; width: 100%;"></div>

<!-- JavaScript Leaflet untuk fungsi peta -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
   // Inisialisasi peta dengan view default di Koordinat Klaten
   var mymap = L.map('mapid').setView([-7.705123, 110.601683], 12);

   // Menambahkan tile layer dari OpenStreetMap
   L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
      maxZoom: 20
   }).addTo(mymap);

   // Membuat tombol untuk melacak lokasi user
   var locateButton = L.control({position: 'bottomright'});
   locateButton.onAdd = function (map) {
      var div = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
      div.innerHTML = '<button style="background-color: white; border: none; padding: 1rem; cursor: pointer; font-size: large;">📍</button>';
      div.onclick = function () {
         map.locate({setView: true, maxZoom: 15});
      };
      return div;
   };
   locateButton.addTo(mymap);

   // Fungsi yang dijalankan ketika lokasi user ditemukan
   mymap.on('locationfound', function (e) {
      // Membuat ikon khusus untuk menandai posisi user
      var userIcon = L.divIcon({
         html: `<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="red" class="bi bi-geo-fill" viewBox="0 0 16 16"><path fill-rule="evenodd" d="..."/></svg>`,
         className: '',
         iconSize: [48, 48],
         iconAnchor: [24, 48]
      });

      // Menambahkan marker untuk posisi user
      L.marker(e.latlng, { icon: userIcon }).addTo(mymap)
         .bindPopup("Anda berada di sini").openPopup();

      // Menampilkan lokasi terdekat dari posisi user
      showNearbyLocations(e.latlng.lat, e.latlng.lng);
   });

   // Fungsi yang dijalankan jika gagal menemukan lokasi user
   mymap.on('locationerror', function (e) {
      alert("Lokasi tidak dapat ditemukan: " + e.message);
   });

   // Fungsi untuk menghitung jarak antara dua koordinat (dalam km)
   function calculateDistance(lat1, lon1, lat2, lon2) {
      var R = 6371; // Radius bumi dalam km
      var dLat = (lat2 - lat1) * Math.PI / 180;
      var dLon = (lon2 - lon1) * Math.PI / 180;
      var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
      var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      return R * c;
   }

   // Fungsi untuk menampilkan lokasi terdekat dari posisi user
   function showNearbyLocations(userLat, userLng) {
      // Mengambil data lokasi dari database PHP
      var locations = <?php
         $mysqli = mysqli_connect('localhost', 'root', '', 'ta_wgis');
         if (!$mysqli) {
             die("Koneksi database gagal: " . mysqli_connect_error());
         }
         $locations = [];
         $tampil = mysqli_query($mysqli, "SELECT * FROM lokasi");
         while ($hasil = mysqli_fetch_array($tampil)) {
            $latLng = explode(',', str_replace(['[', ']', 'LatLng', '(', ')'], '', $hasil['lat_long']));
            if (count($latLng) === 2) {
               $locations[] = [
                  'id' => $hasil['id'],
                  'lat' => floatval($latLng[0]),
                  'lng' => floatval($latLng[1]),
                  'nama_tempat' => addslashes($hasil['nama_tempat']),
                  'kategori' => addslashes($hasil['kategori']),
                  'keterangan' => addslashes($hasil['keterangan'])
               ];
            }
         }
         echo json_encode($locations);
      ?>;

      // Membuat atau mengupdate daftar lokasi terdekat
      var locationList = document.getElementById('locationList');
      if (!locationList) {
         locationList = document.createElement('div');
         locationList.id = 'locationList';
         locationList.style.cssText = `
            padding: 10px;
            position: absolute;
            bottom: 10px;
            left: 10px;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            max-height: 200px;
            overflow-y: auto;
         `;
         document.body.appendChild(locationList);
      } else {
         locationList.innerHTML = '';
      }

      // Filter lokasi yang berada dalam radius 5km dari user
      var nearbyLocations = locations.filter(loc => calculateDistance(userLat, userLng, loc.lat, loc.lng) <= 5);

      if (nearbyLocations.length === 0) {
         locationList.innerHTML = '<p>Tidak ada lokasi di sekitar Anda.</p>';
      } else {
         var ul = document.createElement('ul');
         ul.style.listStyle = 'none';
         ul.style.padding = '0';

         // Menambahkan setiap lokasi terdekat ke dalam list
         nearbyLocations.forEach(loc => {
            var li = document.createElement('li');
            li.style.marginBottom = '10px';
            li.innerHTML = `
               <strong>${loc.nama_tempat}</strong> (${loc.kategori})<br>
               ${loc.keterangan}<br>
               <small>Jarak: ${calculateDistance(userLat, userLng, loc.lat, loc.lng).toFixed(2)} km</small>
            `;
            ul.appendChild(li);
         });

         locationList.innerHTML = '';
         locationList.style.position = 'relative';
         locationList.style.marginTop = '10px';
         locationList.appendChild(ul);
      }
   }

   // Menambahkan marker untuk semua lokasi dari database
   <?php
   $tampil = mysqli_query($mysqli, "SELECT * FROM lokasi");
   while ($hasil = mysqli_fetch_array($tampil)) {
      $latLng = str_replace(['[', ']', 'LatLng', '(', ')'], '', $hasil['lat_long']);
      $kategori = $hasil['kategori'];
      $iconColor = $kategori === 'SMA' ? 'blue' : ($kategori === 'SMK' ? 'grey' : ($kategori === 'MA' ? 'green' : 'red'));
   ?>
   L.marker([<?php echo $latLng; ?>], {
      icon: L.divIcon({
         html: `<i class="fa fa-map-marker" style="color: <?php echo $iconColor; ?>; font-size: 30px;"></i>`,
         className: '',
         iconSize: [24, 24],
         iconAnchor: [12, 24]
      })
   }).addTo(mymap).bindPopup(`
      <strong style="font-size: 1.5rem;">Nama Tempat : </strong> <span style="font-size: 1.5rem;"><?php echo $hasil['nama_tempat']; ?></span><br>
      <strong style="font-size: 1.5rem;">Kategori : </strong> <span style="font-size: 1.5rem;"><?php echo $hasil['kategori']; ?></span><br>
      <strong style="font-size: 1.5rem;">Keterangan : </strong> <span style="font-size: 1.5rem;"><?php echo $hasil['keterangan']; ?></span><br>
      <div class="d-flex justify-content-between">
         <a href="detail.php?id=<?php echo urlencode($hasil['id']); ?>" class="btn btn-sm btn-primary mt-2 text-white">Detail</a>
         <a href="<?php echo htmlspecialchars($hasil['link_lokasi']); ?>" target="_blank" class="btn btn-sm btn-primary mt-2 text-white">Lokasi</a>
      </div>
   `);
   <?php } ?>

   // Variabel untuk menyimpan warna kecamatan
   let districtColors = {};

   // Mengambil data warna kecamatan dari file PHP
   fetch('json_kecamatan_warna.php')
      .then(response => response.json())
      .then(data => {
         districtColors = data;
         addGeoJSON();
      });

   // Fungsi untuk mendapatkan warna berdasarkan nama kecamatan
   function getColorByDistrict(district) {
      return districtColors[district] || "#ffffff"; // Warna default jika tidak ditemukan
   }

   // Fungsi untuk menambahkan layer GeoJSON kecamatan
   function addGeoJSON() {
      fetch('../json/klaten.geojson')
         .then(response => response.json())
         .then(geojson => {
            L.geoJSON(geojson, {
               style: function(feature) {
                  return {
                     color: "##000000",
                     weight: 0.5,
                     opacity: 1,
                     fillOpacity: 1,
                     fillColor: getColorByDistrict(feature.properties.NAME_3)
                  };
               },
               onEachFeature: function(feature, layer) {
                  if (feature.properties && feature.properties.NAME_3) {
                     layer.bindPopup("<b></b> " + feature.properties.NAME_3);
                  }
               }
            }).addTo(mymap);
         });
   }
</script>

<!-- Menyertakan script JavaScript kustom -->
<script src="../js/script.js"></script>

</body>
</html>
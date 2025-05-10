<?php
// Include database connection
include 'components/connect.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>

   <!-- Font Awesome CDN Link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS File Link -->
   <link rel="stylesheet" href="css/style.css">

   <!-- bootstrap css file link  -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

   <!-- Leaflet CSS -->
   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>

<body>

<?php include 'components/user_header.php'; ?>

<!-- Map Section -->
<div id="mapid" style="height: 85vh; width: 100%;"></div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
   // Initialize the map with default coordinates (Yogyakarta)
   var mymap = L.map('mapid').setView([-7.705123, 110.601683], 12);

   // Add OpenStreetMap tile layer
   L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
      maxZoom: 20
   }).addTo(mymap);

   // Add a button to locate the user's position
   var locateButton = L.control({position: 'bottomright'});
   locateButton.onAdd = function (map) {
      var div = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
      div.innerHTML = '<button style="background-color: white; border: none; padding: 1rem; cursor: pointer; font-size: large;">📍</button>';
      div.onclick = function () {
         map.locate({setView: true, maxZoom: 15});
      };
      return div;
   };
   // Menambahkan tombol untuk menemukan lokasi pengguna ke peta
   locateButton.addTo(mymap);

   // Handle user location events
   mymap.on('locationfound', function (e) {
      var userIcon = L.divIcon({
         html: `<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="red" class="bi bi-geo-fill" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.3.3 0 0 0-.37.265.3.3 0 0 0-.057.09V14l.002.008.016.033a.6.6 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.6.6 0 0 0 .146-.15l.015-.033L12 14v-.004a.3.3 0 0 0-.057-.09 1.3 1.3 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465s-2.462-.172-3.34-.465c-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411"/>
               </svg>`,
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

   // Function to calculate distance between two coordinates
   function calculateDistance(lat1, lon1, lat2, lon2) {
      var R = 6371; // Earth's radius in kilometers
      var dLat = (lat2 - lat1) * Math.PI / 180;
      var dLon = (lon2 - lon1) * Math.PI / 180;
      var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
      var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      return R * c; // Distance in kilometers
   }

   // Function to display nearby locations
   function showNearbyLocations(userLat, userLng) {
      var locations = <?php
         $mysqli = mysqli_connect('localhost', 'root', '', 'ta_wgis');
         if (!$mysqli) {
         $tampil = mysqli_query($mysqli, "SELECT * FROM lokasi");
         if (!$tampil) {
             die("Query failed: " . mysqli_error($mysqli));
         }
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

      // Mendapatkan elemen untuk menampilkan daftar lokasi
      var locationList = document.getElementById('locationList');
      if (!locationList) {
         locationList = document.createElement('div');
         locationList.id = 'locationList';
         locationList.style.padding = '10px';
         locationList.style.position = 'absolute';
         locationList.style.bottom = '10px';
         locationList.style.left = '10px';
         locationList.style.backgroundColor = 'white';
         locationList.style.border = '1px solid #ccc';
         locationList.style.borderRadius = '5px';
         locationList.style.maxHeight = '200px';
         locationList.style.overflowY = 'auto';
         document.body.appendChild(locationList);
      } else {
         locationList.innerHTML = ''; // Clear previous content
      }

      // Filter lokasi yang berada dalam radius 5 km dari lokasi pengguna
      var nearbyLocations = locations.filter(function (loc) {
         return calculateDistance(userLat, userLng, loc.lat, loc.lng) <= 5; // Radius 5 km
      });

      if (nearbyLocations.length === 0) {
         locationList.innerHTML = '<p>Tidak ada lokasi di sekitar Anda.</p>';
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

          // Move the location list below the map
          locationList.style.position = 'relative';
          locationList.style.bottom = 'auto';
          locationList.style.left = 'auto';
          locationList.style.marginTop = '10px';
          locationList.style.fontSize = 'medium';
          var mapContainer = document.getElementById('mapid');
         locationList.appendChild(ul);
         locationList.innerHTML = ''; // Clear previous content
         locationList.appendChild(ul);
      }
   }

   // Add markers from the database
   <?php
   $mysqli = mysqli_connect('localhost', 'root', '', 'ta_wgis');
   $tampil = mysqli_query($mysqli, "SELECT * FROM lokasi");
   while ($hasil = mysqli_fetch_array($tampil)) {
      $latLng = str_replace(['[', ']', 'LatLng', '(', ')'], '', $hasil['lat_long']);
      $kategori = $hasil['kategori'];
      $iconColor = '';

      // Set marker color based on category
      if ($kategori === 'SMA') {
         $iconColor = 'blue';
      } elseif ($kategori === 'SMK') {
         $iconColor = 'grey';
      } elseif ($kategori === 'MA') {
         $iconColor = 'green';
      }
   ?>
      L.marker([<?php echo $latLng; ?>], {
         icon: L.divIcon({
            html: `<i class="fa fa-map-marker" style="color: <?php echo $iconColor; ?>; font-size: 30px;"></i>`,
            className: '',
            iconSize: [24, 24],
            iconAnchor: [12, 24]
         })
      })
      .addTo(mymap)
      .bindPopup(`
         <strong>Nama Tempat:</strong> <?php echo $hasil['nama_tempat']; ?><br>
         <strong>Kategori:</strong> <?php echo $hasil['kategori']; ?><br>
         <strong>Keterangan:</strong> <?php echo $hasil['keterangan']; ?><br>
         <a href="detail.php?id=<?php echo $hasil['id']; ?>" class="btn btn-sm btn-primary mt-2 text-white">Detail</a>
      `);
   <?php } ?>
</script>





<!-- Footer Section -->
<?php // include 'components/footer.php'; ?>

<!-- Custom JS File Link -->
<script src="js/script.js"></script>

</body>
</html>

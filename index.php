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
   

   <!-- Bootstrap CSS -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
         integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/style.css">

   <!-- Leaflet CSS -->
   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<style>
   .leaflet-top, .leaflet-bottom {
    position: absolute;
    z-index: 900;
    pointer-events: none;
   }
</style>
<body>

<?php include 'components/user_header.php'; ?>

<div id="mapid" style="height: 85vh; width: 100%; position: relative;">  <!-- Tambahkan position: relative -->
   <!-- Filter Control -->
   <div id="filterControl" class="leaflet-control" style="position: absolute; bottom: 10px; left: 10px; z-index: 1000; background: white; padding: 10px; border-radius: 8px; box-shadow: 0 1px 5px rgba(0,0,0,0.4);">
      <label for="filterKategori">Filter Sekolah:</label>
      <select id="filterKategori" class="form-control mt-1">
         <option value="all">Semua</option>
         <option value="SMA">SMA</option>
         <option value="SMK">SMK</option>
         <option value="MA">MA</option>
      </select>
   </div>
</div>



<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
   var mymap = L.map('mapid').setView([-7.705123, 110.601683], 12);

   L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
      maxZoom: 20
   }).addTo(mymap);

   // Locate Button
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

   function calculateDistance(lat1, lon1, lat2, lon2) {
      var R = 6371;
      var dLat = (lat2 - lat1) * Math.PI / 180;
      var dLon = (lon2 - lon1) * Math.PI / 180;
      var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
      var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      return R * c;
   }

   function showNearbyLocations(userLat, userLng) {
      var locations = <?php
         $mysqli = mysqli_connect('localhost', 'root', '', 'ta_wgis');
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
         echo json_encode($locations, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
      ?>;

      var locationList = document.getElementById('locationList');
      if (!locationList) {
         locationList = document.createElement('div');
         locationList.id = 'locationList';
         locationList.style.cssText = `
            padding: 10px;
            position: absolute;
            bottom: 10px;
            right: 10px;
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

      var nearbyLocations = locations.filter(loc => calculateDistance(userLat, userLng, loc.lat, loc.lng) <= 5);

      if (nearbyLocations.length === 0) {
         locationList.innerHTML = '<p>Tidak ada lokasi di sekitar Anda.</p>';
      } else {
         var ul = document.createElement('ul');
         ul.style.listStyle = 'none';
         ul.style.padding = '0';

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
</script>

<!-- Footer -->
<?php // include 'components/footer.php'; ?>

<!-- Custom JS -->
<script src="js/script.js"></script>

</body>
</html>

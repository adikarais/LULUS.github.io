<?php
// Menyertakan file koneksi database
include 'components/connect.php';

// menghitung kategori
$mysqli = mysqli_connect('localhost', 'root', '', 'ta_wgis');
$kategoriCounts = [];
$tampilKategori = mysqli_query($mysqli, "SELECT kategori, COUNT(*) as jumlah FROM lokasi GROUP BY kategori");
while ($row = mysqli_fetch_assoc($tampilKategori)) {
   $kategoriCounts[$row['kategori']] = $row['jumlah'];
}

// menghitung kecamatan
$kecamatanCounts = [];
$tampilKecamatan = mysqli_query($mysqli, "SELECT nama_kecamatan, COUNT(*) as jumlah FROM lokasi GROUP BY nama_kecamatan");
while ($row = mysqli_fetch_assoc($tampilKecamatan)) {
   $kecamatanCounts[$row['nama_kecamatan']] = $row['jumlah'];
}

// Query untuk tabel pivot kecamatan vs kategori
$kecamatanKategoriCounts = [];
$tampilKecamatanKategori = mysqli_query($mysqli, 
    "SELECT nama_kecamatan, kategori, COUNT(*) as jumlah 
     FROM lokasi 
     GROUP BY nama_kecamatan, kategori");
while ($row = mysqli_fetch_assoc($tampilKecamatanKategori)) {
    $kecamatanKategoriCounts[$row['nama_kecamatan']][$row['kategori']] = $row['jumlah'];
}

// Query untuk tabel data lokasi
$lokasiData = [];
$tampilLokasi = mysqli_query($mysqli, "SELECT id, kategori, nama_kecamatan FROM lokasi ORDER BY nama_kecamatan, kategori");
while ($row = mysqli_fetch_assoc($tampilLokasi)) {
    $lokasiData[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <!-- Meta tags untuk pengaturan dasar halaman web -->
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Json</title>

   <!-- Menyertakan CSS Bootstrap -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
         integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

   <!-- Menyertakan Font Awesome untuk ikon -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- CSS kustom -->
   <link rel="stylesheet" href="css/style.css">

   <!-- CSS Leaflet untuk peta -->
   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

   <!-- CDN Chart.js -->
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
   .leaflet-popup-content {
      margin: 13px 24px 13px 20px;
      line-height: 1.3;
      font-size: 13px;
      font-size: 1.5rem;
      min-height: 1px;
   }
   
   .chart-container {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
      margin-bottom: 20px;
   }
   
   .chart-box {
      background: white;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      padding: 15px;
      width: 49%;
      min-width: 300px;
   }
   
   .chart-title {
      text-align: center;
      margin-bottom: 15px;
      font-weight: bold;
   }
   
   .table-container {
      background: white;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      padding: 15px;
      margin: 20px auto;
      
   }
   
   .dataTables_wrapper {
      margin-top: 20px;
   }
   
   .table-responsive {
      overflow-x: auto;
   }
   .leaflet-top, .leaflet-bottom {
    position: absolute;
    z-index: 900;
    pointer-events: none;
   }
</style>
<body>

   <!-- Menyertakan header user -->
   <?php include 'components/user_header.php'; ?>

   <!-- Container untuk menampilkan peta -->
   <div id="mapid" style="height: 75vh; width: 100%;"></div>

   <!-- Container untuk grafik -->
   <div class="chart-container">
      <div class="chart-box">
         <h3 class="chart-title">Distribusi Berdasarkan Kategori</h3>
         <canvas id="kategoriChart"></canvas>
      </div>
      <div class="chart-box">
         <h3 class="chart-title">Distribusi Berdasarkan Kecamatan</h3>
         <canvas id="kecamatanChart"></canvas>
      </div>
   </div>

   <!-- Container untuk tabel distribusi sekolah per kecamatan -->
   <div class="table-container">
      <h3 class="chart-title text-center">Distribusi Sekolah per Kecamatan</h3>
      <div class="table-responsive">
         <table class="table table-bordered table-striped" id="kecamatanTable">
            <thead class="thead-dark">
               <tr>
                  <th>Kecamatan</th>
                  <?php foreach ($kategoriCounts as $kategori => $count): ?>
                     <th><?= htmlspecialchars($kategori) ?></th>
                  <?php endforeach; ?>
                  <th>Total</th>
               </tr>
            </thead>
            <tbody>
               <?php foreach ($kecamatanCounts as $kecamatan => $total): ?>
                  <tr>
                     <td><?= htmlspecialchars($kecamatan) ?></td>
                     <?php 
                     $rowTotal = 0;
                     foreach ($kategoriCounts as $kategori => $count): 
                        $jumlah = $kecamatanKategoriCounts[$kecamatan][$kategori] ?? 0;
                        $rowTotal += $jumlah;
                     ?>
                        <td><?= $jumlah ?></td>
                     <?php endforeach; ?>
                     <td><strong><?= $rowTotal ?></strong></td>
                  </tr>
               <?php endforeach; ?>
            </tbody>
            <tfoot>
               <tr class="table-info">
                  <td><strong>Total</strong></td>
                  <?php 
                  $grandTotal = 0;
                  foreach ($kategoriCounts as $kategori => $count): 
                     $colTotal = 0;
                     foreach ($kecamatanCounts as $kecamatan => $total) {
                        $colTotal += $kecamatanKategoriCounts[$kecamatan][$kategori] ?? 0;
                     }
                     $grandTotal += $colTotal;
                  ?>
                     <td><strong><?= $colTotal ?></strong></td>
                  <?php endforeach; ?>
                  <td><strong><?= $grandTotal ?></strong></td>
               </tr>
            </tfoot>
         </table>
      </div>
   </div>

   <!-- JavaScript Leaflet untuk fungsi peta -->
   <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
   
   <!-- DataTables JavaScript -->
   <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
   <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

   <script>
      // Inisialisasi peta dengan view default di Koordinat Klaten
      var mymap = L.map('mapid').setView([-7.705123, 110.601683], 12);

      // Menambahkan tile layer dari OpenStreetMap
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
         attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
         maxZoom: 20
      }).addTo(mymap);

      // Fungsi untuk menampilkan lokasi sekolah 
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

      }

      // Menambahkan warna Persebaran di pojok kiri bawah
      var legend = L.control({position: 'bottomleft'});
      legend.onAdd = function (map) {
         var div = L.DomUtil.create('div', 'info legend');
         div.style.backgroundColor = 'white';
         div.style.padding = '10px';
         div.style.borderRadius = '5px';
         div.style.border = '1px solid #ccc';
         div.innerHTML = '<h4>Persebaran</h4>' +
            '<div style="display: flex; align-items: center; margin-bottom: 5px;"><div style="width: 20px; height: 20px; background-color: #fff; margin-right: 10px; border: 1px solid #000;"></div><h4>0</h4></div>' +
            '<div style="display: flex; align-items: center; margin-bottom: 5px;"><div style="width: 20px; height: 20px; background-color: #caf0f8; margin-right: 10px; border: 1px solid #000;"></div><h4>1</h4></div>' +
            '<div style="display: flex; align-items: center; margin-bottom: 5px;"><div style="width: 20px; height: 20px; background-color: #90e0ef; margin-right: 10px; border: 1px solid #000;"></div><h4>2</h4></div>' +
            '<div style="display: flex; align-items: center; margin-bottom: 5px;"><div style="width: 20px; height: 20px; background-color: #48cae4; margin-right: 10px; border: 1px solid #000;"></div><h4>3</h4></div>' +
            '<div style="display: flex; align-items: center; margin-bottom: 5px;"><div style="width: 20px; height: 20px; background-color: #0077b6; margin-right: 10px; border: 1px solid #000;"></div><h4>4</h4></div>' +
            '<div style="display: flex; align-items: center;"><div style="width: 20px; height: 20px; background-color: #03045e; margin-right: 10px; border: 1px solid #000;"></div><h4>≥5</h4></div>';
         return div;
      };
      legend.addTo(mymap);

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

      // Menambahkan marker untuk semua lokasi dari database
      <?php
      $tampil = mysqli_query($mysqli, "SELECT * FROM lokasi");
      while ($hasil = mysqli_fetch_array($tampil)) {
         $latLng = str_replace(['[', ']', 'LatLng', '(', ')'], '', $hasil['lat_long']);
         $kategori = $hasil['kategori'];
         $iconColor = $kategori === 'SMA' ? 'blue' : ($kategori === 'SMK' ? 'orange' : ($kategori === 'MA' ? 'green' : 'grey'));
      ?>
      L.marker([<?php echo $latLng; ?>], {
         icon: L.divIcon({
            html: `<i class="fa fa-map-marker-alt" style="color: <?php echo $iconColor; ?>; font-size: 30px;"></i>`,
            className: '',
            iconSize: [24, 24],
            iconAnchor: [12, 24]
         })
      }).addTo(mymap).bindPopup(`
         <strong style="font-size: 1.5rem;">Nama Sekolah : </strong> <span style="font-size: 1.5rem;"><?php echo htmlspecialchars($hasil['nama_tempat']); ?></span><br>
         <strong style="font-size: 1.5rem;">Kategori : </strong> <span style="font-size: 1.5rem;"><?php echo htmlspecialchars($hasil['kategori']); ?></span><br>
         <strong style="font-size: 1.5rem;">Alamat : </strong> <span style="font-size: 1.5rem;"><?php echo htmlspecialchars($hasil['keterangan']); ?></span><br>
         <div class="d-flex justify-content-between">
            <a href="detail.php?id=<?php echo urlencode($hasil['id']); ?>" class="btn btn-sm btn-primary mt-2 text-white">Detail</a>
            <a href="<?php echo htmlspecialchars($hasil['link_lokasi']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary mt-2 text-white">Lokasi</a>
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
         fetch('json/klaten.geojson')
            .then(response => response.json())
            .then(geojson => {
               L.geoJSON(geojson, {
                  style: function(feature) {
                     return {
                        color: "#000000",
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
      
      // Inisialisasi DataTables
      $(document).ready(function() {
         $('#kecamatanTable').DataTable({
            paging: false,
            searching: true,
            ordering: true,
            info: false
         });
      });
   </script>

  <!-- PIE CHART Script -->
<script>
// Fungsi untuk mengambil data dari tabel
function getDataFromTable() {
    const table = document.getElementById('kecamatanTable');
    const rows = table.querySelectorAll('tbody tr');
    const headerCells = table.querySelectorAll('thead th');
    
    // Ambil label kategori (header kolom)
    const kategoriLabels = [];
    for (let i = 1; i < headerCells.length - 1; i++) {
        kategoriLabels.push(headerCells[i].textContent.trim());
    }
    
    // Hitung total per kategori dari tabel
    const kategoriTotals = {};
    kategoriLabels.forEach(label => {
        kategoriTotals[label] = 0;
    });
    
    // Hitung total per kecamatan dari tabel
    const kecamatanTotals = {};
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const kecamatan = cells[0].textContent.trim();
        let totalKecamatan = 0;
        
        for (let i = 1; i < cells.length - 1; i++) {
            const value = parseInt(cells[i].textContent.trim()) || 0;
            const kategori = kategoriLabels[i-1];
            kategoriTotals[kategori] += value;
            totalKecamatan += value;
        }
        
        kecamatanTotals[kecamatan] = totalKecamatan;
    });
    
    return {
        kategori: {
            labels: kategoriLabels,
            data: kategoriLabels.map(label => kategoriTotals[label])
        },
        kecamatan: {
            labels: Object.keys(kecamatanTotals),
            data: Object.values(kecamatanTotals)
        }
    };
}

// Ambil data dari tabel
const chartData = getDataFromTable();

// Grafik Kategori
const kategoriColors = chartData.kategori.labels.map((_, i) => {
    const colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1'];
    return colors[i % colors.length];
});

const kategoriCtx = document.getElementById('kategoriChart').getContext('2d');
new Chart(kategoriCtx, {
    type: 'pie',
    data: {
        labels: chartData.kategori.labels,
        datasets: [{
            data: chartData.kategori.data,
            backgroundColor: kategoriColors
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1).replace('.', ',');
                        return ` jumlah : ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// Grafik Kecamatan
const kecamatanColors = chartData.kecamatan.labels.map((_, i) => {
    const hue = (i * 137.508) % 360;
    return `hsl(${hue}, 70%, 60%)`;
});

const kecamatanCtx = document.getElementById('kecamatanChart').getContext('2d');
new Chart(kecamatanCtx, {
    type: 'pie',
    data: {
        labels: chartData.kecamatan.labels,
        datasets: [{
            data: chartData.kecamatan.data,
            backgroundColor: kecamatanColors
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: {
                        size: 10
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1).replace('.', ',');
                        return ` jumlah : ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});
</script>

   <!-- Menyertakan script JavaScript kustom -->
   <script src="js/script.js"></script>

</body>
</html>
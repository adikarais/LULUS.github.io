// Mendapatkan elemen body
let body = document.body;

// Mendapatkan elemen profile di dalam header
let profile = document.querySelector('.header .flex .profile');

// Fungsi untuk toggle (menampilkan/menyembunyikan) elemen profile ketika tombol user diklik
document.querySelector('#user-btn').onclick = () => {
  profile.classList.toggle('active');
  searchForm.classList.remove('active'); // Menyembunyikan form pencarian jika aktif
};

// Mendapatkan elemen search form di dalam header
let searchForm = document.querySelector('.header .flex .search-form');

// Fungsi untuk toggle (menampilkan/menyembunyikan) elemen search form ketika tombol search diklik
document.querySelector('#search-btn').onclick = () => {
  searchForm.classList.toggle('active');
  profile.classList.remove('active'); // Menyembunyikan profile jika aktif
};

// Mendapatkan elemen sidebar
let sideBar = document.querySelector('.side-bar');

// Fungsi untuk toggle (menampilkan/menyembunyikan) sidebar dan menambahkan class active ke body ketika tombol menu diklik
document.querySelector('#menu-btn').onclick = () => {
  sideBar.classList.toggle('active');
  body.classList.toggle('active');
};

// Fungsi untuk menutup sidebar ketika tombol close pada sidebar diklik
document.querySelector('.side-bar .close-side-bar').onclick = () => {
  sideBar.classList.remove('active');
  body.classList.remove('active');
};

// Membatasi panjang input angka sesuai dengan atribut maxLength
document.querySelectorAll('input[type="number"]').forEach((InputNumber) => {
  InputNumber.oninput = () => {
    if (InputNumber.value.length > InputNumber.maxLength) {
      InputNumber.value = InputNumber.value.slice(0, InputNumber.maxLength);
    }
  };
});

// Fungsi yang dijalankan ketika halaman di-scroll
window.onscroll = () => {
  profile.classList.remove('active'); // Menyembunyikan profile
  searchForm.classList.remove('active'); // Menyembunyikan form pencarian

  // Menutup sidebar jika lebar layar kurang dari 1200px
  if (window.innerWidth < 1200) {
    sideBar.classList.remove('active');
    body.classList.remove('active');
  }
};

// Mendapatkan elemen tombol toggle untuk mode gelap
let toggleBtn = document.querySelector('#toggle-btn');

// Mendapatkan status mode gelap dari localStorage
let darkMode = localStorage.getItem('dark-mode');

// Fungsi untuk mengaktifkan mode gelap
const enabelDarkMode = () => {
  toggleBtn.classList.replace('fa-sun', 'fa-moon'); // Mengubah ikon tombol
  body.classList.add('dark'); // Menambahkan class dark ke body
  localStorage.setItem('dark-mode', 'enabled'); // Menyimpan status mode gelap di localStorage
};

// Fungsi untuk menonaktifkan mode gelap
const disableDarkMode = () => {
  toggleBtn.classList.replace('fa-moon', 'fa-sun'); // Mengubah ikon tombol
  body.classList.remove('dark'); // Menghapus class dark dari body
  localStorage.setItem('dark-mode', 'disabled'); // Menyimpan status mode terang di localStorage
};

// Mengecek status mode gelap saat halaman dimuat
if (darkMode === 'enabled') {
  enabelDarkMode(); // Aktifkan mode gelap jika statusnya "enabled"
}

// Fungsi untuk toggle mode gelap ketika tombol toggle diklik
toggleBtn.onclick = (e) => {
  let darkMode = localStorage.getItem('dark-mode');
  if (darkMode === 'disabled') {
    enabelDarkMode(); // Aktifkan mode gelap
  } else {
    disableDarkMode(); // Nonaktifkan mode gelap
  }
};

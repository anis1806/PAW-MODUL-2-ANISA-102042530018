<?php

// Konfigurasi koneksi database
$conn = mysqli_connect(
    '127.0.0.1',
    'root',
    'Nisabunda18@',
    'paw_modul2_perabot'
);

// Memeriksa koneksi database
if (!$conn) {
    exit(
        'Koneksi database gagal. 
        Periksa layanan MySQL dan konfigurasi koneksi.php.'
    );
}

// Mengatur karakter database
mysqli_set_charset($conn, 'utf8mb4');

?>

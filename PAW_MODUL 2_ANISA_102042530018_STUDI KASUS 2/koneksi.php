<?php

$conn = mysqli_connect(
    '127.0.0.1',
    'root',
    'Nisabunda18@',
    'paw_modul2_showroom'
);

if (!$conn) {
    die(
        'Koneksi database gagal: ' .
        mysqli_connect_error()
    );
}

mysqli_set_charset($conn, 'utf8mb4');

?>

PROJECT PRAKTIKUM PAW MODUL 2 - SHOWROOM EAD

Persiapan:
1. Letakkan folder project di C:/laragon/www/ (Laragon) atau htdocs (XAMPP).
2. Nyalakan Apache dan MySQL.
3. Buka phpMyAdmin, pilih Import, lalu import database.sql.
4. Jika nama database berbeda, sesuaikan di koneksi.php. Database bawaan project: paw_modul2_showroom.
5. Buka http://localhost/PAW_MODUL2_Showroom/index.php (sesuaikan nama folder bila diubah).

Data mobil uji untuk latihan:
Kode: MBL-DEMO
Nama: Toyota Rush
Brand: Toyota
Warna awal: Hitam
Tipe: SUV
Harga awal: 295000000

Urutan latihan halaman 58:
1. Tampilkan 3 mobil awal.
2. Tambahkan mobil uji di atas.
3. Cari Rush lalu buka detail.
4. Edit warna menjadi Putih dan harga menjadi 300000000.
5. Uji kolom kosong, harga negatif, dan kode duplikat.
6. Di halaman hapus, klik Batal dan pastikan data masih ada.
7. Konfirmasi penghapusan mobil uji; jumlah kembali 3.
8. Uji detail.php?id=abc dan detail.php?id=999.
9. Tutup dan buka lagi browser; data database harus tetap tersimpan.

Catatan:
Kode disusun berdasarkan bagian Praktik Showroom dan Latihan Praktikum pada Modul 2.
File database.sql sebaiknya di-import satu kali saja. Jika tabel sudah ada, jangan import ulang.

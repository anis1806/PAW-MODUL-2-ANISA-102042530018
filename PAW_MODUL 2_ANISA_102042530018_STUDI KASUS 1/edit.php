
<?php
require 'koneksi.php';

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$id = (int)($_GET['id'] ?? 0);

$stmt = mysqli_prepare(
    $conn,
    'SELECT * FROM perabot WHERE id = ?'
);

mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$perabot = mysqli_fetch_assoc($hasil);

if (!$perabot) {
    http_response_code(404);

    exit(
        'Perabot tidak ditemukan. ' .
        '<a href="index.php">Kembali ke katalog</a>.'
    );
}

$kode = $perabot['kode_perabot'];
$nama = $perabot['nama_perabot'];
$brand = $perabot['merek_perabot'];
$kategori = $perabot['kategori_perabot'];
$jenis = $perabot['bahan_perabot'];
$harga = $perabot['harga_perabot'];
$stok = $perabot['stok_perabot'];
$deskripsi = $perabot['deskripsi_perabot'];

$pesan = '';

if (isset($_POST['simpan'])) {

    $kode = trim($_POST['kode_perabot'] ?? '');
    $nama = trim($_POST['nama_perabot'] ?? '');
    $brand = trim($_POST['merek_perabot'] ?? '');
    $kategori = trim($_POST['kategori_perabot'] ?? '');
    $jenis = trim($_POST['bahan_perabot'] ?? '');
    $harga = trim($_POST['harga_perabot'] ?? '');
    $stok = trim($_POST['stok_perabot'] ?? '');
    $deskripsi = trim($_POST['deskripsi_perabot'] ?? '');

    if (
        $kode === '' ||
        $nama === '' ||
        $brand === '' ||
        $kategori === '' ||
        $jenis === '' ||
        $deskripsi === ''
    ) {
        $pesan = 'Semua kolom wajib diisi.';

    } elseif (
        !is_numeric($harga) ||
        $harga <= 0 ||
        $harga > 9999999999
    ) {
        $pesan = 'Harga harus berupa angka positif tanpa Rp atau titik.';

    } elseif (
        filter_var($stok, FILTER_VALIDATE_INT) === false ||
        $stok < 0
    ) {
        $pesan = 'Stok harus berupa bilangan bulat nol atau lebih.';

    } else {

        $stmt = mysqli_prepare(
            $conn,
            'SELECT id FROM perabot
             WHERE kode_perabot = ? AND id <> ?'
        );

        mysqli_stmt_bind_param($stmt, 'si', $kode, $id);
        mysqli_stmt_execute($stmt);

        $cek = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($cek) > 0) {

            $pesan = 'Kode perabot sudah digunakan perabot lain.';

        } else {

            $stmt = mysqli_prepare(
                $conn,
                'UPDATE perabot SET
                    kode_perabot = ?,
                    nama_perabot = ?,
                    merek_perabot = ?,
                    kategori_perabot = ?,
                    bahan_perabot = ?,
                    harga_perabot = ?,
                    stok_perabot = ?,
                    deskripsi_perabot = ?
                 WHERE id = ?'
            );

            mysqli_stmt_bind_param(
                $stmt,
                'sssssdisi',
                $kode,
                $nama,
                $brand,
                $kategori,
                $jenis,
                $harga,
                $stok,
                $deskripsi,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                header('Location: detail.php?id=' . $id . '&pesan=1');
                exit;
            }

            $pesan = 'Perabot gagal diperbarui. Periksa kembali data.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Edit Produk — RumahNinis</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header class="topbar">

        <a class="brandmark" href="index.php">
            rumah<span>ninis.</span>
        </a>

        <nav class="navlinks">
            <a href="index.php">Katalog</a>
            <a href="tambah.php">Tambah Perabot</a>
        </nav>

    </header>

    <main class="wrap">

        <p class="eyebrow">
            Perabot management
        </p>

        <h1 class="page-title">
            Edit perabot
        </h1>

        <p class="meta">
            Perbarui informasi perabot di bawah ini.
        </p>

        <section class="panel">

            <?php if ($pesan !== ''): ?>

                <div class="error">
                    <?= h($pesan) ?>
                </div>

            <?php endif; ?>

            <form method="post">

                <div class="form-grid">

                    <div>
                        <label for="kode">Kode perabot</label>

                        <input
                            id="kode"
                            name="kode_perabot"
                            maxlength="20"
                            required
                            value="<?= h($kode) ?>"
                        >
                    </div>

                    <div>
                        <label for="nama">Nama perabot</label>

                        <input
                            id="nama"
                            name="nama_perabot"
                            maxlength="100"
                            required
                            value="<?= h($nama) ?>"
                        >
                    </div>

                    <div>
                        <label for="brand">Merek</label>

                        <input
                            id="brand"
                            name="merek_perabot"
                            maxlength="50"
                            required
                            value="<?= h($brand) ?>"
                        >
                    </div>

                    <div>
                        <label for="kategori">Tipe perabot</label>

                        <select
                            id="kategori"
                            name="kategori_perabot"
                            required
                        >
                            <option value="">Pilih tipe perabot</option>

                            <?php
                            foreach (
                                [
                                    'Ruang Tamu',
                                    'Kamar Tidur',
                                    'Dapur',
                                    'Penyimpanan',
                                    'Dekorasi',
                                    'Lainnya'
                                ] as $opt
                            ):
                            ?>
                                <option
                                    value="<?= h($opt) ?>"
                                    <?= $kategori === $opt ? 'selected' : '' ?>
                                >
                                    <?= h($opt) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div>
                        <label for="jenis">Bahan utama</label>

                        <select
                            id="jenis"
                            name="bahan_perabot"
                            required
                        >
                            <option value="">Pilih bahan utama</option>

                            <?php
                            foreach (
                                [
                                    'Kayu solid',
                                    'Kayu olahan',
                                    'Plastik',
                                    'Besi',
                                    'Kain',
                                    'Rotan',
                                    'Kaca',
                                    'Lainnya'
                                ] as $opt
                            ):
                            ?>
                                <option
                                    value="<?= h($opt) ?>"
                                    <?= $jenis === $opt ? 'selected' : '' ?>
                                >
                                    <?= h($opt) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div>
                        <label for="harga">Harga (angka saja)</label>

                        <input
                            id="harga"
                            name="harga_perabot"
                            type="number"
                            min="1"
                            step="1"
                            required
                            value="<?= h($harga) ?>"
                        >
                    </div>

                    <div>
                        <label for="stok">Stok</label>

                        <input
                            id="stok"
                            name="stok_perabot"
                            type="number"
                            min="0"
                            step="1"
                            required
                            value="<?= h($stok) ?>"
                        >
                    </div>

                    <div class="full">
                        <label for="deskripsi">Deskripsi perabot</label>

                        <textarea
                            id="deskripsi"
                            name="deskripsi_perabot"
                            required
                        ><?= h($deskripsi) ?></textarea>
                    </div>

                </div>

                <div class="actions">

                    <button
                        class="btn"
                        name="simpan"
                        type="submit"
                    >
                        Simpan perubahan
                    </button>

                    <a
                        class="btn secondary"
                        href="detail.php?id=<?= $id ?>"
                    >
                        Batal
                    </a>

                </div>

            </form>

        </section>

    </main>

    <footer class="footer">
        RumahNinis · Praktikum PAW Modul 2
    </footer>

</body>

</html>

```php
<?php
require 'koneksi.php';

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function rupiah($value) {
    return 'Rp ' . number_format((float)$value, 0, ',', '.');
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
        'Perabot tidak ditemukan. 
        <a href="index.php">Kembali ke katalog</a>.'
    );
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Detail Perabot — RumahNinis</title>

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
            Perabot details
        </p>

        <h1 class="page-title">
            Detail perabot
        </h1>

        <section class="panel">

            <div class="detail-grid">

                <div class="detail-art" aria-hidden="true">
                    🪑
                </div>

                <div>

                    <span class="pill">
                        <?= h($perabot['kategori_perabot']) ?>
                    </span>

                    <h2
                        class="page-title"
                        style="font-size: 30px; margin-top: 12px;"
                    >
                        <?= h($perabot['nama_perabot']) ?>
                    </h2>

                    <p class="meta">
                        <?= h($perabot['merek_perabot']) ?>
                    </p>

                    <p class="price">
                        <?= rupiah($perabot['harga_perabot']) ?>
                    </p>

                    <dl class="detail-list">

                        <dt>Kode perabot</dt>
                        <dd>
                            <?= h($perabot['kode_perabot']) ?>
                        </dd>

                        <dt>Tipe perabot</dt>
                        <dd>
                            <?= h($perabot['kategori_perabot']) ?>
                        </dd>

                        <dt>Bahan utama</dt>
                        <dd>
                            <?= h($perabot['bahan_perabot']) ?>
                        </dd>

                        <dt>Stok</dt>
                        <dd>
                            <?= (int)$perabot['stok_perabot'] ?> unit
                        </dd>

                        <dt>Deskripsi</dt>
                        <dd>
                            <?= nl2br(h($perabot['deskripsi_perabot'])) ?>
                        </dd>

                    </dl>

                    <div class="actions">

                        <a
                            class="btn"
                            href="edit.php?id=<?= (int)$perabot['id'] ?>"
                        >
                            Edit perabot
                        </a>

                        <a
                            class="btn secondary"
                            href="index.php"
                        >
                            Kembali ke katalog
                        </a>

                        <a
                            class="btn secondary"
                            href="hapus.php?id=<?= (int)$perabot['id'] ?>"
                        >
                            Hapus
                        </a>

                    </div>

                </div>

            </div>

        </section>

    </main>

    <footer class="footer">
        RumahNinis · Praktikum PAW Modul 2
    </footer>

</body>

</html>
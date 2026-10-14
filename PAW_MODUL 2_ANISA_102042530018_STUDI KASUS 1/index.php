
<?php
require 'koneksi.php';

$cari = trim($_GET['cari'] ?? '');

$kata = mysqli_real_escape_string($conn, $cari);

$sql = "
    SELECT *
    FROM perabot
    WHERE nama_perabot LIKE '%$kata%'
        OR merek_perabot LIKE '%$kata%'
        OR kategori_perabot LIKE '%$kata%'
    ORDER BY id DESC
";

$hasil = mysqli_query($conn, $sql);

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function rupiah($value) {
    return 'Rp ' . number_format((float)$value, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>RumahNinis — Katalog Perabot</title>

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

        <section class="hero">

            <p class="eyebrow">
                Bikin rumah lebih nyaman
            </p>

            <h1>
                Lengkapi rumah,<br>
                nyaman setiap hari.
            </h1>

            <p>
                Kelola katalog perabot rumah tangga dalam satu tempat.
                Cari produk, cek stok, dan perbarui informasi dengan mudah.
            </p>

        </section>

        <?php if (isset($_GET['pesan'])): ?>

            <div class="notice">
                Perubahan data perabot berhasil disimpan.
            </div>

        <?php endif; ?>

        <div class="toolbar">

            <div>

                <p class="eyebrow">
                    RumahNinis collection
                </p>

                <h2>
                    Katalog perabot
                </h2>

            </div>

            <form class="search" method="get">

                <input
                    type="search"
                    name="cari"
                    placeholder="Cari nama, merek, kategori..."
                    value="<?= h($cari) ?>"
                >

                <button class="btn" type="submit">
                    Cari
                </button>

                <?php if ($cari !== ''): ?>

                    <a class="btn secondary" href="index.php">
                        Reset
                    </a>

                <?php endif; ?>

            </form>

        </div>

        <p class="meta">
            Jumlah hasil: <?= mysqli_num_rows($hasil) ?> produk
        </p>

        <?php if (mysqli_num_rows($hasil) === 0): ?>

            <div class="empty">

                <h3>
                    Perabot belum ditemukan
                </h3>

                <p>
                    Coba kata kunci lain atau tambahkan perabot baru.
                </p>

                <a class="btn" href="tambah.php">
                    + Tambah Perabot
                </a>

            </div>

        <?php else: ?>

            <div class="cards">

                <?php while ($perabot = mysqli_fetch_assoc($hasil)): ?>

                    <article class="product-card">

                        <div class="product-art" aria-hidden="true">
                            🪑
                        </div>

                        <span class="pill">
                            <?= h($perabot['kategori_perabot']) ?>
                        </span>

                        <h3>
                            <?= h($perabot['nama_perabot']) ?>
                        </h3>

                        <p class="meta">
                            <?= h($perabot['merek_perabot']) ?>
                            ·
                            <?= h($perabot['bahan_perabot']) ?>
                        </p>

                        <p class="price">
                            <?= rupiah($perabot['harga_perabot']) ?>
                        </p>

                        <p class="stock">
                            Stok tersedia:
                            <?= (int)$perabot['stok_perabot'] ?>
                        </p>

                        <div class="card-actions">

                            <a href="detail.php?id=<?= (int)$perabot['id'] ?>">
                                Lihat detail
                            </a>

                            <a href="edit.php?id=<?= (int)$perabot['id'] ?>">
                                Edit
                            </a>

                            <a href="hapus.php?id=<?= (int)$perabot['id'] ?>">
                                Hapus
                            </a>

                        </div>

                    </article>

                <?php endwhile; ?>

            </div>

        <?php endif; ?>

    </main>

    <footer class="footer">
        RumahNinis · Praktikum PAW Modul 2 · PHP & MySQL
    </footer>

</body>

</html>

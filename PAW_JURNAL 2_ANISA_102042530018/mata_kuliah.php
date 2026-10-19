<?php
require 'koneksi.php';

$msg = '';
$action = $_GET['action'] ?? 'list';
$id = intval($_GET['id'] ?? 0);

// PROSES TAMBAH / EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $kode = trim($_POST['kode']);
    $nama = trim($_POST['nama']);
    $dosen = trim($_POST['dosen']);
    $semester = intval($_POST['semester']);

    // Validasi Isian
    if (!preg_match('/^[A-Z0-9\-]{3,20}$/', $kode)) {
        $msg = 'Kode harus 3-20 karakter (Huruf Kapital, Angka, atau Hyphen).';
    } elseif (strlen($nama) < 3 || strlen($nama) > 100) {
        $msg = 'Nama mata kuliah harus 3-100 karakter.';
    } elseif ($semester < 1 || $semester > 14) {
        $msg = 'Semester harus antara 1-14.';
    } else {
        if ($id > 0) { // Update
            $stmt = $conn->prepare("UPDATE mata_kuliah SET kode=?, nama=?, dosen=?, semester=? WHERE id=?");
            $stmt->bind_param("sssii", $kode, $nama, $dosen, $semester, $id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows === 0) $msg = "Tidak ada perubahan data.";
                else header("Location: mata_kuliah.php?msg=Data+berhasil+diubah");
            } else $msg = "Kode mata kuliah sudah digunakan!";
        } else { // Insert
            $stmt = $conn->prepare("INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $kode, $nama, $dosen, $semester);
            if ($stmt->execute()) header("Location: mata_kuliah.php?msg=Data+berhasil+ditambah");
            else $msg = "Kode mata kuliah sudah digunakan.";
        }
    }
}

// PROSES HAPUS (Gunakan POST + CSRF Token)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Token keamanan tidak valid.");
    }
    
    try {
        $stmt = $conn->prepare("DELETE FROM mata_kuliah WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: mata_kuliah.php?msg=Mata+kuliah+berhasil+dihapus");
    } catch (mysqli_sql_exception $e) {
        $msg = "Data belum tersimpan. Mata kuliah masih memiliki materi atau tugas. Hapus data terkait terlebih dahulu.";
    }
}

// PENCARIAN
$search = trim($_GET['search'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Mata Kuliah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <a href="index.php">← Beranda</a> | <a href="mata_kuliah.php?action=add">Tambah Mata Kuliah</a>
    <h1>Pengelolaan Mata Kuliah</h1>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert success"><?= e($_GET['msg']) ?></div>
    <?php endif; ?>
    <?php if ($msg): ?>
        <div class="alert"><?= e($msg) ?></div>
    <?php endif; ?>

    <?php if ($action === 'add' || $action === 'edit'): 
        $data = ['kode'=>'', 'nama'=>'', 'dosen'=>'', 'semester'=>''];
        if ($id > 0) {
            $stmt = $conn->prepare("SELECT * FROM mata_kuliah WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $data = $stmt->get_result()->fetch_assoc();
        }
    ?>
        <div class="panel">
            <h2><?= $id ? 'Edit' : 'Tambah' ?> Mata Kuliah</h2>
            <form method="POST">
                <label>Kode MK:</label><br>
                <input type="text" name="kode" value="<?= e($data['kode']) ?>" required><br><br>
                <label>Nama MK:</label><br>
                <input type="text" name="nama" value="<?= e($data['nama']) ?>" required><br><br>
                <label>Dosen Pengampu:</label><br>
                <input type="text" name="dosen" value="<?= e($data['dosen']) ?>" required><br><br>
                <label>Semester (1-14):</label><br>
                <input type="number" name="semester" value="<?= e($data['semester']) ?>" min="1" max="14" required><br><br>
                <button type="submit" name="simpan">Simpan Data</button>
                <a href="mata_kuliah.php">Batal</a>
            </form>
        </div>

    <?php elseif ($action === 'delete'): ?>
        <div class="panel">
            <h2>Konfirmasi Hapus</h2>
            <p>Apakah Anda yakin ingin menghapus mata kuliah ini?</p>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <button type="submit" name="hapus" style="color:red;">Ya, Hapus Data</button>
                <a href="mata_kuliah.php">Batal</a>
            </form>
        </div>

    <?php else: ?>
        <form method="GET">
            <input type="text" name="search" placeholder="Cari Kode / Nama MK..." value="<?= e($search) ?>">
            <button type="submit">Cari</button>
            <a href="mata_kuliah.php">Reset</a>
        </form>

        <div class="panel">
            <table>
                <thead>
                    <tr><th>Kode</th><th>Nama</th><th>Dosen / Semester</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM mata_kuliah WHERE kode LIKE ? OR nama LIKE ? ORDER BY nama";
                    $param = "%$search%";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $param, $param);
                    $stmt->execute();
                    $res = $stmt->get_result();

                    if ($res->num_rows === 0): ?>
                        <tr><td colspan="4">Data tidak ditemukan.</td></tr>
                    <?php else:
                        while ($row = $res->fetch_assoc()): ?>
                            <tr>
                                <td><?= e($row['kode']) ?></td>
                                <td><?= e($row['nama']) ?></td>
                                <td><?= e($row['dosen']) ?> / Sem <?= e($row['semester']) ?></td>
                                <td>
                                    <a href="mata_kuliah.php?action=edit&id=<?= $row['id'] ?>">Edit</a> |
                                    <a href="mata_kuliah.php?action=delete&id=<?= $row['id'] ?>">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile;
                    endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</body>
</html>
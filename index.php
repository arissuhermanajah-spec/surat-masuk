<?php
// config database
$host = 'localhost';
$db   = 'db_surat';
$user = 'root';
$pass = '';
$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$jenis = $_GET['jenis'] ?? 'masuk';

// simpan / update
if (isset($_POST['simpan'])) {
    if ($_POST['id'] == '') {
        $stmt = $conn->prepare("INSERT INTO surat (jenis, nomor, pengirim, tanggal, perihal) VALUES (?,?,?,?,?)");
        $stmt->execute([$jenis, $_POST['nomor'], $_POST['pengirim'], $_POST['tanggal'], $_POST['perihal']]);
    } else {
        $stmt = $conn->prepare("UPDATE surat SET nomor=?, pengirim=?, tanggal=?, perihal=? WHERE id=?");
        $stmt->execute([$_POST['nomor'], $_POST['pengirim'], $_POST['tanggal'], $_POST['perihal'], $_POST['id']]);
    }
    header("Location: index.php?jenis=$jenis");
}

// hapus
if (isset($_GET['hapus'])) {
    $conn->prepare("DELETE FROM surat WHERE id=?")->execute([$_GET['hapus']]);
    header("Location: index.php?jenis=$jenis");
}

// edit
$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM surat WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit = $stmt->fetch();
}

$data = $conn->query("SELECT * FROM surat WHERE jenis='$jenis' ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Aplikasi Surat</title>
<style>
body{font-family:Segoe UI;background:#f4f6f8;padding:20px}
.container{max-width:1000px;margin:auto}
h1{text-align:center}
.tabs a{padding:10px 20px;background:#e5e7eb;border-radius:8px;text-decoration:none;color:#000;margin-right:10px}
.active{background:#2563eb;color:#fff}
.card{background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,.1)}
form{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px}
input,button{padding:10px;border-radius:8px;border:1px solid #ccc}
button{grid-column:span 2;background:#2563eb;color:#fff;border:none}
table{width:100%;border-collapse:collapse}
th,td{padding:10px;border-bottom:1px solid #eee}
.btn{padding:5px 10px;border-radius:6px;text-decoration:none}
.edit{background:#facc15;color:#000}
.delete{background:#ef4444;color:#fff}
</style>
</head>
<body>
<div class="container">
<h1>Aplikasi Surat</h1>
<div class="tabs">
<a href="?jenis=masuk" class="<?= $jenis=='masuk'?'active':'' ?>">Surat Masuk</a>
<a href="?jenis=keluar" class="<?= $jenis=='keluar'?'active':'' ?>">Surat Keluar</a>
</div>
<div class="card">
<form method="post">
<input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
<input name="nomor" placeholder="Nomor Surat" value="<?= $edit['nomor'] ?? '' ?>" required>
<input name="pengirim" placeholder="Pengirim / Tujuan" value="<?= $edit['pengirim'] ?? '' ?>" required>
<input type="date" name="tanggal" value="<?= $edit['tanggal'] ?? '' ?>" required>
<input name="perihal" placeholder="Perihal" value="<?= $edit['perihal'] ?? '' ?>" required>
<button name="simpan">Simpan</button>
</form>
<table>
<tr><th>No</th><th>Nomor Surat</th><th>Pengirim</th><th>Tanggal</th><th>Perihal</th><th>Aksi</th></tr>
<?php $no=1; foreach($data as $d): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= $d['nomor'] ?></td>
<td><?= $d['pengirim'] ?></td>
<td><?= $d['tanggal'] ?></td>
<td><?= $d['perihal'] ?></td>
<td>
<a class="btn edit" href="?jenis=<?= $jenis ?>&edit=<?= $d['id'] ?>">Edit</a>
<a class="btn delete" href="?jenis=<?= $jenis ?>&hapus=<?= $d['id'] ?>" onclick="return confirm('Hapus surat?')">Hapus</a>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>
</div>
</body>
</html>
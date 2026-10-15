<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $id_user = $_POST['id_user'] ?? '';
    $nama  = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $no_hp = trim($_POST['no_hp']);

    if ($id_user) {
        $stmt = $pdo->prepare('UPDATE users SET nama=?, email=?, no_hp=? WHERE id_user=? AND role="user"');
        $stmt->execute([$nama, $email, $no_hp, $id_user]);
        $msg = 'Data anggota diperbarui.';
    }
}

if (isset($_GET['hapus'])) {
    $stmt = $pdo->prepare('DELETE FROM users WHERE id_user=? AND role="user"');
    $stmt->execute([$_GET['hapus']]);
    header('Location: anggota.php?msg=deleted');
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id_user=? AND role="user"');
    $stmt->execute([$_GET['edit']]);
    $editData = $stmt->fetch();
}

$daftarAnggota = $pdo->query("SELECT * FROM users WHERE role='user' ORDER BY id_user DESC")->fetchAll();

$pageTitle = 'Data Anggota';
require_once __DIR__ . '/../includes/header_admin.php';
?>

<?php if ($msg || isset($_GET['msg'])): ?>
  <div class="alert alert-success"><?= $msg ?: 'Anggota berhasil dihapus.' ?></div>
<?php endif; ?>

<?php if ($editData): ?>
<div class="panel">
  <h3 style="color:var(--gold); margin-bottom:15px;">✏️ Edit Data Anggota</h3>
  <form method="POST">
    <input type="hidden" name="id_user" value="<?= $editData['id_user'] ?>">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
      <div class="form-group"><label>Nama</label><input type="text" name="nama" required value="<?= e($editData['nama']) ?>"></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= e($editData['email'] ?? '') ?>"></div>
      <div class="form-group"><label>No. HP</label><input type="text" name="no_hp" value="<?= e($editData['no_hp'] ?? '') ?>"></div>
    </div>
    <button type="submit" name="simpan" class="btn btn-gold">💾 Simpan</button>
    <a href="anggota.php" class="btn btn-outline">Batal</a>
  </form>
</div>
<?php endif; ?>

<div class="panel">
  <h3 style="color:var(--gold); margin-bottom:10px;">👥 Data Anggota / Pengunjung Perpustakaan</h3>
  <table>
    <tr><th>Username</th><th>Nama</th><th>Email</th><th>No HP</th><th>Terdaftar</th><th>Aksi</th></tr>
    <?php foreach ($daftarAnggota as $a): ?>
      <tr>
        <td><?= e($a['username']) ?></td>
        <td><?= e($a['nama']) ?></td>
        <td><?= e($a['email'] ?? '-') ?></td>
        <td><?= e($a['no_hp'] ?? '-') ?></td>
        <td><?= e(date('d M Y', strtotime($a['created_at']))) ?></td>
        <td>
          <a href="?edit=<?= $a['id_user'] ?>" class="btn btn-outline btn-sm">Edit</a>
          <a href="?hapus=<?= $a['id_user'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus anggota ini?')">Hapus</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
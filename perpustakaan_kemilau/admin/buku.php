<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

$uploadDir = __DIR__ . '/../assets/uploads/';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $id_buku   = $_POST['id_buku'] ?? '';
    $judul     = trim($_POST['judul']);
    $pengarang = trim($_POST['pengarang']);
    $kategori  = trim($_POST['kategori']);
    $deskripsi = trim($_POST['deskripsi']);
    $stok      = (int) $_POST['stok'];
    $coverName = null;

    if (!empty($_FILES['cover']['name'])) {
        $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $allowed)) {
            $coverName = 'buku_' . time() . '_' . rand(100,999) . '.' . $ext;
            move_uploaded_file($_FILES['cover']['tmp_name'], $uploadDir . $coverName);
        }
    }

    if ($id_buku) {
        if ($coverName) {
            $stmt = $pdo->prepare('UPDATE buku SET judul=?, pengarang=?, kategori=?, deskripsi=?, stok=?, cover=? WHERE id_buku=?');
            $stmt->execute([$judul, $pengarang, $kategori, $deskripsi, $stok, $coverName, $id_buku]);
        } else {
            $stmt = $pdo->prepare('UPDATE buku SET judul=?, pengarang=?, kategori=?, deskripsi=?, stok=? WHERE id_buku=?');
            $stmt->execute([$judul, $pengarang, $kategori, $deskripsi, $stok, $id_buku]);
        }
        $msg = 'Data buku berhasil diperbarui.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO buku (judul, pengarang, kategori, deskripsi, stok, cover) VALUES (?,?,?,?,?,?)');
        $stmt->execute([$judul, $pengarang, $kategori, $deskripsi, $stok, $coverName]);
        $msg = 'Buku baru berhasil ditambahkan.';
    }
}

if (isset($_GET['hapus'])) {
    $stmt = $pdo->prepare('DELETE FROM buku WHERE id_buku=?');
    $stmt->execute([$_GET['hapus']]);
    header('Location: buku.php?msg=deleted');
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM buku WHERE id_buku=?');
    $stmt->execute([$_GET['edit']]);
    $editData = $stmt->fetch();
}

$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM buku WHERE judul LIKE ? OR pengarang LIKE ? ORDER BY id_buku DESC");
    $like = "%$search%";
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query('SELECT * FROM buku ORDER BY id_buku DESC');
}
$daftarBuku = $stmt->fetchAll();

$pageTitle = 'Data Buku';
require_once __DIR__ . '/../includes/header_admin.php';
?>

<?php if ($msg || isset($_GET['msg'])): ?>
  <div class="alert alert-success"><?= $msg ?: 'Buku berhasil dihapus.' ?></div>
<?php endif; ?>

<div class="panel">
  <h3 style="color:var(--gold); margin-bottom:15px;"><?= $editData ? '✏️ Edit Buku' : '➕ Tambah Buku Baru' ?></h3>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id_buku" value="<?= e((string)($editData['id_buku'] ?? '')) ?>">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
      <div class="form-group"><label>Judul Buku</label><input type="text" name="judul" required value="<?= e($editData['judul'] ?? '') ?>"></div>
      <div class="form-group"><label>Pengarang</label><input type="text" name="pengarang" required value="<?= e($editData['pengarang'] ?? '') ?>"></div>
      <div class="form-group"><label>Kategori</label><input type="text" name="kategori" value="<?= e($editData['kategori'] ?? '') ?>"></div>
      <div class="form-group"><label>Stok</label><input type="number" name="stok" min="0" required value="<?= e((string)($editData['stok'] ?? 0)) ?>"></div>
    </div>
    <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="2"><?= e($editData['deskripsi'] ?? '') ?></textarea></div>
    <div class="form-group">
      <label>Cover Buku (upload gambar - otomatis tersimpan di project)</label>
      <input type="file" name="cover" accept="image/*">
      <?php if (!empty($editData['cover'])): ?>
        <small style="opacity:.7;">Cover saat ini: <?= e($editData['cover']) ?> (biarkan kosong jika tidak ingin ganti)</small>
      <?php endif; ?>
    </div>
    <button type="submit" name="simpan" class="btn btn-gold">💾 Simpan</button>
    <?php if ($editData): ?><a href="buku.php" class="btn btn-outline">Batal</a><?php endif; ?>
  </form>
</div>

<div class="panel">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
    <h3 style="color:var(--gold);">📖 Daftar Buku</h3>
    <form method="GET" style="display:flex; gap:8px;">
      <input type="text" name="q" placeholder="Cari judul/pengarang..." value="<?= e($search) ?>" style="width:220px;">
      <button class="btn btn-outline btn-sm">Cari</button>
    </form>
  </div>
  <table>
    <tr><th>Cover</th><th>Judul</th><th>Pengarang</th><th>Kategori</th><th>Stok</th><th>Aksi</th></tr>
    <?php foreach ($daftarBuku as $b): ?>
      <tr>
        <td>
          <?php if ($b['cover']): ?>
            <img src="../assets/uploads/<?= e($b['cover']) ?>" style="width:40px;height:55px;object-fit:cover;border-radius:3px;">
          <?php else: ?>
            <span style="font-size:20px;">📕</span>
          <?php endif; ?>
        </td>
        <td><?= e($b['judul']) ?></td>
        <td><?= e($b['pengarang']) ?></td>
        <td><?= e($b['kategori']) ?></td>
        <td><?= $b['stok'] ?></td>
        <td>
          <a href="?edit=<?= $b['id_buku'] ?>" class="btn btn-outline btn-sm">Edit</a>
          <a href="?hapus=<?= $b['id_buku'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus buku ini?')">Hapus</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
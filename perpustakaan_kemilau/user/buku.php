<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole('user');

$id_user = $_SESSION['id_user'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pinjam'])) {
    $id_buku = $_POST['id_buku'];
    $tanggal_kembali_rencana = $_POST['tanggal_kembali_rencana'];
    $tanggal_pinjam = date('Y-m-d');

    $cek = $pdo->prepare('SELECT stok FROM buku WHERE id_buku=?');
    $cek->execute([$id_buku]);
    $stok = $cek->fetchColumn();

    if ($stok > 0) {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO peminjaman (id_user, id_buku, tanggal_pinjam, tanggal_kembali_rencana, status) VALUES (?,?,?,?,"dipinjam")');
        $stmt->execute([$id_user, $id_buku, $tanggal_pinjam, $tanggal_kembali_rencana]);
        $pdo->prepare('UPDATE buku SET stok = stok - 1 WHERE id_buku=?')->execute([$id_buku]);
        $pdo->commit();
        $msg = 'Buku berhasil dipinjam! Cek di menu Riwayat Pinjam.';
    } else {
        $msg = 'Maaf, stok buku ini sedang habis.';
    }
}

if (isset($_GET['fav'])) {
    $stmt = $pdo->prepare('INSERT IGNORE INTO favorit (id_user, id_buku) VALUES (?,?)');
    $stmt->execute([$id_user, $_GET['fav']]);
    header('Location: buku.php?msg=fav');
    exit;
}

$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare('SELECT * FROM buku WHERE judul LIKE ? OR pengarang LIKE ? OR kategori LIKE ? ORDER BY judul');
    $like = "%$search%";
    $stmt->execute([$like, $like, $like]);
} else {
    $stmt = $pdo->query('SELECT * FROM buku ORDER BY judul');
}
$daftarBuku = $stmt->fetchAll();

$pageTitle = 'Daftar Buku';
require_once __DIR__ . '/../includes/header_user.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if (isset($_GET['msg']) && $_GET['msg']==='fav'): ?><div class="alert alert-success">Buku ditambahkan ke Favorit ⭐</div><?php endif; ?>

<div class="panel">
  <form method="GET" style="display:flex; gap:8px; margin-bottom:5px;">
    <input type="text" name="q" placeholder="Cari judul, pengarang, atau kategori..." value="<?= e($search) ?>">
    <button class="btn btn-outline">Cari</button>
  </form>
</div>

<div class="book-grid">
  <?php foreach ($daftarBuku as $b): ?>
    <div class="book-card">
      <?php if ($b['cover']): ?>
        <img class="book-cover" src="../assets/uploads/<?= e($b['cover']) ?>" alt="<?= e($b['judul']) ?>">
      <?php else: ?>
        <div class="book-cover-placeholder">📘</div>
      <?php endif; ?>
      <div class="book-info">
        <div class="judul"><?= e($b['judul']) ?></div>
        <div class="pengarang"><?= e($b['pengarang']) ?></div>
        <div class="stok">
          <?php if ($b['stok'] > 0): ?><span class="badge badge-success">Tersedia (<?= $b['stok'] ?>)</span>
          <?php else: ?><span class="badge badge-danger">Stok Habis</span><?php endif; ?>
        </div>
        <div class="book-actions">
          <?php if ($b['stok'] > 0): ?>
            <button class="btn btn-gold btn-sm" onclick="bukaModal(<?= $b['id_buku'] ?>, '<?= e(addslashes($b['judul'])) ?>')">Pinjam</button>
          <?php endif; ?>
          <a href="?fav=<?= $b['id_buku'] ?>" class="btn btn-outline btn-sm">⭐</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="modal-overlay" id="modalPinjam">
  <div class="modal-box">
    <span class="modal-close" onclick="tutupModal()">✕</span>
    <h3>Pinjam Buku</h3>
    <p id="judulModal" style="margin-bottom:12px; opacity:.85;"></p>
    <form method="POST">
      <input type="hidden" name="id_buku" id="idBukuModal">
      <div class="form-group">
        <label>Tanggal Pinjam</label>
        <input type="text" value="<?= date('d-m-Y') ?> (hari ini)" disabled>
      </div>
      <div class="form-group">
        <label>Tanggal Rencana Kembali</label>
        <input type="date" name="tanggal_kembali_rencana" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
      </div>
      <button type="submit" name="pinjam" class="btn btn-gold" style="width:100%;">Konfirmasi Pinjam</button>
    </form>
  </div>
</div>

<script>
function bukaModal(id, judul){
  document.getElementById('idBukuModal').value = id;
  document.getElementById('judulModal').textContent = 'Buku: ' + judul;
  document.getElementById('modalPinjam').classList.add('show');
}
function tutupModal(){
  document.getElementById('modalPinjam').classList.remove('show');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
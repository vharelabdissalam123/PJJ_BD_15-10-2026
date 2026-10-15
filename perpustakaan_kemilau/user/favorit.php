<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole('user');

$id_user = $_SESSION['id_user'];

if (isset($_GET['hapus'])) {
    $stmt = $pdo->prepare('DELETE FROM favorit WHERE id_user=? AND id_buku=?');
    $stmt->execute([$id_user, $_GET['hapus']]);
    header('Location: favorit.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT b.* FROM favorit f
    JOIN buku b ON b.id_buku = f.id_buku
    WHERE f.id_user = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$id_user]);
$daftarFavorit = $stmt->fetchAll();

$pageTitle = 'Favorit Saya';
require_once __DIR__ . '/../includes/header_user.php';
?>

<div class="panel">
  <h3 style="color:var(--gold); margin-bottom:12px;">⭐ Daftar Buku Favorit / Ingin Dipinjam</h3>
  <?php if (!$daftarFavorit): ?>
    <p style="opacity:.8;">Belum ada buku favorit. Tambahkan dari halaman <a href="buku.php" style="color:var(--gold);">Daftar Buku</a>.</p>
  <?php endif; ?>
  <div class="book-grid">
    <?php foreach ($daftarFavorit as $b): ?>
      <div class="book-card">
        <?php if ($b['cover']): ?>
          <img class="book-cover" src="../assets/uploads/<?= e($b['cover']) ?>" alt="<?= e($b['judul']) ?>">
        <?php else: ?>
          <div class="book-cover-placeholder">💛</div>
        <?php endif; ?>
        <div class="book-info">
          <div class="judul"><?= e($b['judul']) ?></div>
          <div class="pengarang"><?= e($b['pengarang']) ?></div>
          <div class="book-actions">
            <a href="buku.php?q=<?= urlencode($b['judul']) ?>" class="btn btn-gold btn-sm">Pinjam</a>
            <a href="?hapus=<?= $b['id_buku'] ?>" class="btn btn-danger btn-sm">Hapus</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
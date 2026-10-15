<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole('user');

$rekomendasi = $pdo->query("
    SELECT b.*, COUNT(p.id_peminjaman) total_pinjam
    FROM buku b
    LEFT JOIN peminjaman p ON p.id_buku = b.id_buku
    WHERE b.stok > 0
    GROUP BY b.id_buku
    ORDER BY total_pinjam DESC, b.created_at DESC
    LIMIT 8
")->fetchAll();

$pageTitle = 'Rekomendasi Buku';
require_once __DIR__ . '/../includes/header_user.php';
?>

<div class="panel">
  <h3 style="color:var(--gold); margin-bottom:12px;">✨ Rekomendasi Buku Untukmu</h3>
  <p style="font-size:13px; opacity:.8; margin-bottom:15px;">Dipilih berdasarkan buku yang paling banyak diminati anggota lain.</p>
  <div class="book-grid">
    <?php foreach ($rekomendasi as $b): ?>
      <div class="book-card">
        <?php if ($b['cover']): ?>
          <img class="book-cover" src="../assets/uploads/<?= e($b['cover']) ?>" alt="<?= e($b['judul']) ?>">
        <?php else: ?>
          <div class="book-cover-placeholder">📙</div>
        <?php endif; ?>
        <div class="book-info">
          <div class="judul"><?= e($b['judul']) ?></div>
          <div class="pengarang"><?= e($b['pengarang']) ?></div>
          <div class="stok"><span class="badge badge-success">Tersedia (<?= $b['stok'] ?>)</span></div>
          <div class="book-actions">
            <a href="buku.php?q=<?= urlencode($b['judul']) ?>" class="btn btn-gold btn-sm">Lihat & Pinjam</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
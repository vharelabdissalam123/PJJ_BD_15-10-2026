<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole('user');

$id_user = $_SESSION['id_user'];

$sedangDipinjam = $pdo->prepare("SELECT COUNT(*) FROM peminjaman WHERE id_user=? AND status='dipinjam'");
$sedangDipinjam->execute([$id_user]);
$sedangDipinjam = $sedangDipinjam->fetchColumn();

$totalFavorit = $pdo->prepare("SELECT COUNT(*) FROM favorit WHERE id_user=?");
$totalFavorit->execute([$id_user]);
$totalFavorit = $totalFavorit->fetchColumn();

$rekomendasi = $pdo->query("SELECT b.* FROM buku b WHERE b.stok > 0 ORDER BY RAND() LIMIT 4")->fetchAll();

$pageTitle = 'Beranda';
require_once __DIR__ . '/../includes/header_user.php';
?>

<div class="greeting-box">
  <h1>Halo, <?= e($_SESSION['nama']) ?>! 👋 Mau baca buku apa hari ini?</h1>
  <div id="rotatingText">Semangat hari ini! 📖</div>
</div>

<div class="stat-grid">
  <div class="stat-card"><div class="num"><?= $sedangDipinjam ?></div><div class="label">Buku Sedang Dipinjam</div></div>
  <div class="stat-card"><div class="num"><?= $totalFavorit ?></div><div class="label">Buku di Favorit</div></div>
</div>

<div class="panel">
  <h3 style="color:var(--gold); margin-bottom:12px;">✨ Rekomendasi Untukmu</h3>
  <div class="book-grid">
    <?php foreach ($rekomendasi as $b): ?>
      <div class="book-card">
        <?php if ($b['cover']): ?>
          <img class="book-cover" src="../assets/uploads/<?= e($b['cover']) ?>" alt="<?= e($b['judul']) ?>">
        <?php else: ?>
          <div class="book-cover-placeholder">📗</div>
        <?php endif; ?>
        <div class="book-info">
          <div class="judul"><?= e($b['judul']) ?></div>
          <div class="pengarang"><?= e($b['pengarang']) ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
const kalimat = [
  "Semangat hari ini! 📖",
  "Buku adalah jembatan menuju dunia. 🌍",
  "Satu halaman hari ini, satu langkah lebih bijak. ✨",
  "Membaca sedikit, mengerti banyak. 💡",
  "Yuk pinjam buku baru dan mulai petualanganmu!",
  "Waktu terbaik membaca adalah sekarang. ⏳",
  "Buku yang baik adalah teman yang tak pernah mengecewakan. 💛"
];
let idx = 0;
const el = document.getElementById('rotatingText');
setInterval(() => {
  idx = (idx + 1) % kalimat.length;
  el.style.opacity = 0;
  setTimeout(() => { el.textContent = kalimat[idx]; el.style.opacity = 1; }, 300);
}, 15000);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
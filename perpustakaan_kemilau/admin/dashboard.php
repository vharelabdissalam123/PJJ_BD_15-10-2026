<?php
require_once __DIR__ . '/../config/koneksi.php';
$pageTitle = 'Dashboard Admin';
require_once __DIR__ . '/../includes/header_admin.php';

$totalBuku   = $pdo->query('SELECT COALESCE(SUM(stok),0) n FROM buku')->fetch()['n'];
$totalJudul  = $pdo->query('SELECT COUNT(*) n FROM buku')->fetch()['n'];
$totalUser   = $pdo->query("SELECT COUNT(*) n FROM users WHERE role='user'")->fetch()['n'];
$sedangPinjam= $pdo->query("SELECT COUNT(*) n FROM peminjaman WHERE status='dipinjam'")->fetch()['n'];
$telatKembali= $pdo->query("SELECT COUNT(*) n FROM peminjaman WHERE status='dipinjam' AND tanggal_kembali_rencana < CURDATE()")->fetch()['n'];

$populer = $pdo->query("
    SELECT b.judul, COUNT(p.id_peminjaman) total
    FROM peminjaman p JOIN buku b ON b.id_buku = p.id_buku
    GROUP BY p.id_buku ORDER BY total DESC LIMIT 5
")->fetchAll();

$aktif = $pdo->query("
    SELECT p.*, u.nama AS nama_user, b.judul
    FROM peminjaman p
    JOIN users u ON u.id_user = p.id_user
    JOIN buku b ON b.id_buku = p.id_buku
    WHERE p.status='dipinjam'
    ORDER BY p.tanggal_kembali_rencana ASC LIMIT 8
")->fetchAll();
?>

<div class="stat-grid">
  <div class="stat-card"><div class="num"><?= $totalJudul ?></div><div class="label">Judul Buku</div></div>
  <div class="stat-card"><div class="num"><?= $totalBuku ?></div><div class="label">Total Stok Buku</div></div>
  <div class="stat-card"><div class="num"><?= $totalUser ?></div><div class="label">Anggota Terdaftar</div></div>
  <div class="stat-card"><div class="num"><?= $sedangPinjam ?></div><div class="label">Sedang Dipinjam</div></div>
  <div class="stat-card"><div class="num" style="color:#e8a598"><?= $telatKembali ?></div><div class="label">Terlambat Kembali</div></div>
</div>

<div class="panel">
  <h3 style="color:var(--gold); margin-bottom:10px;">📈 Buku Terpopuler (Statistik Peminjaman)</h3>
  <table>
    <tr><th>Judul Buku</th><th>Jumlah Dipinjam</th></tr>
    <?php if (!$populer): ?><tr><td colspan="2">Belum ada data peminjaman.</td></tr><?php endif; ?>
    <?php foreach ($populer as $p): ?>
      <tr><td><?= e($p['judul']) ?></td><td><?= $p['total'] ?>x</td></tr>
    <?php endforeach; ?>
  </table>
</div>

<div class="panel">
  <h3 style="color:var(--gold); margin-bottom:10px;">🔄 Transaksi Sedang Berjalan</h3>
  <table>
    <tr><th>Anggota</th><th>Judul Buku</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Status</th></tr>
    <?php if (!$aktif): ?><tr><td colspan="5">Tidak ada transaksi aktif.</td></tr><?php endif; ?>
    <?php foreach ($aktif as $a): $telat = $a['tanggal_kembali_rencana'] < date('Y-m-d'); ?>
      <tr>
        <td><?= e($a['nama_user']) ?></td>
        <td><?= e($a['judul']) ?></td>
        <td><?= e($a['tanggal_pinjam']) ?></td>
        <td><?= e($a['tanggal_kembali_rencana']) ?></td>
        <td><span class="badge <?= $telat ? 'badge-danger' : 'badge-warning' ?>"><?= $telat ? 'Terlambat' : 'Dipinjam' ?></span></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

$stmt = $pdo->prepare("
    SELECT p.*, u.nama AS nama_user, b.judul
    FROM peminjaman p
    JOIN users u ON u.id_user=p.id_user
    JOIN buku b ON b.id_buku=p.id_buku
    WHERE p.tanggal_pinjam BETWEEN ? AND ?
    ORDER BY p.tanggal_pinjam ASC
");
$stmt->execute([$dari, $sampai]);
$data = $stmt->fetchAll();

$pageTitle = 'Laporan Transaksi';
require_once __DIR__ . '/../includes/header_admin.php';
?>

<div class="panel no-print">
  <form method="GET" style="display:flex; gap:12px; align-items:flex-end;">
    <div class="form-group"><label>Dari Tanggal</label><input type="date" name="dari" value="<?= e($dari) ?>"></div>
    <div class="form-group"><label>Sampai Tanggal</label><input type="date" name="sampai" value="<?= e($sampai) ?>"></div>
    <button class="btn btn-outline">Filter</button>
    <button type="button" class="btn btn-gold" onclick="window.print()">🧾 Cetak / Simpan sebagai PDF</button>
  </form>
</div>

<div class="panel">
  <h3 style="color:var(--gold); margin-bottom:5px;">Laporan Transaksi Peminjaman</h3>
  <p style="font-size:12px; opacity:.8; margin-bottom:15px;">Periode: <?= e($dari) ?> s/d <?= e($sampai) ?></p>
  <table>
    <tr><th>No</th><th>Anggota</th><th>Judul Buku</th><th>Tgl Pinjam</th><th>Rencana Kembali</th><th>Aktual Kembali</th><th>Status</th></tr>
    <?php if (!$data): ?><tr><td colspan="7">Tidak ada data pada periode ini.</td></tr><?php endif; ?>
    <?php foreach ($data as $i => $d): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><?= e($d['nama_user']) ?></td>
        <td><?= e($d['judul']) ?></td>
        <td><?= e($d['tanggal_pinjam']) ?></td>
        <td><?= e($d['tanggal_kembali_rencana']) ?></td>
        <td><?= e($d['tanggal_kembali_aktual'] ?? '-') ?></td>
        <td><?= $d['status']=='dikembalikan' ? 'Dikembalikan' : 'Dipinjam' ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <p style="margin-top:20px; font-size:11px; opacity:.7;">Dicetak pada: <?= date('d-m-Y H:i') ?> — Perpustakaan Kemilau</p>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
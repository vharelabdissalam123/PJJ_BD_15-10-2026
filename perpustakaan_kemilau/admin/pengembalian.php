<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

$msg = '';

if (isset($_GET['kembalikan'])) {
    $id_peminjaman = $_GET['kembalikan'];
    $stmt = $pdo->prepare('SELECT id_buku FROM peminjaman WHERE id_peminjaman=? AND status="dipinjam"');
    $stmt->execute([$id_peminjaman]);
    $row = $stmt->fetch();

    if ($row) {
        $pdo->beginTransaction();
        $pdo->prepare('UPDATE peminjaman SET status="dikembalikan", tanggal_kembali_aktual=? WHERE id_peminjaman=?')
            ->execute([date('Y-m-d'), $id_peminjaman]);
        $pdo->prepare('UPDATE buku SET stok = stok + 1 WHERE id_buku=?')->execute([$row['id_buku']]);
        $pdo->commit();
        $msg = 'Buku berhasil ditandai sebagai dikembalikan.';
    }
}

$aktif = $pdo->query("
    SELECT p.*, u.nama AS nama_user, b.judul
    FROM peminjaman p
    JOIN users u ON u.id_user=p.id_user
    JOIN buku b ON b.id_buku=p.id_buku
    WHERE p.status='dipinjam'
    ORDER BY p.tanggal_kembali_rencana ASC
")->fetchAll();

$pageTitle = 'Pengembalian Buku';
require_once __DIR__ . '/../includes/header_admin.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="panel">
  <h3 style="color:var(--gold); margin-bottom:10px;">↩️ Buku yang Sedang Dipinjam</h3>
  <table>
    <tr><th>Anggota</th><th>Judul Buku</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Status</th><th>Aksi</th></tr>
    <?php if (!$aktif): ?><tr><td colspan="6">Tidak ada buku yang perlu dikembalikan.</td></tr><?php endif; ?>
    <?php foreach ($aktif as $a): $telat = $a['tanggal_kembali_rencana'] < date('Y-m-d'); ?>
      <tr>
        <td><?= e($a['nama_user']) ?></td>
        <td><?= e($a['judul']) ?></td>
        <td><?= e($a['tanggal_pinjam']) ?></td>
        <td><?= e($a['tanggal_kembali_rencana']) ?></td>
        <td><span class="badge <?= $telat ? 'badge-danger' : 'badge-warning' ?>"><?= $telat ? 'Terlambat' : 'Dipinjam' ?></span></td>
        <td><a href="?kembalikan=<?= $a['id_peminjaman'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Tandai buku ini sudah dikembalikan?')">Proses Kembali</a></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
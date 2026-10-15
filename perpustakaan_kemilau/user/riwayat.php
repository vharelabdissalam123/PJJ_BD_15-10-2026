<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole('user');

$id_user = $_SESSION['id_user'];

$stmt = $pdo->prepare("
    SELECT p.*, b.judul, b.pengarang, b.cover
    FROM peminjaman p
    JOIN buku b ON b.id_buku = p.id_buku
    WHERE p.id_user = ?
    ORDER BY p.id_peminjaman DESC
");
$stmt->execute([$id_user]);
$riwayat = $stmt->fetchAll();

$pageTitle = 'Riwayat Peminjaman';
require_once __DIR__ . '/../includes/header_user.php';
?>

<div class="panel">
  <h3 style="color:var(--gold); margin-bottom:12px;">🕒 Riwayat Peminjaman Saya</h3>
  <table>
    <tr><th>Cover</th><th>Judul</th><th>Tgl Pinjam</th><th>Rencana Kembali</th><th>Aktual Kembali</th><th>Status</th></tr>
    <?php if (!$riwayat): ?><tr><td colspan="6">Kamu belum pernah meminjam buku.</td></tr><?php endif; ?>
    <?php foreach ($riwayat as $r): $telat = $r['status']=='dipinjam' && $r['tanggal_kembali_rencana'] < date('Y-m-d'); ?>
      <tr>
        <td>
          <?php if ($r['cover']): ?>
            <img src="../assets/uploads/<?= e($r['cover']) ?>" style="width:32px;height:44px;object-fit:cover;border-radius:3px;">
          <?php else: ?>📘<?php endif; ?>
        </td>
        <td><?= e($r['judul']) ?></td>
        <td><?= e($r['tanggal_pinjam']) ?></td>
        <td><?= e($r['tanggal_kembali_rencana']) ?></td>
        <td><?= e($r['tanggal_kembali_aktual'] ?? '-') ?></td>
        <td>
          <?php if ($r['status']=='dikembalikan'): ?><span class="badge badge-success">Sudah Dikembalikan</span>
          <?php elseif ($telat): ?><span class="badge badge-danger">Terlambat</span>
          <?php else: ?><span class="badge badge-warning">Masih Dipinjam</span><?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
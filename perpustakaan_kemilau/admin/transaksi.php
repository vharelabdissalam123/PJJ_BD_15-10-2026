<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pinjamkan'])) {
    $id_user = $_POST['id_user'];
    $id_buku = $_POST['id_buku'];
    $tgl_pinjam = date('Y-m-d');
    $tgl_kembali = $_POST['tanggal_kembali_rencana'];

    $cekStok = $pdo->prepare('SELECT stok FROM buku WHERE id_buku=?');
    $cekStok->execute([$id_buku]);
    $stok = $cekStok->fetchColumn();

    if ($stok > 0) {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO peminjaman (id_user, id_buku, tanggal_pinjam, tanggal_kembali_rencana, status) VALUES (?,?,?,?,"dipinjam")');
        $stmt->execute([$id_user, $id_buku, $tgl_pinjam, $tgl_kembali]);
        $pdo->prepare('UPDATE buku SET stok = stok - 1 WHERE id_buku=?')->execute([$id_buku]);
        $pdo->commit();
        $msg = 'Peminjaman berhasil dicatat.';
    } else {
        $msg = 'Stok buku habis, tidak bisa dipinjamkan.';
    }
}

$daftarUser = $pdo->query("SELECT id_user, nama FROM users WHERE role='user' ORDER BY nama")->fetchAll();
$daftarBuku = $pdo->query("SELECT id_buku, judul, stok FROM buku WHERE stok > 0 ORDER BY judul")->fetchAll();

$riwayat = $pdo->query("
    SELECT p.*, u.nama AS nama_user, b.judul
    FROM peminjaman p
    JOIN users u ON u.id_user=p.id_user
    JOIN buku b ON b.id_buku=p.id_buku
    ORDER BY p.id_peminjaman DESC
")->fetchAll();

$pageTitle = 'Transaksi Peminjaman';
require_once __DIR__ . '/../includes/header_admin.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="panel">
  <h3 style="color:var(--gold); margin-bottom:15px;">➕ Catat Peminjaman Baru</h3>
  <form method="POST">
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:15px;">
      <div class="form-group">
        <label>Anggota</label>
        <select name="id_user" required>
          <option value="">-- Pilih Anggota --</option>
          <?php foreach ($daftarUser as $u): ?><option value="<?= $u['id_user'] ?>"><?= e($u['nama']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Buku</label>
        <select name="id_buku" required>
          <option value="">-- Pilih Buku --</option>
          <?php foreach ($daftarBuku as $b): ?><option value="<?= $b['id_buku'] ?>"><?= e($b['judul']) ?> (stok: <?= $b['stok'] ?>)</option><?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Tanggal Pinjam</label>
        <input type="text" value="<?= date('d-m-Y') ?> (hari ini)" disabled>
      </div>
    </div>
    <div class="form-group" style="max-width:250px;">
      <label>Tanggal Kembali (Rencana)</label>
      <input type="date" name="tanggal_kembali_rencana" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
    </div>
    <button type="submit" name="pinjamkan" class="btn btn-gold">Catat Peminjaman</button>
  </form>
</div>

<div class="panel">
  <h3 style="color:var(--gold); margin-bottom:10px;">📋 Riwayat Semua Transaksi</h3>
  <table>
    <tr><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Rencana Kembali</th><th>Aktual Kembali</th><th>Status</th></tr>
    <?php foreach ($riwayat as $r): $telat = $r['status']=='dipinjam' && $r['tanggal_kembali_rencana'] < date('Y-m-d'); ?>
      <tr>
        <td><?= e($r['nama_user']) ?></td>
        <td><?= e($r['judul']) ?></td>
        <td><?= e($r['tanggal_pinjam']) ?></td>
        <td><?= e($r['tanggal_kembali_rencana']) ?></td>
        <td><?= e($r['tanggal_kembali_aktual'] ?? '-') ?></td>
        <td>
          <?php if ($r['status']=='dikembalikan'): ?><span class="badge badge-success">Dikembalikan</span>
          <?php elseif ($telat): ?><span class="badge badge-danger">Terlambat</span>
          <?php else: ?><span class="badge badge-warning">Dipinjam</span><?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<?php require_once __DIR__ . '/auth.php'; requireRole('admin'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perpustakaan Kemilau - Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="brand">[ADMIN] Perpustakaan<span>Kemilau</span></div>
    <nav>
      <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':'' ?>">🖥️ Dashboard</a>
      <a href="buku.php" class="<?= basename($_SERVER['PHP_SELF'])=='buku.php'?'active':'' ?>">📖 Data Buku</a>
      <a href="anggota.php" class="<?= basename($_SERVER['PHP_SELF'])=='anggota.php'?'active':'' ?>">👥 Data Anggota</a>
      <a href="transaksi.php" class="<?= basename($_SERVER['PHP_SELF'])=='transaksi.php'?'active':'' ?>">🔄 Transaksi Peminjaman</a>
      <a href="pengembalian.php" class="<?= basename($_SERVER['PHP_SELF'])=='pengembalian.php'?'active':'' ?>">↩️ Pengembalian</a>
      <a href="laporan.php" class="<?= basename($_SERVER['PHP_SELF'])=='laporan.php'?'active':'' ?>">🧾 Laporan</a>
      <a href="../logout.php" class="logout">🚪 Keluar</a>
    </nav>
  </aside>
  <main class="content">
    <div class="topbar">
      <h2><?= e($pageTitle ?? 'Dashboard') ?></h2>
      <div class="user-chip">👤 <?= e($_SESSION['nama']) ?> <span>(Admin)</span></div>
    </div>
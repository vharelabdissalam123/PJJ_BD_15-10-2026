<?php require_once __DIR__ . '/auth.php'; requireRole('user'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perpustakaan Kemilau</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="brand"> Perpustakaan<span>Kemilau</span></div>
    <nav>
      <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':'' ?>">🏠 Beranda</a>
      <a href="buku.php" class="<?= basename($_SERVER['PHP_SELF'])=='buku.php'?'active':'' ?>">📖 Daftar Buku</a>
      <a href="rekomendasi.php" class="<?= basename($_SERVER['PHP_SELF'])=='rekomendasi.php'?'active':'' ?>">✨ Rekomendasi</a>
      <a href="favorit.php" class="<?= basename($_SERVER['PHP_SELF'])=='favorit.php'?'active':'' ?>">⭐ Favorit Saya</a>
      <a href="riwayat.php" class="<?= basename($_SERVER['PHP_SELF'])=='riwayat.php'?'active':'' ?>">🕒 Riwayat Pinjam</a>
      <a href="../logout.php" class="logout">🚪 Keluar</a>
    </nav>
  </aside>
  <main class="content">
    <div class="topbar">
      <h2><?= e($pageTitle ?? 'Beranda') ?></h2>
      <div class="user-chip">👤 <?= e($_SESSION['nama']) ?></div>
    </div>
<?php
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $nama === '' || $password === '') {
        $error = 'Semua field wajib diisi.';
    } else {
        $cek = $pdo->prepare('SELECT id_user FROM users WHERE username = ?');
        $cek->execute([$username]);
        if ($cek->fetch()) {
            $error = 'Username sudah digunakan.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password, nama, email, role) VALUES (?, ?, ?, ?, "user")');
            $stmt->execute([$username, $hash, $nama, $email]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar - Perpustakaan Kemilau</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
  body{ display:flex; align-items:center; justify-content:center; }
  .auth-box{ background:var(--navy-dark); border:2px solid var(--gold); border-radius:10px; padding:35px; width:100%; max-width:380px; }
  .auth-box h1{ color:var(--gold); text-align:center; margin-bottom:5px; font-size:22px; }
  .auth-box .btn{ width:100%; margin-top:10px; }
  .switch-link{ text-align:center; margin-top:15px; font-size:13px; }
  .switch-link a{ color:var(--gold); text-decoration:underline; }
</style>
</head>
<body>
  <div class="auth-box">
    <h1>📚 Daftar Anggota</h1>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama" required></div>
      <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
      <div class="form-group"><label>Email</label><input type="email" name="email"></div>
      <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
      <button type="submit" class="btn btn-gold">Daftar</button>
    </form>
    <div class="switch-link">Sudah punya akun? <a href="login.php">Masuk</a></div>
  </div>
</body>
</html>
<?php
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . (currentRole() === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama']     = $user['nama'];
            $_SESSION['role']     = $user['role'];
            header('Location: ' . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Perpustakaan Kemilau</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
  body{ display:flex; align-items:center; justify-content:center; }
  .auth-box{ background:var(--navy-dark); border:2px solid var(--gold); border-radius:10px; padding:35px; width:100%; max-width:380px; }
  .auth-box h1{ color:var(--gold); text-align:center; margin-bottom:5px; font-size:22px; }
  .auth-box p.sub{ text-align:center; font-size:12px; opacity:.8; margin-bottom:20px; }
  .auth-box .btn{ width:100%; margin-top:10px; }
  .switch-link{ text-align:center; margin-top:15px; font-size:13px; }
  .switch-link a{ color:var(--gold); text-decoration:underline; }
</style>
</head>
<body>
  <div class="auth-box">
    <h1>📚 Perpustakaan Kemilau</h1>
    <p class="sub">Masuk untuk melanjutkan</p>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <?php if (isset($_GET['err']) && $_GET['err'] === 'akses_ditolak'): ?>
      <div class="alert alert-danger">Akses ditolak untuk halaman tersebut.</div>
    <?php endif; ?>
    <?php if (isset($_GET['registered'])): ?>
      <div class="alert alert-success">Registrasi berhasil, silakan login.</div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group"><label>Username</label><input type="text" name="username" required autofocus></div>
      <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
      <button type="submit" class="btn btn-gold">Masuk</button>
    </form>
    <div class="switch-link">Belum punya akun? <a href="register.php">Daftar sebagai anggota</a></div>
  </div>
</body>
</html>
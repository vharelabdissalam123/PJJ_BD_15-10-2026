<?php
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) {
    header('Location: ' . (currentRole() === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
} else {
    header('Location: login.php');
}
exit;
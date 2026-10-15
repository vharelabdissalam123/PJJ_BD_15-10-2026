<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return isset($_SESSION['id_user']);
}

function currentRole(): ?string {
    return $_SESSION['role'] ?? null;
}

// Dipakai hanya dari dalam folder admin/ atau user/, makanya "../login.php"
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit;
    }
}

function requireRole(string $role): void {
    requireLogin();
    if (currentRole() !== $role) {
        header('Location: ../login.php?err=akses_ditolak');
        exit;
    }
}

function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
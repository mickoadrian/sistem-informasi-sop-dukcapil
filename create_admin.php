<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Perintah ini hanya dapat dijalankan melalui terminal.');
}

require_once __DIR__ . '/config.php';

$username = trim((string) getenv('ADMIN_USERNAME'));
$password = (string) getenv('ADMIN_PASSWORD');

if ($username === '' || strlen($password) < 8) {
    fwrite(STDERR, "Tetapkan ADMIN_USERNAME dan ADMIN_PASSWORD minimal 8 karakter.\n");
    exit(1);
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$statement = $conn->prepare(
    'INSERT INTO admin (username, password) VALUES (?, ?) '
    . 'ON DUPLICATE KEY UPDATE password = VALUES(password)'
);
$statement->bind_param('ss', $username, $passwordHash);
$statement->execute();
$statement->close();

fwrite(STDOUT, "Akun admin berhasil dibuat atau diperbarui.\n");

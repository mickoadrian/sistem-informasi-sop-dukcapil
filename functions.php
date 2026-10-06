<?php

require_once __DIR__ . '/config.php';

function start_secure_session()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params(0, '/', '', $secure, true);
    session_start();
}

function array_value($array, $key, $default = null)
{
    return is_array($array) && isset($array[$key]) ? $array[$key] : $default;
}

function secure_random_bytes($length)
{
    if (function_exists('random_bytes')) {
        return random_bytes($length);
    }

    if (function_exists('openssl_random_pseudo_bytes')) {
        $strong = false;
        $bytes = openssl_random_pseudo_bytes($length, $strong);

        if ($bytes !== false && $strong) {
            return $bytes;
        }
    }

    throw new RuntimeException('Sumber bilangan acak yang aman tidak tersedia.');
}

function require_admin()
{
    start_secure_session();

    if (empty($_SESSION['admin_username'])) {
        header('Location: login.php');
        exit;
    }
}

function csrf_token()
{
    start_secure_session();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(secure_random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token)
{
    start_secure_session();

    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function upload_file($file, array $allowedMimeTypes, $prefix, $maxBytes)
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Proses unggah file gagal.');
    }

    if ($file['size'] > $maxBytes) {
        throw new RuntimeException('Ukuran file melebihi batas yang diizinkan.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!isset($allowedMimeTypes[$mimeType])) {
        throw new RuntimeException('Jenis file tidak diizinkan.');
    }

    $uploadDirectory = __DIR__ . '/uploads';
    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true)) {
        throw new RuntimeException('Folder unggahan tidak dapat dibuat.');
    }

    $fileName = $prefix . bin2hex(secure_random_bytes(12)) . '.' . $allowedMimeTypes[$mimeType];
    $destination = $uploadDirectory . '/' . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('File tidak dapat disimpan.');
    }

    return $fileName;
}

function delete_uploaded_file($fileName)
{
    if (!$fileName) {
        return;
    }

    $safeName = basename($fileName);
    $path = __DIR__ . '/uploads/' . $safeName;

    if (is_file($path)) {
        unlink($path);
    }
}

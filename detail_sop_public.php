<?php
require_once __DIR__ . '/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

$statement = $conn->prepare('SELECT * FROM sop WHERE id = ?');
$statement->bind_param('i', $id);
$statement->execute();
$sop = $statement->get_result()->fetch_assoc();
$statement->close();

if (!$sop) {
    http_response_code(404);
    exit('Data SOP tidak ditemukan.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail SOP - Disdukcapil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 30px;
            max-width: 800px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            color: #3498db;
            margin-bottom: 20px;
        }
        .sop-image {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        p {
            font-size: 1.1rem;
            line-height: 1.6;
        }
        .download-container {
            margin-top: 30px;
            text-align: center;
        }
        a.button {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        a.button:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>

<div class="container">
    <h1><?= e($sop['judul_sop']) ?></h1>


    <p><?= nl2br(e($sop['deskripsi'])) ?></p>


    <!-- Tampilkan gambar jika ada -->
    <?php if (!empty($sop['gambar'])): ?>
        <img src="uploads/<?= e(basename($sop['gambar'])) ?>" alt="Gambar SOP" class="sop-image">
    <?php endif; ?>


    <!-- Tampilkan tombol unduh PDF jika ada -->
    <?php if (!empty($sop['file_pdf'])): ?>
        <div class="download-container">
            <a class="button" href="uploads/<?= e(basename($sop['file_pdf'])) ?>" target="_blank" rel="noopener">Klik untuk Mengunduh PDF</a>
        </div>
    <?php else: ?>
        <p>Tidak ada file PDF yang terlampir.</p>
    <?php endif; ?>

    <div class="download-container">
        <a class="button" href="index.php">← Kembali ke Daftar SOP</a>
    </div>
</div>

</body>
</html>

<?php
require_once __DIR__ . '/functions.php';
require_admin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('ID SOP tidak valid.');
}

$selectStatement = $conn->prepare('SELECT * FROM sop WHERE id = ?');
$selectStatement->bind_param('i', $id);
$selectStatement->execute();
$sop = $selectStatement->get_result()->fetch_assoc();
$selectStatement->close();

if (!$sop) {
    http_response_code(404);
    exit('SOP tidak ditemukan.');
}

$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token(array_value($_POST, 'csrf_token'))) {
        http_response_code(400);
        $error = 'Permintaan tidak valid. Silakan muat ulang halaman.';
    } else {
        $judulSop = trim((string) array_value($_POST, 'judul_sop', ''));
        $deskripsi = trim((string) array_value($_POST, 'deskripsi', ''));

        if ($judulSop === '' || $deskripsi === '') {
            $error = 'Judul dan deskripsi SOP wajib diisi.';
        } else {
            $uploadedPdf = null;
            $uploadedImage = null;

            try {
                $uploadedPdf = upload_file(
                    array_value($_FILES, 'file_pdf', array()),
                    ['application/pdf' => 'pdf'],
                    'sop_',
                    5 * 1024 * 1024
                );
                $uploadedImage = upload_file(
                    array_value($_FILES, 'gambar', array()),
                    [
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/webp' => 'webp',
                    ],
                    'img_',
                    3 * 1024 * 1024
                );

                $newPdf = $uploadedPdf ?: $sop['file_pdf'];
                $newImage = $uploadedImage ?: $sop['gambar'];

                $updateStatement = $conn->prepare(
                    'UPDATE sop SET judul_sop = ?, deskripsi = ?, file_pdf = ?, gambar = ? WHERE id = ?'
                );
                $updateStatement->bind_param('ssssi', $judulSop, $deskripsi, $newPdf, $newImage, $id);
                $updateStatement->execute();
                $updateStatement->close();

                if ($uploadedPdf) {
                    delete_uploaded_file($sop['file_pdf']);
                }
                if ($uploadedImage) {
                    delete_uploaded_file($sop['gambar']);
                }

                $sop['judul_sop'] = $judulSop;
                $sop['deskripsi'] = $deskripsi;
                $sop['file_pdf'] = $newPdf;
                $sop['gambar'] = $newImage;
                $message = 'SOP berhasil diperbarui.';
            } catch (Exception $exception) {
                delete_uploaded_file($uploadedPdf);
                delete_uploaded_file($uploadedImage);
                error_log($exception->getMessage());
                $error = $exception instanceof RuntimeException
                    ? $exception->getMessage()
                    : 'SOP belum dapat diperbarui.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit SOP</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #3498db;
            font-size: 24px;
            margin-bottom: 20px;
        }
        label {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }
        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 14px;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
        }
        button {
            padding: 12px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        button:hover {
            background-color: #2980b9;
        }
        .message {
            color: green;
            margin-top: 15px;
        }
        .error {
            color: red;
            margin-top: 15px;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #3498db;
        }
        a:hover {
            text-decoration: underline;
        }
        .preview-image {
            max-height: 200px;
            margin-top: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit SOP</h2>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label for="judul_sop">Judul SOP:</label>
        <input type="text" name="judul_sop" id="judul_sop" value="<?= e($sop['judul_sop']) ?>" required>

        <label for="deskripsi">Deskripsi:</label>
        <textarea name="deskripsi" id="deskripsi" rows="4" required><?= e($sop['deskripsi']) ?></textarea>

        <label for="file_pdf">Upload File PDF (Opsional):</label>
        <input type="file" name="file_pdf" id="file_pdf" accept="application/pdf">

        <label for="gambar">Upload Gambar (Opsional):</label>
        <input type="file" name="gambar" id="gambar" accept="image/*">

        <!-- Preview gambar lama -->
        <?php if (!empty($sop['gambar'])): ?>
            <p>Gambar saat ini:</p>
            <img src="uploads/<?= e(basename($sop['gambar'])) ?>" class="preview-image" alt="Gambar SOP saat ini">
        <?php endif; ?>

        <button type="submit">Perbarui SOP</button>
    </form>

    <?php if ($message): ?><p class="message"><?= e($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>

    <a href="dashboard.php">← Kembali ke Dashboard</a>
</div>

</body>
</html>

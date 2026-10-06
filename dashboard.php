<?php
require_once __DIR__ . '/functions.php';
require_admin();

$username = $_SESSION['admin_username'];
$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token(array_value($_POST, 'csrf_token'))) {
        http_response_code(400);
        $error = 'Permintaan tidak valid. Silakan muat ulang halaman.';
    } else {
        $action = array_value($_POST, 'action', '');

        if ($action === 'add') {
            $judulSop = trim((string) array_value($_POST, 'judul_sop', ''));
            $deskripsi = trim((string) array_value($_POST, 'deskripsi', ''));
            $filePdf = null;
            $gambar = null;

            if ($judulSop === '' || $deskripsi === '') {
                $error = 'Judul dan deskripsi SOP wajib diisi.';
            } else {
                try {
                    $filePdf = upload_file(
                        array_value($_FILES, 'file_pdf', array()),
                        ['application/pdf' => 'pdf'],
                        'sop_',
                        5 * 1024 * 1024
                    );
                    $gambar = upload_file(
                        array_value($_FILES, 'gambar', array()),
                        [
                            'image/jpeg' => 'jpg',
                            'image/png' => 'png',
                            'image/webp' => 'webp',
                        ],
                        'img_',
                        3 * 1024 * 1024
                    );

                    $statement = $conn->prepare(
                        'INSERT INTO sop (judul_sop, deskripsi, file_pdf, gambar) VALUES (?, ?, ?, ?)'
                    );
                    $statement->bind_param('ssss', $judulSop, $deskripsi, $filePdf, $gambar);
                    $statement->execute();
                    $statement->close();
                    $message = 'SOP berhasil ditambahkan.';
                } catch (Exception $exception) {
                    delete_uploaded_file($filePdf);
                    delete_uploaded_file($gambar);
                    error_log($exception->getMessage());
                    $error = $exception instanceof RuntimeException
                        ? $exception->getMessage()
                        : 'SOP belum dapat disimpan.';
                }
            }
        }

        if ($action === 'delete') {
            $id = filter_var(array_value($_POST, 'id'), FILTER_VALIDATE_INT);

            if (!$id) {
                $error = 'ID SOP tidak valid.';
            } else {
                $selectStatement = $conn->prepare('SELECT file_pdf, gambar FROM sop WHERE id = ?');
                $selectStatement->bind_param('i', $id);
                $selectStatement->execute();
                $row = $selectStatement->get_result()->fetch_assoc();
                $selectStatement->close();

                if (!$row) {
                    $error = 'SOP tidak ditemukan.';
                } else {
                    $deleteStatement = $conn->prepare('DELETE FROM sop WHERE id = ?');
                    $deleteStatement->bind_param('i', $id);
                    $deleteStatement->execute();
                    $deleteStatement->close();

                    delete_uploaded_file($row['file_pdf']);
                    delete_uploaded_file($row['gambar']);
                    $message = 'SOP berhasil dihapus.';
                }
            }
        }
    }
}

$sop_result = $conn->query('SELECT * FROM sop ORDER BY id DESC');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SOP</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        .sidebar {
            width: 200px;
            background-color: #3498db;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
            padding: 8px;
            background: #2980b9;
            border-radius: 4px;
        }
        .sidebar a:hover {
            background-color: #2471a3;
        }
        .content {
            margin-left: 220px;
            padding: 30px;
        }
        .form-container, .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .table-container {
            overflow-x: auto;
        }
        h2 {
            color: #3498db;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 15px;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
        }
        button:hover {
            background: #2980b9;
        }
        .message { color: green; }
        .error { color: red; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }
        img {
            max-width: 100px;
            height: auto;
        }
        .download-link,
        .edit-link,
        .delete-link {
            color: #3498db;
            text-decoration: none;
        }
        .delete-link:hover {
            color: red;
        }
        .delete-form {
            display: inline;
        }
        .delete-button {
            margin: 0;
            padding: 0;
            color: #c0392b;
            background: transparent;
            border: 0;
            cursor: pointer;
            font: inherit;
        }
        @media (max-width: 800px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }
            .content {
                margin-left: 0;
                padding: 18px;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Dashboard</h2>
    <a href="dashboard.php">Home</a>
    <a href="logout.php">Logout</a>
</div>

<div class="content">
    <h1>Selamat Datang, <?= e($username) ?></h1>

    <div class="form-container">
        <h2>Tambah SOP Baru</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="add">
            <label for="judul_sop">Judul SOP:</label>
            <input type="text" name="judul_sop" id="judul_sop" required>

            <label for="deskripsi">Deskripsi:</label>
            <textarea name="deskripsi" id="deskripsi" rows="4" required></textarea>

            <label for="file_pdf">Upload File PDF (Opsional):</label>
            <input type="file" name="file_pdf" id="file_pdf" accept="application/pdf">

            <label for="gambar">Upload Gambar (Opsional):</label>
            <input type="file" name="gambar" id="gambar" accept="image/*">

            <button type="submit">Tambah SOP</button>
        </form>

        <?php if ($message): ?><p class="message"><?= e($message) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    </div>

    <div class="table-container">
        <h2>Daftar SOP</h2>
        <?php if ($sop_result && $sop_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>PDF</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $sop_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= e($row['judul_sop']) ?></td>
                            <td><?= nl2br(e($row['deskripsi'])) ?></td>
                            <td>
                                <?php if (!empty($row['file_pdf'])): ?>
                                    <a class="download-link" href="uploads/<?= e(basename($row['file_pdf'])) ?>" target="_blank" rel="noopener">Download PDF</a>
                                <?php else: ?>
                                    Tidak ada file
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($row['gambar'])): ?>
                                    <img src="uploads/<?= e(basename($row['gambar'])) ?>" alt="Gambar SOP">
                                <?php else: ?>
                                    Tidak ada gambar
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_sop.php?id=<?= (int) $row['id'] ?>" class="edit-link">Edit</a> |
                                <form method="POST" class="delete-form" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SOP ini?')">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                    <button type="submit" class="delete-button">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Belum ada SOP yang ditambahkan.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

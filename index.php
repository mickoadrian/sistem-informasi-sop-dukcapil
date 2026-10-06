<?php
require_once __DIR__ . '/functions.php';

$result = $conn->query('SELECT id, judul_sop, deskripsi FROM sop ORDER BY id DESC');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar SOP - Disdukcapil</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            color: #333;
        }
        header {
            background-color: #3498db;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        nav ul {
            list-style-type: none;
            padding: 10px 0;
            text-align: center;
        }
        nav ul li {
            display: inline-block;
            margin: 0 15px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            background-color: #2980b9;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        nav ul li a:hover {
            background-color: #1c6ba0;
        }
        main {
            padding: 20px;
            text-align: center;
        }
        section h2 {
            margin-bottom: 20px;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 250px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card img.card-img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        .card h3 {
            color: #3498db;
            margin-bottom: 10px;
        }
        .card p {
            color: #555;
            font-size: 0.95rem;
            margin-bottom: 15px;
        }
        .card a {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
        }
        .card a:hover {
            background-color: #2980b9;
        }
        footer {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            text-align: center;
            margin-top: 30px;
        }
        @media (max-width: 768px) {
            .card {
                width: 80%;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Daftar SOP Pelayanan Disdukcapil</h1>
        <nav>
            <ul>
                <li><a href="login.php">Dashboard Admin</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Daftar SOP Pelayanan</h2>
            <div class="card-container">
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $id = (int) $row['id'];
                        $judul = e($row['judul_sop']);
                        $deskripsi = e(substr($row['deskripsi'], 0, 100));

                        echo "<div class='card'>";




                        echo "  <h3>{$judul}</h3>
                                <p>{$deskripsi}...</p>
                                <a href='detail_sop_public.php?id={$id}'>Lihat Detail</a>
                              </div>";
                    }
                } else {
                    echo "<p>Tidak ada SOP yang tersedia.</p>";
                }
                ?>
            </div>
        </section>
    </main>


</body>
</html>

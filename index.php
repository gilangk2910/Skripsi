<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Random Forest</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header class="bg-primary text-white p-3">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="h3">Aplikasi Random Forest</h1>
            <nav>
                <ul class="nav">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link text-white"><i class="material-icons">dashboard</i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="input.php" class="nav-link text-white"><i class="material-icons">storage</i> Input</a>
                    </li>
                    <li class="nav-item">
                        <a href="proses.php" class="nav-link text-white"><i class="material-icons">check_circle</i> Proses</a>
                    </li>
                </ul>
            </nav>
            <div class="user-profile d-flex align-items-center">
                <img src="user-profile.jpg" alt="Profile Picture" class="rounded-circle" width="30" height="30">
                <span class="ml-2">10520061 Raden Gilang Komara</span>
            </div>
        </div>
    </header>
    <main class="container-fluid my-4">
        <div class="breadcrumb mb-4">
            <span>Home</span> &gt; <span>Dashboard</span>
        </div>
        <div class="content">
            <h2 class="mb-4">Dashboard</h2>
            <div class="card p-3 mb-4">
                <p>Implementasi Metode Random Forest untuk Prediksi 10520061 Raden Gilang Komara</p>
            </div>
            <div class="card p-3 mb-4">
                <form action="upload.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="csvFile">Upload file .csv:</label>
                        <input type="file" name="csvFile" id="csvFile" accept=".csv" class="form-control-file">
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
            <div class="card p-3 mb-4">
                <form method="get" action="index.php">
                    <div class="form-group">
                        <label for="dataCount">Tampilkan jumlah data:</label>
                        <select name="dataCount" id="dataCount" class="form-control" style="max-width: 200px;">
                            <option value="10" <?php if (isset($_GET['dataCount']) && $_GET['dataCount'] == '10') echo 'selected'; ?>>10</option>
                            <option value="50" <?php if (isset($_GET['dataCount']) && $_GET['dataCount'] == '50') echo 'selected'; ?>>50</option>
                            <option value="100" <?php if (isset($_GET['dataCount']) && $_GET['dataCount'] == '100') echo 'selected'; ?>>100</option>
                            <option value="all" <?php if (isset($_GET['dataCount']) && $_GET['dataCount'] == 'all') echo 'selected'; ?>>Semua</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>

            <?php if (file_exists('upload.csv')): ?>
                <?php
                $total_rumah_sakit_umum = 0;
                $total_rumah_sakit_khusus = 0;

                if (($handle = fopen('upload.csv', "r")) !== FALSE) {
                    // Skip the header row
                    fgetcsv($handle, 1000, ";");

                    // Loop through the file to count the hospital types
                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                        if (strcasecmp($data[7], 'RUMAH SAKIT UMUM') === 0) {
                            $total_rumah_sakit_umum++;
                        } elseif (strcasecmp($data[7], 'RUMAH SAKIT KHUSUS') === 0) {
                            $total_rumah_sakit_khusus++;
                        }
                    }

                    fclose($handle);
                }
                ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card text-white bg-info text-center mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Rumah Sakit Umum</h5>
                                <h2 class="card-text"><?= $total_rumah_sakit_umum ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card text-white bg-primary text-center mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Rumah Sakit Khusus</h5>
                                <h2 class="card-text"><?= $total_rumah_sakit_khusus ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div>
                <?php
                // Include and call the function to display table data if file exists
                if (file_exists('upload.csv')) {
                    include 'display_table.php';
                }
                ?>
            </div>
        </div>
    </main>
    <footer class="bg-light py-3 text-center">
        <p>&copy; Random Forest 10520061 Raden Gilang Komara</p>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
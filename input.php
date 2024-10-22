<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data - Aplikasi Random Forest</title>
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
                        <a href="input_process.php" class="nav-link text-white"><i class="material-icons">storage</i> Input</a>
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
            <span>Home</span> &gt; <span>Input</span>
        </div>
        <div class="content">
            <h2 class="mb-4">Input Data</h2>
            <div class="card p-3 mb-4">
                <form action="" method="post">
                    <div class="form-group">
                        <label for="bed_occupation_rate">Bed Occupation Rate (Persen):</label>
                        <input type="number" step="0.01" name="bed_occupation_rate" id="bed_occupation_rate" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="gross_death_rate">Gross Death Rate (Persen):</label>
                        <input type="number" step="0.01" name="gross_death_rate" id="gross_death_rate" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="net_death_rate">Net Death Rate (Persen):</label>
                        <input type="number" step="0.01" name="net_death_rate" id="net_death_rate" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="bed_turn_over">Bed Turn Over (Kali):</label>
                        <input type="number" step="0.01" name="bed_turn_over" id="bed_turn_over" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="turn_over_interval">Turn Over Interval (Hari):</label>
                        <input type="number" step="0.01" name="turn_over_interval" id="turn_over_interval" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="average_length_of_stay">Average Length of Stay (Hari):</label>
                        <input type="number" step="0.01" name="average_length_of_stay" id="average_length_of_stay" class="form-control" required>
                    </div>
                    <div class="form-group form-check">
                        <input type="radio" class="form-check-input" id="rumah_sakit_umum" name="hospital_type" value="umum">
                        <label class="form-check-label" for="rumah_sakit_umum">Rumah Sakit Umum</label>
                    </div>
                    <div class="form-group form-check">
                        <input type="radio" class="form-check-input" id="rumah_sakit_khusus" name="hospital_type" value="khusus">
                        <label class="form-check-label" for="rumah_sakit_khusus">Rumah Sakit Khusus</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Get form data
                $bed_occupation_rate = $_POST['bed_occupation_rate'];
                $gross_death_rate = $_POST['gross_death_rate'];
                $net_death_rate = $_POST['net_death_rate'];
                $bed_turn_over = $_POST['bed_turn_over'];
                $turn_over_interval = $_POST['turn_over_interval'];
                $average_length_of_stay = $_POST['average_length_of_stay'];

                // Get hospital type from the radio button
                $hospital_type = $_POST['hospital_type'];
                $hospital_type_display = ($hospital_type == "umum") ? "RUMAH SAKIT UMUM" : "RUMAH SAKIT KHUSUS";

                // Determine the model type based on hospital type
                $model_type = ($hospital_type == "umum") ? 'rsu' : 'rsk';

                // Display inserted data in a table
                echo "<table class='table table-bordered'>";
                echo "<thead class='thead-light'>";
                echo "<tr>";
                echo "<th>jenis_rumah_sakit</th>";
                echo "<th>bed_occupation_rate (Persen)</th>";
                echo "<th>gross_death_rate (Persen)</th>";
                echo "<th>net_death_rate (Persen)</th>";
                echo "<th>bed_turn_over (Kali)</th>";
                echo "<th>turn_over_interval (Hari)</th>";
                echo "<th>average_length_of_stay (Hari)</th>";
                echo "<th>Prediction</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr>";
                echo "<td>$hospital_type_display</td>";
                echo "<td>$bed_occupation_rate</td>";
                echo "<td>$gross_death_rate</td>";
                echo "<td>$net_death_rate</td>";
                echo "<td>$bed_turn_over</td>";
                echo "<td>$turn_over_interval</td>";
                echo "<td>$average_length_of_stay</td>";

                // Prepare data for prediction
                $inputData = [
                    [$bed_occupation_rate, $gross_death_rate, $net_death_rate, $bed_turn_over, $turn_over_interval, $average_length_of_stay]
                ];

                $inputJson = json_encode($inputData);

                // Command to execute Python script with model type and input data
                $command = 'export LC_ALL=C.UTF-8 && export LANG=C.UTF-8 && python3 run_model.py ' . $model_type . ' \'' . $inputJson . '\'';
                $output = shell_exec($command);

                if ($output === null || trim($output) === '') {
                    echo "<td>Error: No output returned from the Python script.</td>";
                } else {
                    $predictions = json_decode($output, true);
                    if ($predictions === null) {
                        echo "<td>Error: Failed to decode JSON. Raw output was: " . $output . "</td>";
                    } else {
                        echo "<td>" . $predictions[0] . "</td>";
                    }
                }
                echo "</tr>";
                echo "</tbody>";
                echo "</table>";

                if (isset($predictions[0])) {
                    // Classification result
                    $classification = "";
                    switch ($predictions[0]) {
                        case 0:
                            $classification = "Kurang Baik";
                            break;
                        case 1:
                            $classification = "Baik";
                            break;
                        case 2:
                            $classification = "Sangat Baik";
                            break;
                    }

                    echo "<hr><h3 class='text-center'><span class='badge bg-primary text-white p-4'>Termasuk kedalam klasifikasi \"$classification\"</span></h3>";
                }
            }
            ?>
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
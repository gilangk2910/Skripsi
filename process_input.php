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

    // Determine the model type based on hospital type
    $model_type = ($hospital_type == "umum") ? 'rsu' : 'rsk';

    // Display submitted data
    echo "Data berhasil disimpan:";
    echo "<br>Bed Occupation Rate: $bed_occupation_rate%";
    echo "<br>Gross Death Rate: $gross_death_rate%";
    echo "<br>Net Death Rate: $net_death_rate%";
    echo "<br>Bed Turn Over: $bed_turn_over Kali";
    echo "<br>Turn Over Interval: $turn_over_interval Hari";
    echo "<br>Average Length of Stay: $average_length_of_stay Hari";
    echo "<br>Tipe Rumah Sakit: $hospital_type atau " . $_POST['hospital_type'];

    // Prepare data for prediction
    $inputData = [
        [$bed_occupation_rate, $gross_death_rate, $net_death_rate, $bed_turn_over, $turn_over_interval, $average_length_of_stay]
    ];

    $inputJson = json_encode($inputData);

    // Command to execute Python script with model type and input data
    $command = 'export LC_ALL=C.UTF-8 && export LANG=C.UTF-8 && python3 run_model.py ' . $model_type . ' \'' . $inputJson . '\'';
    $output = shell_exec($command);

    if ($output === null || trim($output) === '') {
        echo "<br>Error: No output returned from the Python script.";
    } else {
        $predictions = json_decode($output, true);
        if ($predictions === null) {
            echo "<br>Error: Failed to decode JSON. Raw output was: " . $output;
        } else {
            echo "<hr><br>";
            echo "<b>Prediction: </b>" . $predictions[0];
            // Assuming the plot is saved as 'plot.png' in a directory accessible by your web server
            $plot_path = 'plot.png';

            // Generate the HTML to display the image
            echo "<hr><br>";
            echo "<h2>Prediction Results</h2>";
            echo "<img src='$plot_path' alt='Prediction Plot' style='max-width: 100%; height: auto;'>";
        }
    }
} else {
    echo "Tidak ada data yang dikirim.";
}

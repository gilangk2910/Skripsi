<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $inputCsv = 'upload.csv';
  $outputCsv = 'output.csv';
  $startRow = isset($_POST['startRow']) ? intval($_POST['startRow']) : 0;
  $batchSize = isset($_POST['batchSize']) ? intval($_POST['batchSize']) : 2;
  $hospital_type_override = isset($_POST['hospital_type']) ? $_POST['hospital_type'] : null; // Get the hospital_type from POST request

  // Hapus output.csv jika startRow adalah 0 (batch pertama)
  if ($startRow === 0 && file_exists($outputCsv)) {
    unlink($outputCsv);
  }

  if (($handle = fopen($inputCsv, 'r')) !== FALSE) {
    // Buka atau buat output CSV untuk menulis
    $outputHandle = fopen($outputCsv, $startRow === 0 ? 'w' : 'a');

    // Pada batch pertama, tulis header dengan kolom "Prediction"
    if ($startRow === 0) {
      $header = fgetcsv($handle, 1000, ';');
      $header[] = 'Prediction';
      fputcsv($outputHandle, $header, ';');
    } else {
      // Lewati header jika bukan batch pertama
      fgetcsv($handle, 1000, ';');
    }

    // Skip rows until the start row
    $currentRow = 0;
    while ($currentRow < $startRow && fgetcsv($handle, 1000, ';') !== FALSE) {
      $currentRow++;
    }

    // Process the next batch of rows
    $rowsProcessed = 0;
    while ($rowsProcessed < $batchSize && ($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
      // Determine hospital type from the CSV
      $hospital_type = $data[7];

      // Map frontend values to CSV values
      $csv_hospital_type = ($hospital_type_override == "umum") ? "RUMAH SAKIT UMUM" : "RUMAH SAKIT KHUSUS";

      // Skip the row if it doesn't match the selected hospital type
      if ($hospital_type_override && strcasecmp($hospital_type, $csv_hospital_type) !== 0) {
        $startRow++;
        continue; // Skip this row if it doesn't match the selected type
      }

      // Extract data for the model
      $bed_occupation_rate = $data[8];
      $gross_death_rate = $data[9];
      $net_death_rate = $data[10];
      $bed_turn_over = $data[11];
      $turn_over_interval = $data[12];
      $average_length_of_stay = $data[13];

      // Determine which model to use based on hospital type
      $model_type = ($hospital_type_override == "umum") ? 'rsu' : 'rsk';

      // Prepare data for prediction
      $inputData = [
        [$bed_occupation_rate, $gross_death_rate, $net_death_rate, $bed_turn_over, $turn_over_interval, $average_length_of_stay]
      ];

      $inputJson = json_encode($inputData);

      // Call Python script to get prediction
      $command = 'export LC_ALL=C.UTF-8 && export LANG=C.UTF-8 && python3 run_model.py ' . $model_type . ' \'' . $inputJson . '\'';
      $output = shell_exec($command);

      if ($output !== null && trim($output) !== '') {
        $predictions = json_decode($output, true);
        if ($predictions !== null) {
          $data[] = $predictions[0];
        } else {
          $data[] = 'Error';
        }
      } else {
        $data[] = 'No Output';
      }

      // Write the data to the output CSV
      fputcsv($outputHandle, $data, ';');
      $rowsProcessed++;
      $startRow++;
    }

    fclose($handle);
    fclose($outputHandle);

    $totalRows = count(file($inputCsv)) - 1; // Minus 1 for header
    $progress = min(100, (($startRow) / $totalRows) * 100);

    echo json_encode([
      'status' => 'processing',
      'progress' => $progress,
      'nextStartRow' => $startRow,
    ]);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to open CSV file.']);
  }
}

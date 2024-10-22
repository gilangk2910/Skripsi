<?php
function generate_confusion_matrix($hospitalType)
{
  $confusionMatrixImage = 'confusion_matrix_output.png';

  // Command to run the Python script with the hospital type as an argument
  $command = escapeshellcmd("python3 run_graph.py " . escapeshellarg($hospitalType));
  shell_exec($command);

  // Check if the image was generated successfully
  if (file_exists($confusionMatrixImage)) {
    return ['status' => 'success', 'image' => $confusionMatrixImage];
  } else {
    return ['status' => 'error', 'message' => 'Failed to generate confusion matrix.'];
  }
}

// Get the hospital type from the POST request
$hospitalType = $_POST['hospital_type'] ?? null;

if ($hospitalType) {
  // Generate the confusion matrix and output the result as JSON
  $response = generate_confusion_matrix($hospitalType);
  echo json_encode($response);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Hospital type is required.']);
}

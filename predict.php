<?php
$inputData = [
  [75, 0.1, 0.05, 2, 3, 5],  // Replace this with your actual input data
];

$inputJson = json_encode($inputData);

$command = 'export LC_ALL=C.UTF-8 && export LANG=C.UTF-8 && python3 run_model_rsu.py \'' . $inputJson . '\'';
$output = shell_exec($command);

if ($output === null) {
  echo "Error: Command did not execute correctly.";
} else {
  echo "Raw output: " . $output;
  $predictions = json_decode($output, true);

  if ($predictions === null) {
    echo "Error: JSON decode failed.";
  } else {
    print_r($predictions);
  }
}

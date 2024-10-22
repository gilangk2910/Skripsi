<?php
// Function to display csv as table with specified row limit
function display_result_csv_as_table($filename, $rowCount)
{
  $rowsDisplayed = 0;
  if (($handle = fopen($filename, "r")) !== FALSE) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-striped table-hover">';

    $headerDisplayed = false;
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
      if (!$headerDisplayed) {
        echo '<thead class="thead-light"><tr>';
        foreach ($data as $header) {
          echo '<th>' . htmlspecialchars($header) . '</th>';
        }
        echo '</tr></thead><tbody>';
        $headerDisplayed = true;
      } else {
        if ($rowCount !== 'all' && $rowsDisplayed >= $rowCount) {
          break;
        }
        echo '<tr>';
        foreach ($data as $key => $cell) {
          // Check if it's the last column (Prediction column)
          if ($key === array_key_last($data)) {
            // Map the numeric predictions to descriptive text
            switch ($cell) {
              case '0':
                $cell = 'Kurang Baik';
                break;
              case '1':
                $cell = 'Baik';
                break;
              case '2':
                $cell = 'Sangat Baik';
                break;
            }
          }
          echo '<td>' . htmlspecialchars($cell) . '</td>';
        }
        echo '</tr>';
        $rowsDisplayed++;
      }
    }
    echo '</tbody></table>';
    echo '</div>';
    fclose($handle);
  }
}

// Default file is 'output.csv'
$csvFile = 'output.csv';

if (file_exists($csvFile)) {
  $rowCount = isset($_GET['dataCount']) ? $_GET['dataCount'] : 10;
  display_result_csv_as_table($csvFile, $rowCount);
} else {
  echo "<p>File output.csv tidak ditemukan.</p>";
}

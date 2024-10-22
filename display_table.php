<?php
// Function to display csv as table with specified row limit
function display_csv_as_table($filename, $rowCount)
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
                foreach ($data as $cell) {
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

$rowCount = isset($_GET['dataCount']) ? $_GET['dataCount'] : 10;
display_csv_as_table('upload.csv', $rowCount);
?>

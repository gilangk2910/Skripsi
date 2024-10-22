<?php
if (isset($_POST['submit'])) {
    // Check if the file is uploaded
    if ($_FILES['csvFile']['error'] == UPLOAD_ERR_OK) {
        $tmpName = $_FILES['csvFile']['tmp_name'];
        $csvFileName = 'upload.csv'; // Destination file name

        // Move the uploaded file to the destination
        if (move_uploaded_file($tmpName, $csvFileName)) {
            header('Location: index.php'); // Redirect back to the main page
            exit;
        } else {
            echo 'Failed to upload file.';
        }
    } else {
        echo 'No file uploaded or upload error.';
    }
}
?>

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
            <a href="#" class="nav-link text-white"><i class="material-icons">check_circle</i> Proses</a>
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
      <span>Home</span> &gt; <span>Proses</span>
    </div>
    <div class="content">
      <h2 class="mb-4">Proses</h2>
      <div class="card p-3 mb-4">
        <form method="get" action="proses.php">
          <div class="row">
            <div class="col-md-2">
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
            </div>
            <div class="col-md-10">
              <div class="form-group form-check">
                <input type="radio" class="form-check-input" id="rumah_sakit_umum" name="hospital_type" value="umum">
                <label class="form-check-label" for="rumah_sakit_umum">Rumah Sakit Umum</label>
              </div>
              <div class="form-group form-check">
                <input type="radio" class="form-check-input" id="rumah_sakit_khusus" name="hospital_type" value="khusus">
                <label class="form-check-label" for="rumah_sakit_khusus">Rumah Sakit Khusus</label>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div>
        <button id="processDataButton" class="btn btn-success btn-lg btn-block">
          <span id="processDataButtonText">Proses Data</span>
          <span id="processDataButtonLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
        </button>

        <div class="col-md-12 mb-4">
          <div class="progress">
            <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
          </div>
        </div>

        <div id="processStatus" class="col-md-12 mt-4"></div>

        <?php
        // Initialize counts
        $kurang_baik_count = 0;
        $baik_count = 0;
        $sangat_baik_count = 0;

        if (($handle = fopen('output.csv', "r")) !== FALSE) {
          // Skip the header row
          fgetcsv($handle, 1000, ";");

          // Loop through the file to count the predictions
          while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            switch ($data[14]) { // Assuming the "Prediction" column is the 14th column (index 13)
              case '0':
                $kurang_baik_count++;
                break;
              case '1':
                $baik_count++;
                break;
              case '2':
                $sangat_baik_count++;
                break;
            }
          }

          fclose($handle);
        }
        ?>

        <?php if (file_exists('output.csv')): ?>
          <div class="row">
            <div class="col-md-4">
              <div class="card text-white bg-danger text-center mb-3">
                <div class="card-body">
                  <h5 class="card-title">Total Kurang Baik</h5>
                  <h2 class="card-text"><?= $kurang_baik_count ?></h2>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card text-white bg-primary text-center mb-3">
                <div class="card-body">
                  <h5 class="card-title">Total Baik</h5>
                  <h2 class="card-text"><?= $baik_count ?></h2>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card text-white bg-success text-center mb-3">
                <div class="card-body">
                  <h5 class="card-title">Total Sangat Baik</h5>
                  <h2 class="card-text"><?= $sangat_baik_count ?></h2>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>


        <div class="col-md-12">
          <?php
          // Include and call the function to display result table data if output.csv exists
          if (file_exists('output.csv')) {
            include 'display_result_table.php';
          } else {
            echo "<p>File output.csv tidak ditemukan. Silakan proses data terlebih dahulu.</p>";
          }

          echo '<div class="row">';
          // Check if the classification report image exists and display it
          if (file_exists('classification_report_output.png')) {
            echo "<div class='col-md-6'>";
            echo "<h4>Classification Report:</h4>";
            echo "<img src='classification_report_output.png' alt='Classification Report' style='width: 100%; height: auto;'>";
            echo "</div>";
          }

          // Check if the confusion matrix image exists and display it
          if (file_exists('confusion_matrix_output.png')) {
            echo "<div class='col-md-6'>";
            echo "<h4>Confusion Matrix:</h4>";
            echo "<img src='confusion_matrix_output.png' alt='Confusion Matrix' style='width: 100%; height: auto;'>";
            echo "</div>";
          }
          echo '</div>';
          ?>
        </div>
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
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    $(document).ready(function() {
      let processing = false;

      $('#processDataButton').click(function() {
        Swal.fire({
          title: 'Apakah kamu yakin?',
          text: "Proses ini akan memakan waktu beberapa saat!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya, proses data!'
        }).then((result) => {
          if (result.isConfirmed) {
            // Get the selected hospital type
            var hospitalType = $('input[name="hospital_type"]:checked').val();

            // Disable the button and show loading animation
            $('#processDataButton').prop('disabled', true);
            $('#processDataButtonText').text('Memproses...');
            $('#processDataButtonLoading').show();

            $('#progressBar').css('width', '0%').attr('aria-valuenow', 0).text('0%');
            $('#processStatus').text('');

            processing = true; // Set processing state

            var startRow = 0;
            var batchSize = 2; // Set your batch size here

            function processBatch() {
              $.ajax({
                url: 'process_data.php',
                type: 'POST',
                dataType: 'json',
                data: {
                  startRow: startRow,
                  batchSize: batchSize,
                  hospital_type: hospitalType // Pass the hospital type to the server
                },
                success: function(response) {
                  if (response.status === 'processing') {
                    var progress = response.progress;
                    startRow = response.nextStartRow;

                    $('#progressBar').css('width', progress + '%').attr('aria-valuenow', progress).text(Math.round(progress) + '%');

                    if (progress < 100) {
                      processBatch(); // Continue processing the next batch
                    } else {
                      $('#processStatus').text('Proses selesai! Data telah disimpan ke file output.csv.');
                      Swal.fire(
                        'Selesai!',
                        'Data telah berhasil diproses.',
                        'success'
                      ).then(() => {
                        // Trigger additional action after successful completion
                        afterProcessSuccess();
                      });

                      // Enable the button and reset text
                      $('#processDataButton').prop('disabled', false);
                      $('#processDataButtonText').text('Proses Data');
                      $('#processDataButtonLoading').hide();

                      processing = false; // Reset processing state
                    }
                  } else {
                    handleProcessError();
                  }
                },
                error: handleProcessError
              });
            }

            function handleProcessError() {
              $('#processStatus').text('Terjadi kesalahan selama proses.');
              Swal.fire(
                'Kesalahan!',
                'Terjadi kesalahan selama proses.',
                'error'
              );
              // Enable the button and reset text
              $('#processDataButton').prop('disabled', false);
              $('#processDataButtonText').text('Proses Data');
              $('#processDataButtonLoading').hide();

              processing = false; // Reset processing state
            }

            function afterProcessSuccess() {
              // Show a loading state
              $('#processStatus').text('Menghasilkan Confusion Matrix...');
              Swal.fire({
                title: 'Menghasilkan Confusion Matrix',
                text: "Silakan tunggu sebentar...",
                allowOutsideClick: false,
                didOpen: () => {
                  Swal.showLoading();
                }
              });

              $.ajax({
                url: 'generate_confusion_matrix.php',
                type: 'POST',
                dataType: 'json',
                data: {
                  hospital_type: hospitalType // Pass the hospital type to the server
                },
                success: function(response) {
                  if (response.status === 'success') {
                    Swal.close(); // Close the loading state
                    // Reload the page to display the updated images
                    location.reload();
                  } else {
                    $('#processStatus').text('Gagal menghasilkan Confusion Matrix.');
                    Swal.fire(
                      'Kesalahan!',
                      'Gagal menghasilkan Confusion Matrix.',
                      'error'
                    );
                  }
                },
                error: function() {
                  $('#processStatus').text('Gagal melakukan proses.');
                  Swal.fire(
                    'Kesalahan!',
                    'Gagal melakukan proses.',
                    'error'
                  );
                }
              });
            }

            processBatch(); // Start the first batch
          }
        });
      });

      // Confirm on tab close if processing
      window.addEventListener('beforeunload', function(e) {
        if (processing) {
          var confirmationMessage = 'Proses masih berlangsung. Apakah kamu yakin ingin meninggalkan halaman ini?';
          (e || window.event).returnValue = confirmationMessage; // For most browsers
          return confirmationMessage; // For some older browsers
        }
      });
    });
  </script>
</body>

</html>
<script src="showloading.js"></script>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999; justify-content: center; align-items: center; flex-direction: column;">
    <img src="images/Animation - 1732178074480.gif" alt="Loading..." style="width: 200px; height: 200px;">
    <p style="margin-top: 20px; font-size: 18px;">Loading...</p>
</div>

<?php
session_start();
if (!isset($_SESSION['vetusername'])) {
    header('Location: vet_login.php'); // Redirect if not logged in
    exit();
}

$vetusername = $_SESSION['vetusername'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCMS Appointment Report</title>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include jsPDF and html2canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>


    <style>
        body {
            font-family: Itim, cursive;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        .report-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        h2 {
            color: #2c3e50;
            text-align: center;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            font-size:15px;
            padding-top:20px;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .status-card {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .chart-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .chart-container canvas {
            width: 48% !important;
            height: 300px !important;
        }

        #download-pdf {
            display: block;
            width: auto;
            margin: 20px auto;
            padding: 10px;
            background-color: #5b6e54;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .filter-section {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 20px;
    border: 1px solid #e9ecef;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100px; /* Fixed height */
    overflow: hidden; /* Prevent content from causing overflow */
}

.filter-section form {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%; /* Full width of the container */
    gap: 15px; /* Space between form elements */
}

.filter-section div {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.filter-section label {
    margin-bottom: 5px;
    font-size: 0.9em;
}

.filter-section select {
    width: 150px; /* Fixed width for selects */
    padding: 8px;
    margin: 0;
}

.filter-section button {
    padding: 8px 15px;
}

.filter-display {
    text-align: center;
    background-color: #e9ecef;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
}

.filter-section form {
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }

        .filter-section div {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 10px;
        }

        .filter-section input[type="date"] {
            width: 150px;
            padding: 6px;
            margin: 5px 0;
        }

        .filter-section div {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 10px;
        }

        .filter-section input[type="date"] {
            width: 150px;
            padding: 6px;
            margin: 5px 0;
        }

    </style>
</head>
<body>
<article>
<div class="report-container" id="report-container">
        <h1>Veterinary Clinic Management
        System<br>Appointment Report</h1>

        <?php
        // Database connection (replace with your own credentials)
        $connect = mysqli_connect("localhost", "vet_system_db", "VsDB123$", "vet_database");

        if (!$connect) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Fetch distinct vetids from schedule table for the filter
        $vetResult = mysqli_query($connect, "SELECT DISTINCT vetid FROM schedule");
        
        // Default filter values
        $vetid_filter = isset($_POST['vetid']) ? $_POST['vetid'] : '';
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

        // Server-side date validation
        if (!empty($start_date) && !empty($end_date)) {
            // Ensure end date is not less than start date
            if (strtotime($end_date) < strtotime($start_date)) {
                // Swap dates if end date is less than start date
                $temp = $start_date;
                $start_date = $end_date;
                $end_date = $temp;
            }
        }

        // Query construction
        $query = "SELECT * FROM schedule WHERE 1=1";

        // Add Vet ID filter
        if ($vetid_filter != '') {
            $query .= " AND vetid = '$vetid_filter'";
        }

        // Add Date Range filter
        if (!empty($start_date) && !empty($end_date)) {
            $query .= " AND DATE(datetime) BETWEEN '$start_date' AND '$end_date'";
        }

        // Update filter display
        $filterDisplay = [];
        if ($vetid_filter) {
            $filterDisplay[] = "Vet ID: $vetid_filter";
        }
        if (!empty($start_date) && !empty($end_date)) {
            $filterDisplay[] = "Date: $start_date to $end_date";
        }
        $filterDisplay = $filterDisplay ? implode(", ", $filterDisplay) : "All Vets, All Dates";

        $result = mysqli_query($connect, $query);

        // Initialize variables to hold counts
        $totalAppointments = mysqli_num_rows($result);
        $scheduled = 0;
        $occupiedSlots = 0;
        $availableSlots = 0;
        $overdueSlots = 0;

        // Get current date for overdue checks
        $currentDate = date('Y-m-d');

        // Process the data
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['scheduledstatus'] == 'Occupied') $occupiedSlots++;
            if ($row['scheduledstatus'] == 'Available') $availableSlots++;
            if ($row['scheduledstatus'] == 'Overdue' && $row['datetime'] < $currentDate) $overdueSlots++;
        }

        // Calculate percentages for each status type
        $occupiedPercentage = $totalAppointments > 0 ? ($occupiedSlots / $totalAppointments) * 100 : 0;
        $availablePercentage = $totalAppointments > 0 ? ($availableSlots / $totalAppointments) * 100 : 0;
        $overduePercentage = $totalAppointments > 0 ? ($overdueSlots / $totalAppointments) * 100 : 0;
        ?>

        <!-- Filter form -->
        <div class="filter-section">
            <form method="POST" id="filterForm">
                <div>
                    <label for="vetid">Select Vet ID:</label>
                    <select name="vetid" id="vetid">
                        <option value="">All Vets</option>
                        <?php
                        mysqli_data_seek($vetResult, 0);
                        while ($vet = mysqli_fetch_assoc($vetResult)) {
                            $selected = ($vet['vetid'] == $vetid_filter) ? 'selected' : '';
                            echo "<option value='{$vet['vetid']}' $selected> {$vet['vetid']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="start_date">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" 
                           value="<?php echo $start_date; ?>">
                </div>
                <div>
                    <label for="end_date">End Date:</label>
                    <input type="date" name="end_date" id="end_date" 
                           value="<?php echo $end_date; ?>">
                </div>
                <button type="submit" onclick="return validateCheckboxes()">Apply Filter</button>
            </form>
        </div>

        <div class="filter-display">
            <p><?php echo $filterDisplay; ?></p>
        </div>

        <h2>Data Summary: Total Appointments: <?php echo $totalAppointments; ?></h2>

        <div class="status-grid">
            <div class="status-card">
                <strong>Occupied Slots:</strong> <?php echo $occupiedSlots; ?> (<?php echo number_format($occupiedPercentage, 2); ?>%)
            </div>
            <div class="status-card">
                <strong>Available Slots:</strong> <?php echo $availableSlots; ?> (<?php echo number_format($availablePercentage, 2); ?>%)
            </div>
            <div class="status-card">
                <strong>Overdue Slots:</strong> <?php echo $overdueSlots; ?> (<?php echo number_format($overduePercentage, 2); ?>%)
            </div>
        </div>

        <div class="chart-container">
            <canvas id="appointmentChart"></canvas>
            <canvas id="slotTypeChart"></canvas>
        </div>

        <h2>End of the report</h2>





        <button id="download-pdf" onclick="return validateCheckboxes()">Download PDF Report</button>





      
        <script>

 // Date Range Validation
 document.addEventListener('DOMContentLoaded', function() {
                var startDateInput = document.getElementById('start_date');
                var endDateInput = document.getElementById('end_date');
                var filterForm = document.getElementById('filterForm');

                // Set min attribute for end date based on start date
                startDateInput.addEventListener('change', function() {
                    // Set the minimum value of end date to be the start date
                    endDateInput.min = this.value;
                    
                    // If end date is less than start date, reset end date
                    if (endDateInput.value && endDateInput.value < this.value) {
                        endDateInput.value = this.value;
                    }
                });

                // Additional check on end date change
                endDateInput.addEventListener('change', function() {
                    if (startDateInput.value && this.value < startDateInput.value) {
                        this.value = startDateInput.value;
                    }
                });

                // Form submission validation
                filterForm.addEventListener('submit', function(e) {
                    var startDate = startDateInput.value;
                    var endDate = endDateInput.value;

                    if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
                        e.preventDefault(); // Prevent form submission
                        alert('End date cannot be less than start date. Dates have been adjusted.');
                        
                        // Automatically adjust dates
                        endDateInput.value = startDate;
                    }
                });
            });

            // Chart for Appointments Status
            var ctxAppointment = document.getElementById('appointmentChart').getContext('2d');
            var appointmentChart = new Chart(ctxAppointment, {
                type: 'bar',
                data: {
                    labels: [ 'Occupied', 'Available', 'Overdue'],
                    datasets: [{
                        label: 'Number of Appointments',
                        data: [<?php echo $occupiedSlots; ?>, <?php echo $availableSlots; ?>, <?php echo $overdueSlots; ?>],
                        backgroundColor: ['green', 'orange', 'red'],
                        borderColor: ['green', 'orange', 'red'],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Chart for Slot Types (in percentage)
            var ctxSlotType = document.getElementById('slotTypeChart').getContext('2d');
var slotTypeChart = new Chart(ctxSlotType, {
    type: 'bar',
    data: {
        labels: ['Occupied', 'Available', 'Overdue'],
        datasets: [{
            label: 'Slot Types (in percentage)',
            data: [ <?php echo $occupiedPercentage; ?>, <?php echo $availablePercentage; ?>, <?php echo $overduePercentage; ?>],
            backgroundColor: ['green', 'orange', 'red'],
            borderColor: ['green', 'orange', 'red'],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toFixed(2) + '%';
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.parsed.y.toFixed(2) + '%';
                    }
                }
            }
        }
    }
});



            // Download PDF functionality
            document.getElementById('download-pdf').addEventListener('click', function() {
    const { jsPDF } = window.jspdf;

    const pdf = new jsPDF({
        orientation: 'portrait',
        unit: 'mm',
        format: 'a4'
    });

    // Function to add page numbers
    const addPageNumbers = (pdf) => {
        const pageCount = pdf.getNumberOfPages();
        pdf.setFont("helvetica", "normal");
        pdf.setFontSize(10);
        for (let i = 1; i <= pageCount; i++) {
            pdf.setPage(i);
            pdf.text(`Page ${i} of ${pageCount}`, 200, 10, { align: "right" });
            pdf.text(`VCMS Appointment Report - `+new Date().toLocaleString(), 200, 15, { align: "right" });
        }
    };

    // Add a title page
    pdf.setFont("helvetica", "bold");
    pdf.setFontSize(22);
    pdf.text("Vet Appointment Report ", 105, 40, { align: "center" });
    pdf.setFontSize(18);
    pdf.text("(Filter Applied: " + "<?php echo $filterDisplay; ?>" + ")", 105, 50, { align: "center" });
    pdf.setFont("helvetica", "normal");
    pdf.setFontSize(10);
    // pdf.text("Generated on: " + new Date().toLocaleString(), 105, 60, { align: "center" });
    pdf.addPage();


//     // Inside the PDF generation, add this before creating the title page
//     pdf.setFont("helvetica", "bold");
//     pdf.setFontSize(12);
// pdf.text("Filter Applied: " + "<?php echo $filterDisplay; ?>", 105, 55, { align: "center" });
// pdf.addPage();

pdf.setFont("helvetica", "normal");
pdf.setFontSize(14);
    // Create and insert the full appointments table
    pdf.text("Detailed Appointments Data", 10, 20);
    const appointmentsData = [
        ["Vet ID", "Date Time", "Scheduled Status"],
        <?php 
        // Reset the result pointer
        mysqli_data_seek($result, 0);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "['" . 
                addslashes($row['vetid']) . "', '" . 
                addslashes($row['datetime']) . "', '" . 
            
                addslashes($row['scheduledstatus']) . "'],";
        }
        ?>
    ];
    
    pdf.autoTable({
        head: [["Vet ID", "Date Time",  "Scheduled Status"]],
        body: appointmentsData.slice(1),
        startY: 30,
        styles: { fontSize: 10, cellPadding: 2 },
        columnStyles: { 
            0: { cellWidth: 60 },
            1: { cellWidth: 60 },
            2: { cellWidth: 60 },
           
        }
    });

    pdf.addPage();


    // Create and insert the summary table
    pdf.text("Appointment Summary", 10, 20);
    const summaryData = [
        ["Category", "Count", "Percentage"],
        ["Occupied Slots", "<?php echo $occupiedSlots; ?>", "<?php echo number_format($occupiedPercentage, 2); ?>%"],
        ["Available Slots", "<?php echo $availableSlots; ?>", "<?php echo number_format($availablePercentage, 2); ?>%"],
        ["Overdue Slots", "<?php echo $overdueSlots; ?>", "<?php echo number_format($overduePercentage, 2); ?>%"]
    ];
    
    pdf.autoTable({
        head: [["Category", "Count", "Percentage"]],
        body: summaryData.slice(1),
        startY: 25,
        theme: "grid",
        styles: { fontSize: 10 }
    });

    pdf.addPage();

    // Generate charts as images and add them
    pdf.text("Appointment Charts", 10, 20);
    html2canvas(document.getElementById("appointmentChart"), { scale: 2, useCORS: true }).then(function (appointmentChartCanvas) {
        const imgData1 = appointmentChartCanvas.toDataURL("image/png");
        pdf.addImage(imgData1, "PNG", 15, 30, 180, 100);

        html2canvas(document.getElementById("slotTypeChart"), { scale: 2, useCORS: true }).then(function (slotTypeChartCanvas) {
            const imgData2 = slotTypeChartCanvas.toDataURL("image/png");
            pdf.addImage(imgData2, "PNG", 15, 150, 180, 100);

            // Add an ending page
            pdf.addPage();
            pdf.setFontSize(18);
            pdf.text("--End of Report--", 105, 150, { align: "center" });

            // Add page numbers
            addPageNumbers(pdf);

            // Save the PDF
            pdf.save("VCMS_Appointment_Report.pdf");
        });
    });
});



        </script>
    </div>
</article>
</body>
</html>

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

// Database connection (make sure to replace with your own credentials)
$connect = mysqli_connect("localhost", "vet_system_db", "VsDB123$", "vet_database");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch distinct values for the filters
$animalTypesQuery = "SELECT DISTINCT pettype FROM symptoms";
$symptomStatusesQuery = "SELECT DISTINCT symptomstatus FROM symptoms";
$symptomNamesQuery = "SELECT DISTINCT symptomname FROM symptoms";  // DISTINCT to get unique symptoms

$animalTypesResult = mysqli_query($connect, $animalTypesQuery);
$symptomStatusesResult = mysqli_query($connect, $symptomStatusesQuery);
$symptomNamesResult = mysqli_query($connect, $symptomNamesQuery);

// Initialize filter conditions
$filterConditions = [];

// date filter
if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $startDate = mysqli_real_escape_string($connect, $_GET['start_date']);
    $endDate = mysqli_real_escape_string($connect, $_GET['end_date']);
    
   
    $filterConditions[] = "DATE(date) BETWEEN '$startDate' AND '$endDate'";
}



if (!empty($_GET['animal_type'])) {
    $animalType = mysqli_real_escape_string($connect, $_GET['animal_type']);
    $filterConditions[] = "pettype = '$animalType'";
}

if (!empty($_GET['symptom_status'])) {
    $symptomStatus = mysqli_real_escape_string($connect, $_GET['symptom_status']);
    $filterConditions[] = "symptomstatus = '$symptomStatus'";
}

if (!empty($_GET['symptom_name'])) {
    $symptomName = mysqli_real_escape_string($connect, $_GET['symptom_name']);
    $filterConditions[] = "symptomname = '$symptomName'";
}

// Combine all filter conditions
$whereClause = count($filterConditions) > 0 ? "WHERE " . implode(" AND ", $filterConditions) : "";

try {
    // Fetch all symptoms based on filter
    $result = mysqli_query($connect, "SELECT pettype, symptomname, symptomstatus FROM symptoms $whereClause");

    // Initialize variables to hold counts
    $totalSymptoms = mysqli_num_rows($result);
    $mildStatus = 0;
    $criticalStatus = 0;
    $numCoughing = 0;
    $numVomiting = 0;
    $numDiarrhea = 0;
    $numItching = 0;
    $numDogs = 0;
    $numCats = 0;

    // Process the data
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['symptomstatus'] == 'Mild') $mildStatus++;
        if ($row['symptomstatus'] == 'Critical') $criticalStatus++;

        // Count occurrences of each symptom by checking for partial matches
        if (strpos($row['symptomname'], 'Coughing') !== false) $numCoughing++;
        if (strpos($row['symptomname'], 'Vomiting') !== false) $numVomiting++;
        if (strpos($row['symptomname'], 'Diarrhea') !== false) $numDiarrhea++;
        if (strpos($row['symptomname'], 'Itchiness') !== false) $numItching++;

        if ($row['pettype'] == 'Dog') $numDogs++;
        if ($row['pettype'] == 'Cat') $numCats++;
    }

    // Calculate percentages only if there are symptoms
    $mildPercentage = ($totalSymptoms > 0) ? ($mildStatus / $totalSymptoms) * 100 : 0;
    $criticalPercentage = ($totalSymptoms > 0) ? ($criticalStatus / $totalSymptoms) * 100 : 0;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCMS Pet Symptoms Report</title>

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

.applied-filters {
    text-align: center;
    background-color: #e9ecef;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
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
        <h1>Veterinary Clinic Management System<br>Pet Symptoms Report</h1>

        <div class="filter-section">
            <form method="GET" action="">
                <div>
                    <label for="animal_type">Animal Type:</label>
                    <select id="animal_type" name="animal_type">
                        <option value="">All</option>
                        <?php while ($row = mysqli_fetch_assoc($animalTypesResult)) { ?>
                            <option value="<?php echo $row['pettype']; ?>" <?php if (isset($_GET['animal_type']) && $_GET['animal_type'] === $row['pettype']) echo 'selected'; ?>>
                                <?php echo $row['pettype']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label for="symptom_status">Symptom Status:</label>
                    <select id="symptom_status" name="symptom_status">
                        <option value="">All</option>
                        <?php while ($row = mysqli_fetch_assoc($symptomStatusesResult)) { ?>
                            <option value="<?php echo $row['symptomstatus']; ?>" <?php if (isset($_GET['symptom_status']) && $_GET['symptom_status'] === $row['symptomstatus']) echo 'selected'; ?>>
                                <?php echo $row['symptomstatus']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
               
                
                <div>
    <label for="symptom_name">Symptom Name:</label>
    <select id="symptom_name" name="symptom_name">
        <option value="">All</option>
        <?php 
        // Initialize an array to track the displayed symptoms
        $displayedSymptoms = [];

        while ($row = mysqli_fetch_assoc($symptomNamesResult)) {
            // Split the symptom names stored in the database by comma
            $symptomNamesArray = explode(',', $row['symptomname']);
            
            // Iterate through the split symptom names and display them one by one
            foreach ($symptomNamesArray as $symptom) {
                $symptom = trim($symptom); // Remove any extra spaces
                
                // Check if the symptom has already been displayed
                if (!in_array($symptom, $displayedSymptoms)) {
                    // Add the symptom to the array of displayed symptoms
                    $displayedSymptoms[] = $symptom;
                    ?>
                    <option value="<?php echo $symptom; ?>" <?php if (isset($_GET['symptom_name']) && $_GET['symptom_name'] === $symptom) echo 'selected'; ?>>
                        <?php echo $symptom; ?>
                    </option>
                    <?php
                }
            }
        }
        ?>
    </select>
</div>

<div>
    <label for="start_date">Start Date:</label>
    <input type="date" id="start_date" name="start_date" 
           value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>">
</div>
<div>
    <label for="end_date">End Date:</label>
    <input type="date" id="end_date" name="end_date" 
           value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>">
</div>



                <button type="submit" onclick="return validateCheckboxes()">Apply Filter</button>
            </form>
        </div>



        <div class="applied-filters">
    <p>
        <?php 

if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $startDate = mysqli_real_escape_string($connect, $_GET['start_date']);
    $endDate = mysqli_real_escape_string($connect, $_GET['end_date']);
    $filterConditions[] = "date BETWEEN '$startDate' AND '$endDate'";
}

        $appliedFilters = [];
        if (!empty($_GET['animal_type'])) $appliedFilters[] = "Animal Type: " . htmlspecialchars($_GET['animal_type']);
        if (!empty($_GET['symptom_status'])) $appliedFilters[] = "Symptom Status: " . htmlspecialchars($_GET['symptom_status']);
        if (!empty($_GET['symptom_name'])) $appliedFilters[] = "Symptom Name: " . htmlspecialchars($_GET['symptom_name']);
        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) 
    $appliedFilters[] = "Date Range: " . htmlspecialchars($_GET['start_date']) . " to " . htmlspecialchars($_GET['end_date']);
        
        echo !empty($appliedFilters) ? "Applied Filters: " . implode(" | ", $appliedFilters) : "No filters applied";

        // Add Date Range filter with validation
if (!empty($start_date) && !empty($end_date)) {
    // Ensure end date is not less than start date
    if (strtotime($end_date) < strtotime($start_date)) {
        // If end date is less than start date, swap the dates
        $temp = $start_date;
        $start_date = $end_date;
        $end_date = $temp;
    }
    $query .= " AND DATE(datetime) BETWEEN '$start_date' AND '$end_date'";
}
        ?>
    </p>
</div>

        <h2>Data Summary: Total Symptoms: <?php echo $totalSymptoms; ?></h2>

        <div class="status-grid">
            <div class="status-card">
            <strong>Critical Symptoms:</strong><?php echo $criticalStatus; ?> (<?php echo number_format($criticalPercentage, 2); ?>%)
            </div>
            <div class="status-card">
            <strong>Mild Symptoms: </strong>
                <?php echo $mildStatus; ?> (<?php echo number_format($mildPercentage, 2); ?>%)
            </div>
            <div class="status-card">
            <strong>Coughing Symptoms: </strong>
                <?php echo $numCoughing; ?>
            </div>

            <div class="status-card">
            <strong>Vomiting Symptoms: </strong>
                <?php echo $numVomiting; ?>
            </div>

            <div class="status-card">
            <strong>Diarrhea Symptoms: </strong>
                <?php echo $numDiarrhea; ?>
            </div>


            <div class="status-card">
            <strong>Itchiness Symptoms: </strong>
                <?php echo $numItching; ?>
            </div>

        </div>

        <div class="chart-container">
            <canvas id="statusChart"></canvas>
            <canvas id="symptomChart"></canvas>
        </div>

           <h2>End of the report</h2>
        <button id="download-pdf" onclick="return validateCheckboxes()">Download PDF</button>
    </div>
</article>


<script>
document.addEventListener('DOMContentLoaded', function() {
    var startDateInput = document.getElementById('start_date');
    var endDateInput = document.getElementById('end_date');

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
});

// Chart.js code for generating the charts
const ctx1 = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: ['Critical Symptoms', 'Mild Symptoms'],
        datasets: [{
            label: 'Number of Symptoms',
            data: [<?php echo $criticalStatus; ?>, <?php echo $mildStatus; ?>],
            backgroundColor: ['#e74c3c', '#2ecc71'],
            borderColor: ['#c0392b', '#27ae60'],
            borderWidth: 1
        }]
    }
});

const ctx2 = document.getElementById('symptomChart').getContext('2d');
const symptomChart = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: ['Coughing', 'Vomiting', 'Diarrhea', 'Itchiness'],
        datasets: [{
            label: 'Symptoms Name',
            data: [<?php echo $numCoughing; ?>, <?php echo $numVomiting; ?>, <?php echo $numDiarrhea; ?>, <?php echo $numItching; ?>],
            backgroundColor: ['#3498db', '#f39c12', '#9b59b6', '#1abc9c']
        }]
    }
});

// PDF Generation
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
            pdf.text(`VCMS Pet Symptoms Report - `+new Date().toLocaleString(), 200, 15, { align: "right" });
        }
    };

  

    // Add a title page
    pdf.setFont("helvetica", "bold");
    pdf.setFontSize(22);
    pdf.text("Pet Symptoms Report", 105, 40, { align: "center" });
    pdf.setFontSize(10);
    pdf.text("(Applied Filters:", 105, 50, { align: "center" });
pdf.text("<?php 
    $appliedFilters = [];
    if (!empty($_GET['animal_type'])) $appliedFilters[] = "Animal Type: " . htmlspecialchars($_GET['animal_type']);
    if (!empty($_GET['symptom_status'])) $appliedFilters[] = "Symptom Status: " . htmlspecialchars($_GET['symptom_status']);
    if (!empty($_GET['symptom_name'])) $appliedFilters[] = "Symptom Name: " . htmlspecialchars($_GET['symptom_name']);
    if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) 
    $appliedFilters[] = "Date Range: " . htmlspecialchars($_GET['start_date']) . " to " . htmlspecialchars($_GET['end_date']);
    echo !empty($appliedFilters) ? implode(" | ", $appliedFilters) : "No filters applied";
?>", 105,55, { align: "center" });




pdf.addPage();



  
    


// Create and insert the table
pdf.setFont("helvetica", "normal");
pdf.setFontSize(14);
pdf.text("Detailed Pet Symptoms Data", 10, 20);




const symptomsData = [
        ['Pet Type', 'Symptom Name', 'Symptom Status'],
        <?php 
            mysqli_data_seek($result, 0); // Reset the result pointer
            while ($row = mysqli_fetch_assoc($result)) {
                echo "['" . $row['pettype'] . "', '" . $row['symptomname'] . "', '" . $row['symptomstatus'] . "'],";
            }
        ?>
    ];
    
    pdf.autoTable({
        head: symptomsData.slice(0, 1),
        body: symptomsData.slice(1),
        startY: 25, // Start drawing the table at Y position 100
    });

    pdf.addPage();

    
    // Add a data table
    pdf.setFontSize(14);
    pdf.text("Data Summary", 10, 20);
    const data = [
        ["Category", "Count", "Percentage"],
        ["Critical Symptoms", "<?php echo $criticalStatus; ?>", "<?php echo number_format($criticalPercentage, 2); ?>%"],
        ["Mild Symptoms", "<?php echo $mildStatus; ?>", "<?php echo number_format($mildPercentage, 2); ?>%"],
        ["Coughing", "<?php echo $numCoughing; ?>", "-"],
        ["Vomiting", "<?php echo $numVomiting; ?>", "-"],
        ["Diarrhea", "<?php echo $numDiarrhea; ?>", "-"],
        ["Itchiness", "<?php echo $numItching; ?>", "-"]
    ];
    pdf.autoTable({
        head: [["Category", "Count", "Percentage"]],
        body: data.slice(1),
        startY: 25,
        theme: "grid",
        styles: { fontSize: 10 }
    });

    pdf.addPage();
    

    // Generate charts as images and add them
    pdf.text("Data Graph", 10, 20);
    html2canvas(document.getElementById("statusChart"), { scale: 2, useCORS: true }).then(function (statusChartCanvas) {
        const imgData1 = statusChartCanvas.toDataURL("image/png");
        pdf.addImage(imgData1, "PNG", 15, 30, 180, 100);

        html2canvas(document.getElementById("symptomChart"), { scale: 2, useCORS: true }).then(function (symptomChartCanvas) {
            const imgData2 = symptomChartCanvas.toDataURL("image/png");
            pdf.addImage(imgData2, "PNG", 15, 150, 180, 100);

            // Add an ending page
            pdf.addPage();
            pdf.setFontSize(18);
            pdf.text("--End of Report--", 105, 150, { align: "center" });

            // Add page numbers
            addPageNumbers(pdf);

            // Save the PDF
            pdf.save("VCMS_Pet_Symptoms_Report.pdf");
        });
    });
});



</script>

</body>
</html>

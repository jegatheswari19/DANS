<?php
require '../../vendor/autoload.php';
use Dompdf\Dompdf;

include('../../db_con.php');
 // Adjust the path as per your file structure

session_start();

if (!isset($_SESSION['uid'])) {
    die("No registration number found in session.");
}

$RegNo = $_SESSION['uid'];

// Retrieve data from the database
$sql = "SELECT Name, Sem, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, Department FROM oec_choices WHERE RegNo = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('s', $RegNo);
$stmt->execute();
$result = $stmt->get_result();

// Collect rows and track non-empty columns
$rows = [];
$nonEmptyColumns = ['1' => false, '2' => false, '3' => false, '4' => false, '5' => false, '6' => false, '7' => false, '8' => false];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
    foreach ($nonEmptyColumns as $column => &$isNonEmpty) {
        if (!empty($row[$column])) {
            $isNonEmpty = true;
        }
    }
}

$con->close();

// Generate HTML content
$html = '<html><head><style>
body {
    font-family: "Helvetica Neue", Arial, sans-serif;
    text-align: center;
    background-color: #f0f0f0;
    margin: 0;
    padding: 20px;
}
h1 {
    color: #003366;
    text-transform: capitalize;
}
h2 {
    color: #005b96;
    text-transform: capitalize;
}
.logo {
    text-align: center;
    margin: 20px;
}
.table-container {
    margin: 20px auto;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    padding: 20px;
    width: 60%; /* Adjusted width */
    max-width: 100%; /* Ensure table width does not exceed container */
    overflow-x: auto; /* Allow horizontal scrolling if needed */
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    border: 1px solid #cccccc;
    padding: 4px; /* Adjusted padding */
    text-align: left;
    text-transform: capitalize;
    font-size: 14px; /* Adjust font size */
}
th {
    
    color: black;
}
td {
    max-width: 150px; /* Limit cell width to prevent excessive expansion */
    white-space: nowrap; /* Prevent text wrapping */
    overflow: hidden;
    text-overflow: ellipsis; /* Show ellipsis for overflow text */
    background-color: transparent; /* Remove background color */
}
button {
    margin: 20px auto;
    display: block;
    padding: 10px 20px;
    background-color: #d9534f;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    text-transform: capitalize;
}
button:hover {
    background-color: #c9302c;
}
</style></head><body>';
$html .= '<div class="logo"><img src="../../images/ptu-logo.png" alt="PTU Logo" width="100"></div>';
$html .= '<h1>Puducherry Technological University</h1>';
$html .= '<h2>OEC Preferences</h2>';

// Display individual columns if they are not empty
if (!empty($rows[0]['Name'])) {
    $html .= '<p><strong>Name: </strong>' . htmlspecialchars($rows[0]['Name']) . '</p>';
}
if (!empty($rows[0]['Sem'])) {
    $html .= '<p><strong>Sem: </strong>' . htmlspecialchars($rows[0]['Sem']) . '</p>';
}
if (!empty($rows[0]['Department'])) {
    $html .= '<p><strong>Department: </strong>' . htmlspecialchars($rows[0]['Department']) . '</p>';
}

// Display choices in a vertical table
$html .= '<div class="table-container">';
$html .= '<table>';
$html .= '<tr><th>Choice No</th><th>Preference Course</th></tr>';
foreach ($nonEmptyColumns as $column => $isNonEmpty) {
    if ($isNonEmpty) {
        $html .= '<tr>';
        $html .= '<th>' . $column . '</th>';
        $html .= '<td>';
        foreach ($rows as $row) {
            if (!empty($row[$column])) {
                $html .= htmlspecialchars($row[$column]) . '<br>';
            }
        }
        $html .= '</td>';
        $html .= '</tr>';
    }
}
$html .= '</table>';
$html .= '</div>';

$html .= '</body></html>';

$displayHtml = $html . '<button onclick="window.location.href=\'?download=pdf\'">Save PDF</button>';

// Check if PDF download is requested
if (isset($_GET['download']) && $_GET['download'] == 'pdf') {
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream('oec_choices.pdf');
    exit();
}

// Display HTML content
echo $displayHtml;
?>



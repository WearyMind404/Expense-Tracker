<?php
include "includes/functions.php";
include "database.php";
session_start();
if (!isset($_SESSION["id"])) {
    echo "<script>window.open('index.php?mes=Access Denied..','_self');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" type="text/css" href="css/dashboard.css">
</head>

<body>
    <header class="navigation-bar">
        <nav>
            <h1>Expense tracker</h1>
            <ul>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <h3 class="welcome-text" style="text-align: center; color: #333; font-size: 24px;">Hi, <?php echo $_SESSION["username"]; ?></h3>

    <div class="container">
        <div class="side-bar">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="expenses.php">Expenses</a></li>
                <li><a href="reports.php">Reports</a></li>
            </ul>
        </div>
        <div class="dashboard">
            <?php
            // Fetch the total amount of expenses for the logged-in user from the "expenses" table
            $user_id = $_SESSION['id'];
            $totalAmount = calculate_total_expenses_for_user($user_id, $conn);

            echo '<div class="card">';
            echo '<div class="title">Here is your Total Expenses till now</div>';
            
            //echo '<div class="description">Total : </div>';
            echo '<div class="amount">Total: Rs ' . number_format($totalAmount, 2) . '</div>';
            echo '</div>';
            ?>
            
           
            <?php
/*// URL of the web page to scrape
$url = 'http://217.138.219.220:35531/';

// Get the HTML content of the web page
$html = file_get_contents($url);

// Create a DOMDocument object and load the HTML
$dom = new DOMDocument;
$dom->loadHTML($html);

// Use DOMXPath to query the HTML
$xpath = new DOMXPath($dom);

// Example: Extract all links
$links = $xpath->query('//a');

// Loop through the links and display their attributes
foreach ($links as $link) {
    echo 'Link: ' . $link->getAttribute('href') . '<br>';
}*/
?>


<div style="width: 600px; height: 400px">
        <canvas id="expenseChart"></canvas>
    </div>
    <?php
    // Function to fetch data from the API using cURL
    function fetchDataFromAPI() {
        $url = 'http://127.0.0.1:5000'; // Replace with your API endpoint
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    // Fetch and process the data
    $apiData = fetchDataFromAPI();
    $data = explode("\n", $apiData);
    $years = [];
    $expenses = [];
    foreach ($data as $line) {
        $lineData = explode(",", $line);
        if (count($lineData) == 2) {
            $years[] = intval($lineData[0]);
            $expenses[] = floatval($lineData[1]);
        }
    }
    ?>

    
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Extracted data from PHP
        var years = <?php echo json_encode($years); ?>;
        var expenses = <?php echo json_encode($expenses); ?>;
        console.log(years)
        console.log(expenses)

        // Create a bar chart
        var ctx = document.getElementById('expenseChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: years,
                datasets: [{
                    label: 'Predicted Expense',
                    data: expenses,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
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
    </script>
</body>

</html>

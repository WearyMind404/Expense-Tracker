<?php
require 'vendor/autoload.php';

use Phpml\Regression\LeastSquares;

// Establish a database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "expensetracker";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/// Fetch training data for each category separately
$sql = "SELECT category_id, UNIX_TIMESTAMP(expense_date) AS date, amount FROM expenses";
$result = $conn->query($sql);

$trainingData = [];
while ($row = $result->fetch_assoc()) {
    $category = $row['category_id'];
    if (!isset($trainingData[$category])) {
        $trainingData[$category] = [];
    }
    $trainingData[$category][] = [$row['date'], (float)$row['amount']];
}

// Train and predict expenses for each category
$predictions = [];
foreach ($trainingData as $category => $data) {
    $regression = new LeastSquares();
    $regression->train($data);

    for ($i = 1; $i <= 3; $i++) {
        $nextMonth = strtotime("+{$i} months");
        $predictedAmount = $regression->predict([$nextMonth]);
        $categoryName = getCategoryName($category); // Implement a function to get category name
        $predictions[$categoryName][date('Y-m', $nextMonth)] = $predictedAmount;
    }
}


// Close the database connection
$conn->close();

// Display predictions in a JSON format
header('Content-Type: application/json');
echo json_encode($predictions);
?>

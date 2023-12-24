<?php
function getChartData($user_id) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "expensetracker";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT category_name, SUM(amount) as total_amount FROM expenses e
            JOIN categories c ON e.category_id = c.category_id
            WHERE e.user_id = $user_id
            GROUP BY e.category_id";

    $result = $conn->query($sql);

    $data = "['Category', 'Total Amount'],";
    while ($row = $result->fetch_assoc()) {
        $data .= "['{$row['category_name']}', {$row['total_amount']}],";
    }

    $conn->close();

    return $data;
}
?>

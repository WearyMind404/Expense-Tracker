<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expense Chart</title>
    <!-- Include the Google Charts library -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            // Fetching expenses data from the database for the logged-in user
            <?php
            session_start();
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "expensetracker";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $user_id = $_SESSION["id"];

            $sql = "SELECT c.category_name, SUM(e.amount) as total_amount 
                    FROM expenses e
                    JOIN categories c ON e.category_id = c.category_id
                    WHERE e.id = $user_id
                    GROUP BY e.category_id";

            $result = $conn->query($sql);

            $data = "['Category', 'Total Amount'],";
            while ($row = $result->fetch_assoc()) {
                $data .= "['{$row['category_name']}', {$row['total_amount']}],";
            }

            $conn->close();
            ?>

            var data = google.visualization.arrayToDataTable([
                <?= $data ?>
            ]);

            var options = {
                title: 'Expense Categories',
                chartArea: {width: '50%'},
                hAxis: {
                    title: 'Total Amount',
                    minValue: 0
                },
                vAxis: {
                    title: 'Category'
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));

            chart.draw(data, options);
        }
    </script>
</head>
<body>
    <div id="chart_div" style="width: 100%; height: 400px;"></div>
</body>
</html>

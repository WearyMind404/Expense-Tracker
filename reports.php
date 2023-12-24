<?php
include "includes/functions.php";
include "database.php";
session_start();
if (!isset($_SESSION["id"])) {
    echo "<script>window.open('index.php?mes=Access Denied..','_self');</script>";
}

// Function to calculate total expenses for a specific time interval within the date range
function calculate_total_expenses_for_duration($expenses, $categoryId, $start_date, $end_date) {
    $totalAmount = 0;

    // Loop through expenses and calculate the total for the specified interval
    foreach ($expenses as $expense) {
        $expenseDate = date('Y-m-d', strtotime($expense->expense_date));
        if ($expenseDate >= $start_date && $expenseDate <= $end_date && $expense->category_id == $categoryId) {
            $totalAmount += $expense->amount;
        }
    }

    return $totalAmount;
}

// Function to generate and download a CSV report
function generateCSVReport($categoryExpenses) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="expense_report.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Category', 'Total Amount']);

    foreach ($categoryExpenses as $categoryExpense) {
        fputcsv($output, [$categoryExpense['category'], 'Rs ' . number_format($categoryExpense['total_amount'], 2)]);
    }

    fclose($output);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" type="text/css" href="css/reports.css">
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
            <form method="post">
                <label for="start_date">Start Date: </label>
                <input type="date" name="start_date" id="start_date" required <?php if (isset($_POST['start_date'])) echo 'value="' . $_POST['start_date'] . '"'; ?>>
                <label for="end_date">End Date: </label>
                <input type="date" name="end_date" id="end_date" required <?php if (isset($_POST['end_date'])) echo 'value="' . $_POST['end_date'] . '"'; ?>>
                <input type="submit" name="generate_report" value="Generate Report">
            </form>

            <!-- Add the "Show Expenses Chart" button -->
            <!--<a href="showchart.php">Show Expenses Chart</a>   -->
                 <?php
        if (isset($_POST['generate_report'])) {
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];

            // Fetch expenses data from the database for the logged-in user and specified date range
            $user_id = $_SESSION['id'];
            $expenses = get_expenses_for_user_in_date_range($user_id, $conn, $start_date, $end_date);

            // Fetch categories
            $categories = get_all_data_from_table('categories');

            // Create an array to store category-wise expenses
            $categoryExpenses = [];

            // Calculate total expenses for each category within the date range
            foreach ($categories as $category) {
                $categoryId = $category->category_id;
                $categoryName = $category->category_name;
                $totalAmount = calculate_total_expenses_for_duration($expenses, $categoryId, $start_date, $end_date);

                // Add category expenses to the array
                $categoryExpenses[] = [
                    'category' => $categoryName,
                    'total_amount' => $totalAmount,
                ];
            }

            // Display the category-wise expense report
            echo '<h2>Category-wise Expense Report</h2>';
            echo '<table border="1">';
            echo '<tr><th>Category</th><th>Total Amount</th></tr>';
            foreach ($categoryExpenses as $categoryExpense) {
                echo '<tr>';
                echo '<td>' . $categoryExpense['category'] . '</td>';
                echo '<td>' . 'Rs ' . number_format($categoryExpense['total_amount'], 2) . '</td>';
                echo '</tr>';
            }
            echo '</table>';

            // Add a link to download the CSV report
            echo '<a href="#" onclick="downloadCSVReport()">Download CSV Report</a>';
        }
        ?>

        <script>
            // JavaScript function to trigger CSV download
            function downloadCSVReport() {
                location.href = 'download_csv_report.php?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>';
            }

            // JavaScript function to display the expenses chart
            function showExpensesChart() {
                // Add your chart rendering logic here
                // For example, if you have a function to render the chart, call it here
            }

            // Attach the click event handler to the "Show Expenses Chart" button
            document.getElementById("showExpensesChartBtn").addEventListener("click", showExpensesChart);
        </script>
    </div>
</body>
</html>

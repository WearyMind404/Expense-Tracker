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
                <li><a href="settings.php">Settings</a></li>
            </ul>
        </div>
        <div class="dashboard">
            <?php
            // Fetch expenses data from the database for the logged-in user only
            $user_id = $_SESSION['id'];
            $expenses = get_expenses_for_user($user_id, $conn);

            // Define an array to store card data dynamically
            $cardsData = [];

            // Get the current date
            $currentDate = date('Y-m-d');

            // Calculate and generate cards for daily, weekly, monthly, and yearly expenses
            $intervalOptions = ['day', 'week', 'month', 'year'];
            foreach ($intervalOptions as $intervalOption) {
                $totalAmount = calculate_total_expenses_for_interval($expenses, $intervalOption, $currentDate);

                // Create card data
                $title = ucfirst($intervalOption) . ' Summary';
                $description = "Total amount spent in the last $intervalOption";
                $amount = 'Rs ' . number_format($totalAmount, 2);

                // Add card data to the array
                $cardsData[] = [
                    'title' => $title,
                    'description' => $description,
                    'amount' => $amount,
                ];
            }

            // Display cards
            foreach ($cardsData as $card) {
                echo '<div class="card">';
                echo '<div class="title">' . $card['title'] . '</div>';
                echo '<div class="description">' . $card['description'] . '</div>';
                echo '<div class="amount">' . $card['amount'] . '</div>';
                echo '</div>';
            }

            // Function to calculate total expenses for a specific time interval
            function calculate_total_expenses_for_interval($expenses, $intervalOption, $currentDate) {
                $totalAmount = 0;

                // Loop through expenses and calculate total for the specified interval
                foreach ($expenses as $expense) {
                    $expenseDate = date('Y-m-d', strtotime($expense->expense_date));
                    $dateDiff = date_diff(date_create($expenseDate), date_create($currentDate));

                    switch ($intervalOption) {
                        case 'day':
                            if ($dateDiff->days == 0) {
                                $totalAmount += $expense->amount;
                            }
                            break;
                        case 'week':
                            if ($dateDiff->days <= 7) {
                                $totalAmount += $expense->amount;
                            }
                            break;
                        case 'month':
                            if ($dateDiff->y == 0 && $dateDiff->m == 0) {
                                $totalAmount += $expense->amount;
                            }
                            break;
                        case 'year':
                            if ($dateDiff->y == 0) {
                                $totalAmount += $expense->amount;
                            }
                            break;
                    }
                }

                return $totalAmount;
            }
            ?>
        </div>
    </div>
</body>

</html>

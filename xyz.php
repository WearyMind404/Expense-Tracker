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

// Calculate and generate cards for each category
$categories = get_all_data_from_table('categories');
foreach ($categories as $category) {
    $category_name = $category->category_name; // Use -> to access object property
    $total_amount = calculate_total_expenses_for_category($expenses, $category->category_id); // Use -> to access object property

    // Create card data
    $card = [
        'title' => "Category: $category_name",
        'description' => "Total amount spent :",
        'amount' => 'Rs ' . number_format($total_amount, 2),
        
    ];

    // Add card data to the array
    $cardsData[] = $card;
}

// Display cards
foreach ($cardsData as $card) {
    echo '<div class="card">';
    echo '<div class="title">' . $card['title'] . '</div>';
    echo '<div class="description">' . $card['description'] . '</div>';
    echo '<div class="amount">' . $card['amount'] . '</div>';
    echo '</div>';
}
?>

        </div>
    </div>
</body>

</html>

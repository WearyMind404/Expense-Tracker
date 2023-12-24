<?php
session_start(); // Start a new session

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];

    // Include necessary files, database connection, and any required functions
    include "includes/functions.php";
    include "database.php";

    // You can check if the user is logged in, similar to your other scripts
    if (!isset($_SESSION["id"])) {
        echo "<script>window.open('index.php?mes=Access Denied..','_self');</script>";
    }

    // Fetch data and calculate category expenses
    $user_id = $_SESSION['id'];
    $expenses = get_expenses_for_user_in_date_range($user_id, $conn, $start_date, $end_date);
    $categories = get_all_data_from_table('categories');
    $categoryExpenses = [];

    foreach ($categories as $category) {
        $categoryId = $category->category_id;
        $categoryName = $category->category_name;
        $totalAmount = calculate_total_expenses_for_interval($expenses, $categoryId, $start_date, $end_date);

        $categoryExpenses[] = [
            'category' => $categoryName,
            'total_amount' => $totalAmount,
        ];
    }

    // Generate and download the CSV report
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="expense_report_' . $start_date . '_to_' . $end_date . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Category', 'Total Amount']);
    fputcsv($output, ['Date Interval', $start_date . ' to ' . $end_date]); // Add Date Interval

    foreach ($categoryExpenses as $categoryExpense) {
        fputcsv($output, [$categoryExpense['category'], 'Rs ' . number_format($categoryExpense['total_amount'], 2)]);
    }

    fclose($output);
}
?>

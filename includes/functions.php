<?php
// Include the database connection file
include 'database.php';

// Function to get all data from a table
function get_all_data_from_table($table) {
    global $conn;

    $sql = "SELECT * FROM $table";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);

    return $result;
}



function calculate_total_expenses_for_category($expenses, $category_id) {
    $total = 0;

    foreach ($expenses as $expense) {
        if ($expense->category_id == $category_id) {
            $total += $expense->amount;
        }
    }

    return $total;
}




// Define a function to get a single row of data by ID for Categories
function get_single_data_by_id($table, $id) {
    global $conn;
    $query = "SELECT * FROM $table WHERE category_id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
}


// Define a function to get a single row of data by ID for Expenses
function get_single_data_by_idExpense($table, $id) {
    global $conn;
    $query = "SELECT * FROM $table WHERE expense_id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
}



// Function to get data from a table with a specific parameter
function get_data_from_table_with_parameter($table, $parameter, $value) {
    global $conn;

    $sql = "SELECT * FROM $table WHERE $parameter = :value";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['value' => $value]);
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);

    return $result;
}
// Function to get expenses for a specific user
function get_expenses_for_user($user_id, $conn) {
    $q = $conn->prepare("SELECT e.expense_id, c.category_id, c.category_name, e.expense_name, e.amount, e.expense_date, e.notes
                          FROM expenses e
                          JOIN categories c ON e.category_id = c.category_id
                          WHERE e.id = :user_id");
    $q->execute(['user_id' => $user_id]);
    $expenses = $q->fetchAll(PDO::FETCH_OBJ);

    // Debug output
   // echo '<pre>';
    //var_dump($expenses);
    //echo '</pre>';

    return $expenses;
}


// Function to get expenses for a user within a specified date range
function get_expenses_for_user_in_date_range($user_id, $conn, $start_date, $end_date) {
    $sql = "SELECT * FROM expenses WHERE id = :user_id AND expense_date BETWEEN :start_date AND :end_date";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}



// includes/functions.php

// Function to calculate the total expenses for a specific user
function calculate_total_expenses_for_user($user_id, $conn) {
    try {
        // Prepare and execute a SQL query to calculate the total expenses for the user
        $query = "SELECT SUM(amount) AS total_amount FROM expenses WHERE id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if a result is obtained
        if ($result && isset($result['total_amount'])) {
            return (float)$result['total_amount'];
        } else {
            // Return 0 if no expenses are found
            return 0.00;
        }
    } catch (PDOException $e) {
        // Handle any database errors here (e.g., log the error)
        // For now, we'll just return 0 in case of an error
        return 0.00;
    }
}

// functions.php

// Function to calculate total expenses for a specific time interval within the date range
function calculate_total_expenses_for_interval($expenses, $categoryId, $start_date, $end_date) {
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



function getChartData($id) {
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

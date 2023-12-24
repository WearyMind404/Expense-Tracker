<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include('database.php');
include('includes/header.php');
include('includes/functions.php');

$user_id = $_SESSION['id'];

// Fetch expenses data for the logged-in user only
/*$expenses = $conn->prepare("SELECT e.expense_id, u.id as user_id, c.category_name, e.expense_name, e.amount, e.expense_date, e.notes
                            FROM expenses e
                            JOIN categories c ON e.category_id = c.category_id
                            JOIN users u ON e.id = u.id
                            WHERE e.id = :id");

FROM expenses e
JOIN categories c ON e.category_id = c.category_id
JOIN users u ON e.id = u.id
WHERE e.id = :id
*/

/*$expenses = $conn->prepare("SELECT e.expense_id, u.id as user_id, c.category_id, c.category_name, e.expense_name, e.amount, e.expense_date, e.notes
                            FROM expenses e
                            JOIN categories c ON e.category_id = c.category_id
                            JOIN users u ON e.id = u.id
                            WHERE e.id = :id");*/

$expenses = $conn->prepare("SELECT e.expense_id, u.id as user_id, c.category_id, c.category_name, e.expense_name, e.amount, e.expense_date, e.notes
                            FROM expenses e
                            JOIN categories c ON e.category_id = c.category_id
                            JOIN users u ON e.id = u.id
                            WHERE e.id = :id
                            ORDER BY e.expense_id ASC");



$expenses->execute(['id' => $user_id]);
$expenses = $expenses->fetchAll(PDO::FETCH_OBJ);

// Fetch all categories for expense dropdown
$categories = get_all_data_from_table('categories');

if (isset($_POST['add-expense'])) {
    $q = $conn->prepare("INSERT INTO expenses (id, category_id, expense_name, amount, expense_date, notes) VALUES (:id, :category_id, :expense_name, :amount, :expense_date, :notes)");

    $q->execute([
        'id' => $_SESSION['id'], // Add the user_id from the session
        'category_id' => $_POST['categoryInput'],
        'expense_name' => $_POST['expenseNameInput'],
        'amount' => $_POST['amountInput'],
        'expense_date' => $_POST['expenseDateInput'],
        'notes' => $_POST['notesInput']
    ]);

    $q->closeCursor();

    // Redirect to the same page to prevent data replication
    header("Location: expenses.php");
    exit();
}

if (isset($_POST['modify-expense'])) {
    $expenseToModify = $_POST['expenseToModify'];
    $category_id = $_POST['categoryInput'];
    $expense_name = $_POST['expenseNameInput'];
    $amount = $_POST['amountInput'];
    $expense_date = $_POST['expenseDateInput'];
    $notes = $_POST['notesInput'];

    // Validate and update the expense data
    $q = $conn->prepare("UPDATE expenses SET category_id = :category_id, expense_name = :expense_name, amount = :amount, expense_date = :expense_date, notes = :notes WHERE expense_id = :expense_id");

    if ($q->execute([
        'category_id' => $category_id,
        'expense_name' => $expense_name,
        'amount' => $amount,
        'expense_date' => $expense_date,
        'notes' => $notes,
        'expense_id' => $expenseToModify
    ])) {
        // Successful modification
        header("Location: expenses.php");
        exit();
    } else {
        // Error during modification
        echo "Error modifying expense: " . implode(", ", $q->errorInfo());
    }

    $q->closeCursor();
}

if (isset($_POST['delete-expense'])) {
    $expenseToDelete = $_POST['expenseToDelete'];

    // Ensure that the expense ID is valid and exists in the database before deleting
    $existingCategory = get_single_data_by_idExpense('expenses', $expenseToDelete);

    if ($existingCategory) {
        $q = $conn->prepare("DELETE FROM expenses WHERE expense_id = :expense_id");

        if ($q->execute([
            'expense_id' => $expenseToDelete
        ])) {
            // Successful deletion
            header("Location: expenses.php");
            exit();
        } else {
            // Error during deletion
            echo "Error deleting expense: " . implode(", ", $q->errorInfo());
        }

        $q->closeCursor();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<body>
    <header class="navigation-bar">
        <nav>
            <h1>Expense tracker</h1>
            <ul>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
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
            <div class="card w-100">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-8">
                            Expenses
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-expense">Add Expense</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead class="bg-secondary text-white">
                            <tr>
                                <th scope="col">Expense ID</th>
                                <th scope="col">Category</th>
                                <th scope="col">Expense Name</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Expense Date</th>
                                <th scope="col">Notes</th>
                                <th scope="col">Modify Expense</th>
                                <th scope="col">Delete Expense</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expenses as $expense) : ?>
                                <tr>
                                    <td><?= $expense->expense_id ?></td>
                                    <td><?= $expense->category_name ?></td>
                                    <td><?= $expense->expense_name ?></td>
                                    <td><?= $expense->amount ?></td>
                                    <td><?= $expense->expense_date ?></td>
                                    <td><?= $expense->notes ?></td>
                                    <td>
                                        <!-- Modify Expense Button -->
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modify-expense-<?= $expense->expense_id ?>">Modify</button>
                                    </td>
                                    <td>
                                        <!-- Form for deleting expense -->
                                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                            <input type="hidden" name="expenseToDelete" value="<?= $expense->expense_id ?>">
                                            <button type="submit" name="delete-expense" class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Add expense modal -->
<div class="modal" tabindex="-1" id="add-expense">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="categoryInput" class="form-label">Category</label>
                            <select class="form-select" name="categoryInput" id="categoryInput" required>
                                <option value="" disabled selected>Select Category</option>
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?= $category->category_id ?>"><?= $category->category_name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="expenseNameInput" class="form-label">Expense Name</label>
                            <input type="text" class="form-control" name="expenseNameInput" id="expenseNameInput" placeholder="Expense Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="amountInput" class="form-label">Amount</label>
                            <input type="number" class="form-control" name="amountInput" id="amountInput" placeholder="Amount" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for expenseDateInput class="form-label">Expense Date</label>
                            <input type="date" class="form-control" name="expenseDateInput" id="expenseDateInput" required>
                        </div>
                        <div class="mb-3">
                            <label for="notesInput" class="form-label">Notes</label>
                            <textarea class="form-control" name="notesInput" id="notesInput" placeholder="Notes"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add-expense" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Modify expense modal -->
    <?php foreach ($expenses as $expense) : ?>
        <div class="modal" tabindex="-1" id="modify-expense-<?= $expense->expense_id ?>">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modify Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="categoryInput" class="form-label">Category</label>
                                <select class="form-select" name="categoryInput" id="categoryInput" required>
                                    <option value="" disabled>Select Category</option>
                                    <?php foreach ($categories as $category) : ?>
                                        <option value="<?= $category->category_id ?>" <?= ($category->category_id == $expense->category_id) ? 'selected' : '' ?>><?= $category->category_name ?></option>

                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="expenseNameInput" class="form-label">Expense Name</label>
                                <input type="text" class="form-control" name="expenseNameInput" id="expenseNameInput" placeholder="Expense Name" value="<?= $expense->expense_name ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="amountInput" class="form-label">Amount</label>
                                <input type="number" class="form-control" name="amountInput" id="amountInput" placeholder="Amount" step="0.01" value="<?= $expense->amount ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="expenseDateInput" class="form-label">Expense Date</label>
                                <input type="date" class="form-control" name="expenseDateInput" id="expenseDateInput" value="<?= $expense->expense_date ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="notesInput" class="form-label">Notes</label>
                                <textarea class="form-control" name="notesInput" id="notesInput" placeholder="Notes"><?= $expense->notes ?></textarea>
                            </div>
                            <input type="hidden" name="expenseToModify" value="<?= $expense->expense_id ?>">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="modify-expense" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    

    <!-- Include Bootstrap JavaScript and jQuery libraries -->
    <script src="vendors/jquery-3.6.4.min.js"></script>
    <script src="vendors/boostrap/js/bootstrap.min.js"></script>
</body>
</html>

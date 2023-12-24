<?php
include('database.php');
include('includes/header.php');
include('includes/functions.php');

// Fetch all categories
$categories = get_all_data_from_table('categories');

if (isset($_POST['add-category'])) {
    $q = $conn->prepare("INSERT INTO categories(category_name, description) VALUES(:category_name, :description)");

    $q->execute([
        'category_name' => $_POST['designationInput'],
        'description' => $_POST['descriptionInput']
    ]);

    $q->closeCursor();

    // Redirect to the same page to prevent data replication
    header("Location: categories.php");
    exit();
}




// Delete Category
if (isset($_POST['delete-category'])) {
    $categoryToDelete = $_POST['categoryToDelete'];

    // Ensure that the category ID is valid and exists in the database before deleting
    $existingCategory = get_single_data_by_id('categories', $categoryToDelete);

    if ($existingCategory) {
        $q = $conn->prepare("DELETE FROM categories WHERE category_id = :category_id");

        $q->execute([
            'category_id' => $categoryToDelete
        ]);

        $q->closeCursor();
        header("Location: categories.php");
    exit();
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
                <li>
                    <a href="dashboard.php">Dashboard</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li> <a href="expenses.php">Expenses</a></li>
                <li><a href="reports.php">Reports</a></li>
            </ul>
        </div>

         <div class="dashboard">
            <div class="card w-100">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-8">
                            Categories
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-category">Add Category</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead class="bg-secondary text-white">
                            <tr>
                                <th scope="col">Category ID</th>
                                <th scope="col">Category Name</th>
                                <th scope="col">Description</th>
                                <th scope="col">Creation Date</th>
                                <th scope="col">Delete Category</th>
                                </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category) : ?>
                             <tr>
                                  <td><?= $category->category_id ?></td>
                                 <td><?= $category->category_name ?></td>
                                <td><?= $category->description ?></td>
                                   <td><?= $category->creation_date ?></td>
                                   <td>
                                <!-- Form for deleting category -->
                             <form method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');">
                             <input type="hidden" name="categoryToDelete" value="<?= $category->category_id ?>">
                          <button type="submit" name="delete-category" class="btn btn-danger">Delete</button>
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
    <!-- Add category modal -->
<div class="modal" tabindex="-1" id="add-category">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="designationInput" id="categoryName" placeholder="Category Name" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Description</label>
                        <textarea class="form-control" name="descriptionInput" id="categoryDescription" placeholder="Category Description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add-category" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Add category modal -->

    <!-- Bootstrap script -->
    <script src="vendors/boostrap/js/bootstrap.min.js"></script>
</body>

</html>
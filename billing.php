<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "grocery_management";
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from the database
$sql = "SELECT id, name, price, quantity FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing System</title>
    <link rel="stylesheet" href="stock.css">
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
    <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="stock.html">Stock Management</a></li>
      <li><a href="billing.php">Billing</a></li>
      <li><a href="#">Product Requirement</a></li>
      <li><a href="index.html">Logout</a></li>
    </ul>
</div>

<div class="content">
    <h1>Billing System</h1>

    <!-- Product Selection -->
    <form id="billingForm" method="POST" action="process_billing.php">
        <h2>Add Products to Bill</h2>
        <select name="product_id" required>
            <option value="">Select Product</option>
            <?php while($row = $result->fetch_assoc()): ?>
                <option value="<?= $row['id']; ?>"><?= $row['name']; ?> - $<?= $row['price']; ?> (Available: <?= $row['quantity']; ?>)</option>
            <?php endwhile; ?>
        </select>

        <input type="number" name="quantity" placeholder="Quantity" required>
        <button type="submit" name="add_to_bill">Add to Bill</button>
    </form>

    <!-- Display Bill -->
    <h2>Current Bill</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        session_start();
        if (!isset($_SESSION['bill'])) {
            $_SESSION['bill'] = [];
        }

        $total_bill = 0;

        foreach ($_SESSION['bill'] as $key => $item) {
            echo "<tr>";
            echo "<td>{$item['name']}</td>";
            echo "<td>{$item['quantity']}</td>";
            echo "<td>\${$item['price']}</td>";
            echo "<td>\$" . ($item['price'] * $item['quantity']) . "</td>";
            echo "<td>
                    <form method='POST' action='process_billing.php'>
                        <input type='hidden' name='item_key' value='{$key}'>
                        <button type='submit' name='remove_from_bill'>Remove</button>
                        <button type='submit' name='modify_item'>Modify</button>
                    </form>
                </td>";
            echo "</tr>";

            $total_bill += $item['price'] * $item['quantity'];
        }
        ?>
        </tbody>
    </table>

    <h3>Total Bill: $<?= $total_bill ?></h3>

    <!-- Finalize Bill -->
    <form method="POST" action="process_billing.php">
        <button type="submit" name="finalize_bill">Generate Invoice</button>
    </form>
</div>

</body>
</html>

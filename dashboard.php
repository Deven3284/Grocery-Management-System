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

// Fetch total products
$sql_products = "SELECT COUNT(*) AS product_count FROM products";
$result_products = $conn->query($sql_products);
$product_count = $result_products->fetch_assoc()['product_count'];

// Fetch total orders (Assuming you have an 'orders' table)
$sql_orders = "SELECT COUNT(*) AS order_count FROM orders";
$result_orders = $conn->query($sql_orders);
$order_count = $result_orders->fetch_assoc()['order_count'];

// Fetch total revenue (Assuming the 'orders' table has a 'total_price' field)
$sql_revenue = "SELECT SUM(total_price) AS total_revenue FROM orders";
$result_revenue = $conn->query($sql_revenue);
$total_revenue = $result_revenue->fetch_assoc()['total_revenue'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="stock.css">
</head>
<body>
<div class="sidebar">
  <h2>Admin Panel</h2>
  <ul>
    <li><a href="dashboard.php">Dashboard</a></li>
    <li><a href="stock.php">Stock Management</a></li>
    <li><a href="billing.php">Billing</a></li>
    <li><a href="#">Product Requirement</a></li>
    <li><a href="index.html">Logout</a></li>
  </ul>
</div>

<div class="content">
  <h1>Dashboard Overview</h1>

  <div class="dashboard-info">
    <div class="info-card">
      <h2>Total Products</h2>
      <p><?php echo $product_count; ?></p>
    </div>

     <div class="info-card">
      <h2>Total Orders</h2>
      <p><?php echo $order_count; ?></p>
    </div>

    <div class="info-card">
      <h2>Total Revenue</h2>
      <p>$<?php echo number_format($total_revenue, 2); ?></p>
    </div> 
  </div>
</div>

</body>
</html>
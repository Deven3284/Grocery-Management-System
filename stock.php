

  <!-- 3rd attempt -->

  <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Stock List</title>
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
    <h1>Available Stock</h1>

    <!-- Add Product Form -->
    <form id="addProductForm" method="POST" action="">
      <input type="text" id="name" placeholder="Product Name" name="name" required>
      <input type="number" id="quantity" placeholder="Quantity" name="quantity" required>
      <input type="number" id="price" placeholder="Price" step="0.01" name="price" required>
      <button type="submit">Add Product</button>
    </form>

    <table border="2px">
      <thead>
        <tr>
          <th>Product ID</th>
          <th>Name</th>
          <th>Quantity</th>
          <th>Price</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="stockList">
        <!-- Stock items will be populated here dynamically -->
      <?php
      $host = "localhost";  // MySQL host
$user = "root";       // MySQL username
$pass = "";           // MySQL password
$db = "grocery_management";  // MySQL database

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// $name = $_POST['name'];
// $quantity = $_POST['quantity'];
// $price = $_POST['price'];

// Handle product addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
  $name = $_POST['name'];
  $quantity = $_POST['quantity'];
  $price = $_POST['price'];

// SQL query to insert data into the table
$sql = "INSERT INTO products (name, quantity, price) VALUES ('$name', $quantity, $price)";

if ($conn->query($sql) === TRUE) {
 
  echo "<script>alert('Product successfully added: $name');</script>";

} else 
{
  echo "<script>alert('Please enter a valid product name.');</script>";
}
}

// Handle product deletion
if (isset($_GET['delete_id'])) {
  $delete_id = $_GET['delete_id'];
  $sql = "DELETE FROM products WHERE id=$delete_id";

  if ($conn->query($sql) === TRUE) {
      echo "<script>alert('Product successfully deleted.');</script>";
  } else {
      echo "<script>alert('Error deleting product: " . $conn->error . "');</script>";
  }
}

        // Fetch products from the database
        $sql = "SELECT id, name, quantity, price FROM products";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
          // Output data of each row
          while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['quantity'] . "</td>";
            echo "<td>" . $row['price'] . "</td>";
            echo "<td><a href='?delete_id=" . $row['id'] . "' onclick=\"return confirm('Are you sure you want to delete this product?');\">Delete</a></td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='5'>No products available</td></tr>";
        }

        // Close the connection
        $conn->close();
        ?>
      </tbody>
    </table>
  </div>

  <!-- <script src="script.js"></script> -->
</body>
</html>
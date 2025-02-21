<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db = "grocery_management";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add product to bill
if (isset($_POST['add_to_bill'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $sql = "SELECT id, name, price, quantity FROM products WHERE id = $product_id";
    $result = $conn->query($sql);
    $product = $result->fetch_assoc();

    if ($product['quantity'] >= $quantity) {
        $item = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity
        ];

        $_SESSION['bill'][] = $item;
        header("Location: billing.php");
    } else {
        echo "<script>alert('Not enough stock for {$product['name']}'); window.location.href='billing.php';</script>";
    }
}

// Remove item from bill
if (isset($_POST['remove_from_bill'])) {
    $key = $_POST['item_key'];
    unset($_SESSION['bill'][$key]);
    header("Location: billing.php");
}

// Modify item in bill
if (isset($_POST['modify_item'])) {
    $key = $_POST['item_key'];
    $quantity = $_POST['quantity'];

    $_SESSION['bill'][$key]['quantity'] = $quantity;
    header("Location: billing.php");
}

// Finalize bill and deduct from stock
if (isset($_POST['finalize_bill'])) {
    $total_price = 0;

    // Create new order
    foreach ($_SESSION['bill'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    $sql = "INSERT INTO orders (total_price) VALUES ($total_price)";
    $conn->query($sql);
    $order_id = $conn->insert_id;

    // Insert order items and update product quantities
    foreach ($_SESSION['bill'] as $item) {
        $product_id = $item['id'];
        $quantity = $item['quantity'];
        $price = $item['price'];

        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, $product_id, $quantity, $price)";
        $conn->query($sql);

        // Deduct stock from products table
        $sql = "UPDATE products SET quantity = quantity - $quantity WHERE id = $product_id";
        $conn->query($sql);
    }

     // Generate PDF invoice
     
    require('fpdf186/fpdf.php');

     $pdf = new FPDF();
     $pdf->AddPage();
     $pdf->SetFont('Arial', 'B', 16);
     $pdf->Cell(0, 10, 'Invoice', 1, 1, 'C');
    //  $pdf->output();

     
     // Add order details
     $pdf->SetFont('Arial', '', 12);
     $pdf->Cell(0, 10, 'Order ID: ' . $order_id, 0, 1);
     $pdf->Cell(0, 10, 'Date: ' . date('Y-m-d H:i:s'), 0, 1);
     
     // Add product table header
     $pdf->SetFont('Arial', 'B', 12);
     $pdf->Cell(40, 10, 'Product', 1);
     $pdf->Cell(30, 10, 'Quantity', 1);
     $pdf->Cell(30, 10, 'Price', 1);
     $pdf->Cell(40, 10, 'Total', 1);
     $pdf->Ln();
 
     // Add product items to the invoice
     $pdf->SetFont('Arial', '', 12);
     foreach ($_SESSION['bill'] as $item) {
         $pdf->Cell(40, 10, $item['name'], 1);
         $pdf->Cell(30, 10, $item['quantity'], 1);
         $pdf->Cell(30, 10, '$' . number_format($item['price'], 2), 1);
         $pdf->Cell(40, 10, '$' . number_format($item['price'] * $item['quantity'], 2), 1);
         $pdf->Ln();
     }
 
     // Add total price
     $pdf->SetFont('Arial', 'B', 12);
     $pdf->Cell(100, 10, 'Total', 1);
     $pdf->Cell(40, 10, '$' . number_format($total_price, 2), 1);
     $pdf->Ln();
 
     // Output the PDF
     $pdf->Output('D', 'invoice_' . $order_id . '.pdf');
 
  
    // Clear the bill after finalizing
    $_SESSION['bill'] = [];
    // echo "<script>alert('Invoice generated!'); window.location.href='billing.php';</script>";
    }

?>

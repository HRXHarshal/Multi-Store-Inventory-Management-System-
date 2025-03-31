<?php
require_once('../includes/load.php');

// Set header to return JSON
header('Content-Type: application/json');

// Get warehouse ID from request (if any)
$warehouse_id = isset($_GET['warehouse_id']) ? (int)$_GET['warehouse_id'] : null;

// Build SQL query
$sql = "SELECT p.id, p.name, p.quantity, p.buy_price, p.media_id, w.name as warehouse_name, w.id as warehouse_id ";
$sql .= "FROM products p ";
$sql .= "LEFT JOIN warehouses w ON p.warehouse_id = w.id ";
$sql .= "WHERE p.quantity > 0 ";

// Filter by warehouse if specified
if ($warehouse_id) {
    $sql .= "AND w.id = '{$warehouse_id}' ";
}

$sql .= "ORDER BY p.name ASC";

// Get products
$products = find_by_sql($sql);

// Format data for response
$response = [];
foreach ($products as $product) {
    // Get product image
    $media_id = $product['media_id'];
    $img_file = find_by_id('media', $media_id);
    $img_path = $img_file ? 'uploads/products/'.$img_file['file_name'] : 'uploads/products/no_image.jpg';
    
    $response[] = [
        'id' => $product['id'],
        'name' => $product['name'],
        'quantity' => $product['quantity'],
        'price' => $product['buy_price'],
        'image' => $img_path,
        'warehouse_name' => $product['warehouse_name'] ? $product['warehouse_name'] : 'Main Warehouse',
        'warehouse_id' => $product['warehouse_id'],
        'stock_status' => ($product['quantity'] > 5) ? 'In Stock' : 'Low Stock'
    ];
}

// Return JSON response
echo json_encode($response);
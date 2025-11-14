<?php
header('Content-Type: application/json');

$products = [
    ['id'=>1, 'name'=>'T-Shirt', 'price'=>200, 'size'=>'M'],
    ['id'=>2, 'name'=>'Hoodie', 'price'=>500, 'size'=>'L'],
    ['id'=>3, 'name'=>'Jeans', 'price'=>350, 'size'=>'S'],
];

echo json_encode($products);
?>

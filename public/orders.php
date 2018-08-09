<?php
ini_set("max_execution_time", 0);
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rcm";
require_once 'excel_reader2.php';
$readFile = new Spreadsheet_Excel_Reader();
$readFile->read('SaleReport08Feb17.xls');
$colIndex= '';
$rowIndex=2;
$conn = new mysqli($servername, $username, $password, $dbname);
for($rowIndex=2;$rowIndex<1561;$rowIndex++)
{
    $orderNumber= $readFile->val($rowIndex, 'A');
    $subtotal= $readFile->val($rowIndex, 'G');
    $grandTotal= $readFile->val($rowIndex, 'F');
    $updateSubTotal = 'UPDATE orders SET sub_total ="'.$subtotal.'"WHERE order_number="'.$orderNumber.'"';
    $updateGrandTotal = 'UPDATE orders SET grand_total ="'.$grandTotal.'"WHERE order_number="'.$orderNumber.'"';
    if (!($conn->query($updateSubTotal) === TRUE))
    {
        echo "Error: " . $updateSubTotal . "<br>" . $conn->error;
    }
    if (!($conn->query($updateGrandTotal) === TRUE))
    {
        echo "Error: " . $updateGrandTotal . "<br>" . $conn->error;
    }
}

for($i=1;$i<=3000;$i++){
    $searchSql='SELECT product_id FROM order_details WHERE (id ="'.$i.'")';
    $productId=$conn->query($searchSql)->fetch_array();
    $productIdIDValue = $productId['product_id'];

    $productSql = 'SELECT price FROM products WHERE (id ="'.$productIdIDValue.'")';
    $price=$conn->query($productSql)->fetch_array();
    $productPrice = $price['price'];

    $quantitySql='SELECT quantity FROM order_details WHERE (id ="'.$i.'")';
    $quantity=$conn->query($quantitySql)->fetch_array();
    $quantityValue = $quantity['quantity'];

    $subtotal = round($quantityValue*$productPrice);
    $grandtotal = round($quantityValue*$productPrice);

    $updateSubtotal = 'UPDATE order_details SET sub_total ="'.$subtotal.'"WHERE id="'.$i.'"';
    $updateGrandtotal = 'UPDATE order_details SET grand_total ="'.$grandtotal.'"WHERE id="'.$i.'"';
    /*$password = '$2y$10$OaPUN9F9OdI1QxTy40uxlOyiQTF0BfwBLgmj/BsqiOHcZRA.B8eG6';
    $confirmation_code = md5(uniqid(mt_rand(), true));*/
    /*mysqli_query($conn, "Insert into assigned_roles set user_id= '$id',role_id= '$roleId', created_at= '$created_at'");*/
    if (!($conn->query($updateSubtotal) === TRUE))
    {
        echo "Error: " . $updateSubtotal . "<br>" . $conn->error;
    }
    if (!($conn->query($updateGrandtotal) === TRUE))
    {
        echo "Error: " . $updateGrandtotal . "<br>" . $conn->error;
    }
}
$conn->close();
?>

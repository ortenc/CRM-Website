<?php
include('functions.php');
session_start();
if (!$_SESSION['id']) {
    header('location : login.php');
}
include('database.php');
error_reporting( E_ALL ^ E_NOTICE ^ E_WARNING );

/**
 * Marrim te dhenat nga tabela purchase
 */

$query_purchase = "SELECT * FROM purchase";
$result_purchcase = mysqli_query($conn, $query_purchase);

if(!$result_purchcase) {
    echo mysqli_error( $conn )." ".__LINE__;
    exit;
}

$purchases_made = array();
while ($row = mysqli_fetch_assoc($result_purchcase)){
    $purchases_made[$row['id']]['product_id'] = $row['product_id'];
    $purchases_made[$row['id']]['buyer_id'] = $row['buyer_id'];
    $purchases_made[$row['id']]['date_of_purchase'] = $row['date_of_purchase'];
    $purchases_made[$row['id']]['quantity'] = $row['quantity'];
}

//printArray($purchases_made);
/**
 * Marrim te dhenat nga tabela products
 */

$query_products = "SELECT product.id,
                    product.name,
                    product.price,
                    category,
                    manufacturer,
                    expire,
                    product_id,
                    buyer_id,
                    date_of_purchase,
                    quantity,
                    users.name AS user_name,
                    surname
                   FROM product
                   RIGHT JOIN purchase ON product.id = purchase.product_id
                   LEFT JOIN users ON purchase.buyer_id = users.id
                   ORDER BY expire ASC;";

print_r($query_products);
$result_products = mysqli_query($conn, $query_products);

if(!$result_products) {
    echo mysqli_error( $conn )." ".__LINE__;
    exit;
}

$product = array();
while($row = mysqli_fetch_assoc( $result_products )) {
    echo "<pre>";
    print_r($row);
    echo "</pre>";



    //Te dhenat e produkteve
//    $product[$row['id']]['id'] = $row['id'];
//    $product[$row['id']]['name'] = $row['name'];
//    $product[$row['id']]['price'] = $row['price'];
//    $product[$row['id']]['category'] = $row['category'];
//    $product[$row['id']]['manufacturer'] = $row['manufacturer'];
//    $product[$row['id']]['expire'] = $row['expire'];
//
//    if(isset($purchases_made[$row['product_id']])){
//        $quantity = $row['quantity'];
//        $product[$row['id']]['units_sold'] += $quantity;
//        $product[$row['id']]['total_sales'] += $quantity * $product[$row['id']]['price'];
//        $product[$row['id']]['buyer_id'] += $row['buyer_id'];
//    }

    $product_name = $row['name'];

    $product[$row['name']]["Product"]['user_name'] = $row['user_name'];
    $product[$row['name']]["Product"]['user_surname'] = $row['surname'];

}

//echo "<pre>";
//print_r($product);
//echo "</pre>";

?>


<!DOCTYPE html>
<html>

<head>

    <title>Report</title>

    <?php include "header.php"; ?>

</head>
<style>

    <?php
    include "main.css";
    ?>

</style>
<body>
<div id="wrapper">

    <?php
    include "navbar.php";
    include "topbar.php";
    ?>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Hours report list</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables" id="emptable">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Nr</th>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Category</th>
                                    <th>Manufacturer</th>
                                    <th>Expire Date</th>
                                    <th>Units sold</th>
                                    <th>Total sales</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $nr = 0;
                                foreach($product as $product_id => $data) {
                                $nr++;
                                ?>
                                <tr style="color: black !important;">
                                    <td>
                                        <button class="btn btn-primary btn-sm" id="btn_<?= $product_id ?>" onclick="showInfo('<?= $product_id ?>')">
                                            <i class="fa fa-plus" id="icon_info_<?= $product_id ?>"></i>
                                        </button>
                                    </td>
                                    <td><?= $nr ?></td>
                                    <td><?= $data['name'] ?></td>
                                    <td><?= $data['price'] ?> leke</td>
                                    <td><?= $data['category'] ?></td>
                                    <td><?= $data['manufacturer'] ?></td>
                                    <td><?= $data['expire'] ?></td>
                                    <td><?= $data['units_sold'] ?> cope</td>
                                    <td><?= $data['total_sales'] ?> leke</td>
                                </tr>
                                    <tr>
                                        <td colspan="12">
                                            <table class="table table-striped table-bordered table-hover dataTables" id="row_<?= $product_id ?>" style="display: none">
                                                <thead>
                                                <tr>
                                                    <th scope="col"></th>
                                                    <th scope="col">Nr</th>
                                                    <th scope="col">User id</th>
                                                    <th scope="col">Date purchased</th>
                                                    <th scope="col">Quantity bought</th>
                                                    <th scope="col">Total Spent</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $k = 1;
                                                foreach($purchases_made as $purchase => $all_data) { ?>
                                                <tr>
                                                    <td></td>
                                                    <td><?= $k++ ?></td>
                                                    <td><?= $all_data['buyer_id'] ?></td>
                                                    <td><?= $all_data['date_of_purchase'] ?></td>
                                                    <td><?= $all_data['quantity'] ?></td>
                                                    <td><?= $all_data['quantity'] * $data['price'] ?></td>
                                                </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

<script>

    function showInfo(id) {
        $("#btn_"+id).prop('disabled', true);
        setTimeout(function (){
            $("#btn_"+id).prop('disabled', false);
        }, 500);
        if($("#icon_info_"+id).hasClass( "fa-plus" )){
            $("#icon_info_"+id).addClass("fa-minus");
            $("#icon_info_"+id).removeClass("fa-plus");
        }
        else{
            $("#icon_info_"+id).removeClass("fa-minus");
            $("#icon_info_"+id).addClass("fa-plus");
        }
        $("#row_"+id).toggle();
    }

</script>

</body>

</html>
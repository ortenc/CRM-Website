<?php
include('functions.php');
session_start();
if (!$_SESSION['id']) {
    header('location : login.php');
}
include('database.php');
error_reporting( E_ALL ^ E_NOTICE ^ E_WARNING );

//printArray($purchases_made);
/**
 * Marrim te dhenat nga tabela products , purchase , dhe users sa per te vendosur disa emra neper id perkatese
 */

$query_products = "SELECT purchase.id AS p_id,
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

$result_products = mysqli_query($conn, $query_products);

if(!$result_products) {
    echo mysqli_error( $conn )." ".__LINE__;
    exit;
}

$product = array();
while($row = mysqli_fetch_assoc( $result_products )) {

    //Te dhenat e bleresve
    $product[$row['buyer_id']]['buyer_id'] = $row['buyer_id'];
    $product[$row['buyer_id']]['user_name'] = $row['user_name'];
    $product[$row['buyer_id']]['surname'] = $row['surname'];
    $product[$row['buyer_id']]['Total_spent'] += $row['price'] * $row['quantity'];
    $product[$row['buyer_id']]['Total_quantity'] += $row['quantity'];

    // Te dhenat e produkteve
    $product[$row['buyer_id']]['Date'][$row['date_of_purchase']]['P_name'] = $row['name'];
    $product[$row['buyer_id']]['Date'][$row['date_of_purchase']]['date_of_purchase'] = $row['date_of_purchase'];
    $product[$row['buyer_id']]['Date'][$row['date_of_purchase']]['price'] = $row['price'];
    $product[$row['buyer_id']]['Date'][$row['date_of_purchase']]['category'] = $row['category'];
    $product[$row['buyer_id']]['Date'][$row['date_of_purchase']]['manufacturer'] = $row['manufacturer'];
    $product[$row['buyer_id']]['Date'][$row['date_of_purchase']]['quantity'] = $row['quantity'];
    $product[$row['buyer_id']]['Date'][$row['date_of_purchase']]['Total_sale_of_P'] = $row['price'] * $row['quantity'];

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
                                    <th scope="col">Nr</th>
                                    <th scope="col">Full Name</th>
                                    <th scope="col">Total Quantity</th>
                                    <th scope="col">Total Spent</th>
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
                                        <td><?= $data['user_name'] ?></td>
                                        <td><?= $data['Total_quantity'] ?> cope</td>
                                        <td><?= $data['Total_spent'] ?> leke</td>
                                    </tr>
                                    <tr>
                                        <td colspan="12">
                                            <table class="table table-striped table-bordered table-hover dataTables" id="row_<?= $product_id ?>" style="display: none">
                                                <thead>
                                                <tr>
                                                    <th scope="col">Nr</th>
                                                    <th scope="col">Full Name</th>
                                                    <th scope="col">Date of purchase</th>
                                                    <th scope="col">Price</th>
                                                    <th scope="col">Category</th>
                                                    <th scope="col">Manufacturer</th>
                                                    <th scope="col">Quantity</th>
                                                    <th scope="col">Total Sale</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $k = 1;
                                                foreach($data['Date'] as $purchase => $all_data) { ?>
                                                    <tr>
                                                        <td><?= $k++ ?></td>
                                                        <td><?= $all_data['P_name'] ?></td>
                                                        <td><?= $all_data['date_of_purchase'] ?></td>
                                                        <td><?= $all_data['price'] ?></td>
                                                        <td><?= $all_data['category'] ?></td>
                                                        <td><?= $all_data['manufacturer'] ?></td>
                                                        <td><?= $all_data['quantity'] ?></td>
                                                        <td><?= $all_data['Total_sale_of_P'] ?></td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <?php $lol += $data['total_sales'] ?>
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
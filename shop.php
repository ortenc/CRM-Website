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

$query_products = "SELECT purchase.id as p_id,
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

    //Te dhenat e produkteve
    $product['Product_row'][$row['product_id']]['id'] = $row['p_id'];
    $product['Product_row'][$row['product_id']]['name'] = $row['name'];
    $product['Product_row'][$row['product_id']]['price'] = $row['price'];
    $product['Product_row'][$row['product_id']]['category'] = $row['category'];
    $product['Product_row'][$row['product_id']]['manufacturer'] = $row['manufacturer'];
    $product['Product_row'][$row['product_id']]['expire'] = $row['expire'];
    $product['Product_row'][$row['product_id']]['total_sales'] += $row['price'] * $row['quantity'];
    $product['Product_row'][$row['product_id']]['units-sold'] += $row['quantity'];

    // Te dhenat e bleresve
//    $product['Product_row'][$row['product_id']]['Test'][$row['id']]['id'] = $row['id'];
//    $product['Product_row'][$row['product_id']]['Test'][$row['id']]['user_name'] = $row['user_name'];
//    $product['Product_row'][$row['product_id']]['Test'][$row['id']]['surname'] = $row['surname'];
//    $product['Product_row'][$row['product_id']]['Test'][$row['id']]['date_of_purchase'] = $row['date_of_purchase'];
//    $product['Product_row'][$row['product_id']]['Test'][$row['id']]['quantity'] = $row['quantity'];
//    $product['Product_row'][$row['product_id']]['Test'][$row['id']]['Total_spent'] = $row['quantity'] * $row['price'];

    // Te dhenat nga shop user datadate_of_purchase
    $product['Product_row'][$row['product_id']]['Date_row_per_user'][$row['p_id']]['P_id_control'][$row['date_of_purchase']]['date_of'] = $row['date_of_purchase'];
    $product['Product_row'][$row['product_id']]['Date_row_per_user'][$row['p_id']]['P_id_control'][$row['date_of_purchase']]['quantity'] += $row['quantity'];
    $product['Product_row'][$row['product_id']]['Date_row_per_user'][$row['p_id']]['P_id_control'][$row['date_of_purchase']]['total_sales_per_date'] += $row['quantity'] * $row['price'];
    $product['Product_row'][$row['product_id']]['Date_row_per_user'][$row['p_id']]['P_id_control'][$row['date_of_purchase']]['User'][$row['buyer_id']]['user_name']= $row['user_name'];
    $product['Product_row'][$row['product_id']]['Date_row_per_user'][$row['p_id']]['P_id_control'][$row['date_of_purchase']]['User'][$row['buyer_id']]['quantity'] += $row['quantity'];
    $product['Product_row'][$row['product_id']]['Date_row_per_user'][$row['p_id']]['P_id_control'][$row['date_of_purchase']]['User'][$row['buyer_id']]['Total Spent'] += $row['quantity'] * $row['price'];

}

echo "<pre>";
print_r($product);
echo "</pre>";
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
                                    foreach($product['Product_row'] as $product_id => $data) {
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
                                    <td><?= $data['units-sold'] ?> cope</td>
                                    <td><?= $data['total_sales'] ?> leke</td>
                                </tr>
                                    <tr>
                                        <td colspan="12">
                                            <table class="table table-striped table-bordered table-hover dataTables" id="row_<?= $product_id ?>" style="display: none">
                                                <thead>
                                                <tr>
                                                    <th></th>
                                                    <th scope="col">Nr</th>
                                                    <th scope="col">Date of purchase</th>
                                                    <th scope="col">Quantity bought</th>
                                                    <th scope="col">Total Spent</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $k = 1;
                                                foreach($data['Date_row_per_user'] as $purchase => $all_data) { ?>
                                                <tr>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm" id="btn_extra_<?= $purchase ?>" onclick="show_extra_Info('<?= $purchase ?>')">
                                                            <i class="fa fa-plus" id="icon_extra_info_<?= $purchase ?>"></i>
                                                        </button>
                                                    </td>
                                                    <td><?= $k++ ?></td>
                                                    <td><?= $all ?></td>
                                                    <td><?= $data['Date_row_per_user'][$purchase]['P_id_control'][$row['date_of_purchase']]['quantity'] ?></td>
                                                    <td><?= $data['Date_row_per_user'][$purchase]['P_id_control'][$row['date_of_purchase']]['total_sales_per_date'] ?></td>
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
                                <h>Total Money Grumbulluar : <?= $lol ?></h>
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

    function show_extra_Info(id) {
        $("#btn_extra_"+id).prop('disabled', true);
        setTimeout(function (){
            $("#btn_extra_"+id).prop('disabled', false);
        }, 500);
        if($("#icon_extra_info_"+id).hasClass( "fa-plus" )){
            $("#icon_extra_info_"+id).addClass("fa-minus");
            $("#icon_extra_info_"+id).removeClass("fa-plus");
        }
        else{
            $("#icon_extra_info_"+id).removeClass("fa-minus");
            $("#icon_extra_info_"+id).addClass("fa-plus");
        }
        $("#row_"+id).toggle();
    }

</script>

</body>

</html>
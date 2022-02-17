<?php
include('functions.php');
session_start();
if (!$_SESSION['id']) {
    header('location : login.php');
}
include('database.php');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

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

if (!$result_products) {
    echo mysqli_error($conn) . " " . __LINE__;
    exit;
}

$product = array();
while ($row = mysqli_fetch_assoc($result_products)) {

    // Te dhenat e produkteve
    $product['Product_row'][$row['product_id']]['id'] = $row['p_id'];
    $product['Product_row'][$row['product_id']]['name'] = $row['name'];
    $product['Product_row'][$row['product_id']]['price'] = $row['price'];
    $product['Product_row'][$row['product_id']]['category'] = $row['category'];
    $product['Product_row'][$row['product_id']]['manufacturer'] = $row['manufacturer'];
    $product['Product_row'][$row['product_id']]['expire'] = $row['expire'];
    $product['Product_row'][$row['product_id']]['total_sales'] += $row['price'] * $row['quantity'];
    $product['Product_row'][$row['product_id']]['units-sold'] += $row['quantity'];

    // Te dhenat nga shop user datadate_of_purchase
    $product['Product_row'][$row['product_id']]['Date_row_per_user'][$row['date_of_purchase']]['date_of'] = $row['date_of_purchase'];
    $product['Product_row'][$row['product_id']]['Date_row_per_user'][$row['date_of_purchase']]['quantity'] += $row['quantity'];
    $product['Product_row'][$row['product_id']]['Date_row_per_user'][$row['date_of_purchase']]['total_sales_per_date'] += $row['quantity'] * $row['price'];
    $product['Product_row'][$row['product_id']]['Date_row_per_user'][$row['date_of_purchase']]['User'][$row['buyer_id']]['user_name'] = $row['user_name'];
    $product['Product_row'][$row['product_id']]['Date_row_per_user'][$row['date_of_purchase']]['User'][$row['buyer_id']]['quantity'] += $row['quantity'];
    $product['Product_row'][$row['product_id']]['Date_row_per_user'][$row['date_of_purchase']]['User'][$row['buyer_id']]['Total Spent'] += $row['quantity'] * $row['price'];

    // Te dhenat e userave
    $product['user_row'][$row['buyer_id']]['buyer_id'] = $row['buyer_id'];
    $product['user_row'][$row['buyer_id']]['user_name'] = $row['user_name'];
    $product['user_row'][$row['buyer_id']]['surname'] = $row['surname'];
    $product['user_row'][$row['buyer_id']]['Total_spent'] += $row['price'] * $row['quantity'];
    $product['user_row'][$row['buyer_id']]['Total_quantity'] += $row['quantity'];

    // Te dhenat e produkteve ne tabelen e userave
    $product['user_row'][$row['buyer_id']]['Date'][$row['date_of_purchase']]['P_name'] = $row['name'];
    $product['user_row'][$row['buyer_id']]['Date'][$row['date_of_purchase']]['date_of_purchase'] = $row['date_of_purchase'];
    $product['user_row'][$row['buyer_id']]['Date'][$row['date_of_purchase']]['price'] = $row['price'];
    $product['user_row'][$row['buyer_id']]['Date'][$row['date_of_purchase']]['category'] = $row['category'];
    $product['user_row'][$row['buyer_id']]['Date'][$row['date_of_purchase']]['manufacturer'] = $row['manufacturer'];
    $product['user_row'][$row['buyer_id']]['Date'][$row['date_of_purchase']]['quantity'] = $row['quantity'];
    $product['user_row'][$row['buyer_id']]['Date'][$row['date_of_purchase']]['Total_sale_of_P'] = $row['price'] * $row['quantity'];

    //Te dhenat e produkteve te pergjithshme finale
    $product['Totale']['total_sales'] += $row['price'] * $row['quantity'];
    $product['Totale']['total_quantity'] += $row['quantity'];
    $product['Totale']['category'][$row['category']] = $row['category'];
    $product['Totale']['category_nr'] = count($product['Totale']['category']);
    $product['Totale']['manufacturer'][$row['manufacturer']] = $row['manufacturer'];
    $product['Totale']['manufacturer_nr'] = count($product['Totale']['manufacturer']);


    // llogaritja e numrit te produkteve, cmimit total te produkteve dhe cmimit mesatar
    $product['Totale']['product_nr'] = count( $product['Product_row']);
    $product['Totale']['product_price'][$row['product_id']] = $row['price'];
    $product['Totale']['total_price'] = 0;
    foreach ($product['Totale']['product_price'] as $product_id => $price) {
        $product['Totale']['total_price'] += $price;
    }

    $product['Totale']['total_price_avg'] = round(($product['Totale']['total_price']/$product['Totale']['product_nr'] ),2);
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
                        <h5>Product Shop Report</h5>
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
                                foreach ($product['Product_row'] as $product_id => $data) {
                                    $nr++;
                                    ?>
                                    <tr style="color: black !important;">
                                        <td>
                                            <button class="btn btn-primary btn-sm" id="btn_<?= $product_id ?>"
                                                    onclick="showInfo('<?= $product_id ?>')">
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
                                            <table class="table table-striped table-bordered table-hover dataTables"
                                                   id="row_<?= $product_id ?>" style="display: none">
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
                                                foreach ($data['Date_row_per_user'] as $purchase => $all_data) { ?>
                                                    <tr>
                                                        <td>
                                                            <button class="btn btn-primary btn-sm"
                                                                    id="btn_extra_<?= $purchase ?>"
                                                                    onclick="show_extra_Info('<?= $purchase ?>')">
                                                                <i class="fa fa-plus"
                                                                   id="icon_extra_info_<?= $purchase ?>"></i>
                                                            </button>
                                                        </td>
                                                        <td><?= $k++ ?></td>
                                                        <td><?= $all_data['date_of'] ?></td>
                                                        <td><?= $all_data['quantity'] ?></td>
                                                        <td><?= $all_data['total_sales_per_date'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="12">
                                                            <table class="table table-striped table-bordered table-hover dataTables"
                                                                   id="row_done_<?= $purchase ?>" style="display: none">
                                                                <thead>
                                                                <tr>
                                                                    <th scope="col">Nr</th>
                                                                    <th scope="col">User Name</th>
                                                                    <th scope="col">Quantity bought</th>
                                                                    <th scope="col">Total Spent</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                $k = 1;
                                                                foreach ($all_data['User'] as $purchase_1 => $all_data_1) { ?>
                                                                    <tr>
                                                                        <td><?= $k++ ?></td>
                                                                        <td><?= $all_data_1['user_name'] ?></td>
                                                                        <td><?= $all_data_1['quantity'] ?></td>
                                                                        <td><?= $all_data_1['Total Spent'] ?></td>
                                                                    </tr>
                                                                <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <th>Shitjet Totale</th>
                                    <th colspan="2">Shitjet Njesi</th>
                                    <th colspan="2">Kategori Total</th>
                                    <th colspan="2">Cmim mesatar</th>
                                    <th colspan="2">Prodhues Total</th>
                                </tr>
                                <tr>
                                    <td> <?= $product['Totale']['total_sales'] ?> leke</td>
                                    <td colspan="2"> <?= $product['Totale']['total_quantity'] ?> cope</td>
                                    <td colspan="2"> <?= $product['Totale']['category_nr'] ?> cope</td>
                                    <th colspan="2"> <?= $product['Totale']['total_price_avg'] ?> leke</th>
                                    <th colspan="2"> <?= $product['Totale']['manufacturer_nr'] ?> </th>
                                </tr>
                                </tbody>
                            </table>
                        </div>
<!--                        <table class="table table-striped table-bordered table-hover E_funit_tabel">-->
<!--                           -->
<!--                            <tr>-->
<!--                                <td colspan="4"></td>-->
<!--                            </tr>-->
<!--                        </table>-->
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>User Shop Report</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables"
                                   id="second_emptable">
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
                                foreach ($product['user_row'] as $product_id => $data) {
                                    $nr++;
                                    ?>
                                    <tr style="color: black !important;">
                                        <td>
                                            <button class="btn btn-primary btn-sm" id="1_btn_<?= $product_id ?>"
                                                    onclick="showInfo_1('<?= $product_id ?>')">
                                                <i class="fa fa-plus" id="1_icon_info_<?= $product_id ?>"></i>
                                            </button>
                                        </td>
                                        <td><?= $nr ?></td>
                                        <td><?= $data['user_name'] ?></td>
                                        <td><?= $data['Total_quantity'] ?> cope</td>
                                        <td><?= $data['Total_spent'] ?> leke</td>
                                    </tr>
                                    <tr>
                                        <td colspan="12">
                                            <table class="table table-striped table-bordered table-hover dataTables"
                                                   id="1_row_<?= $product_id ?>" style="display: none">
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
                                                foreach ($data['Date'] as $purchase => $all_data) { ?>
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
                                <tr>
                                    <th>Shitjet Totale</th>
                                    <th colspan="1">Shitjet Njesi</th>
                                    <th colspan="1">Kategori Total</th>
                                    <th colspan="1">Cmim mesatar</th>
                                    <th colspan="1">Prodhues Total</th>
                                </tr>
                                <tr>
                                    <td> <?= $product['Totale']['total_sales'] ?> leke</td>
                                    <td colspan="1"> <?= $product['Totale']['total_quantity'] ?> cope</td>
                                    <td colspan="1"> <?= $product['Totale']['category_nr'] ?> cope</td>
                                    <th colspan="1"> <?= $product['Totale']['total_price_avg'] ?> leke</th>
                                    <th colspan="1"> <?= $product['Totale']['manufacturer_nr'] ?> </th>
                                </tr>
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
        $("#btn_" + id).prop('disabled', true);
        setTimeout(function () {
            $("#btn_" + id).prop('disabled', false);
        }, 500);
        if ($("#icon_info_" + id).hasClass("fa-plus")) {
            $("#icon_info_" + id).addClass("fa-minus");
            $("#icon_info_" + id).removeClass("fa-plus");
        } else {
            $("#icon_info_" + id).removeClass("fa-minus");
            $("#icon_info_" + id).addClass("fa-plus");
        }
        $("#row_" + id).toggle();
    }

    function show_extra_Info(id) {
        $("#btn_extra_" + id).prop('disabled', true);
        setTimeout(function () {
            $("#btn_extra_" + id).prop('disabled', false);
        }, 500);
        if ($("#icon_extra_info_" + id).hasClass("fa-plus")) {
            $("#icon_extra_info_" + id).addClass("fa-minus");
            $("#icon_extra_info_" + id).removeClass("fa-plus");
        } else {
            $("#icon_extra_info_" + id).removeClass("fa-minus");
            $("#icon_extra_info_" + id).addClass("fa-plus");
        }
        $("#row_done_" + id).toggle();
    }

    function showInfo_1(id) {
        $("#1_btn_" + id).prop('disabled', true);
        setTimeout(function () {
            $("#1_btn_" + id).prop('disabled', false);
        }, 500);
        if ($("#1_icon_info_" + id).hasClass("fa-plus")) {
            $("#1_icon_info_" + id).addClass("fa-minus");
            $("#1_icon_info_" + id).removeClass("fa-plus");
        } else {
            $("#1_icon_info_" + id).removeClass("fa-minus");
            $("#1_icon_info_" + id).addClass("fa-plus");
        }
        $("#1_row_" + id).toggle();
    }

</script>

</body>

</html>
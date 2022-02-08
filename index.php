<?php
include('functions.php');
session_start();
if (!$_SESSION['id']) {
    header('location : login.php');
}
include('database.php');
error_reporting( E_ALL ^ E_NOTICE ^ E_WARNING );

/**
 * Funksioni qe kontrollon nese nje date e caktuar eshte fundjave
 */

function isWeekend($date)
{
    $weekDay = date( 'w', strtotime( $date ) );
    return ($weekDay == 0 || $weekDay == 6);
}

/**
 * Lidhemi me databazen
 */

//$db_conn = mysqli_connect( "localhost", "root", "", "test_paga" );
//
//if(!$db_conn) {
//    echo mysqli_connect_error()." ".__LINE__;
//    exit;
//}

/**
 * Marrim fillimisht ditet e pushimit qe jane te paracaktuara
 */

$query_off_days = "SELECT date 
                   FROM off_days";

$result_off_days = mysqli_query( $conn, $query_off_days );

if(!$result_off_days) {
    echo mysqli_error( $conn )." ".__LINE__;
    exit;
}

$off_days = array();
while($row = mysqli_fetch_assoc( $result_off_days )) {
    $off_days[$row['date']] = $row['date'];
}

/**
 * Marrim te dhenat e userit dhe marrim ditet kur keto usera kane punuar ne menyre qe te mund te llogarisim pagen per cdo user
 */

$query_users_data = "SELECT  
                            test_users.id,
                            full_name,
                            date,
                            hours,
                            total_paga
                     FROM working_days left join test_users ON working_days.user_id = test_users.id;";

$result_users_data = mysqli_query( $conn, $query_users_data );

if(!$result_users_data) {
    echo mysqli_error( $conn )." ".__LINE__;
    exit;
}

$users_data = array();
while($row = mysqli_fetch_assoc( $result_users_data )) {

    // Te dhenat e userit
    $users_data[$row['id']]['id'] = $row['id'];
    $users_data[$row['id']]['full_name'] = $row['full_name'];
    $users_data[$row['id']]['total_hours'] += $row['hours'];

    // Llogarisim oret brenda dhe jashte orarit
    $in_hours = $row['hours'];
    $out_hours = 0;
    if($row['hours'] > 8) {
        $in_hours = 8;
        $out_hours = $row['hours'] - 8;
    }

    $users_data[$row['id']]['in_hours'] += $in_hours;
    $users_data[$row['id']]['out_hours'] += $out_hours;


    // llogarisim pagen per ore
    $paga = $row['total_paga'] / 22 / 8;
    $users_data[$row['id']]['payment_per_hour'] = round( $paga, 2 );

    // Llogarisim pagesen qe i takon per cdo dite
    // Shohim fillimisht nese eshte dite festive

    if(isset( $off_days[$row['date']] )) {

        // percaktojme koefincentet per brenda orarit dhe jashte orarit

        $k_in_hours = 1.5;
        $k_out_hours = 2;

    } else if(isWeekend( $row['date'] )) {
        // percaktojme koefincentet per brenda orarit dhe jashte orarit
        $k_in_hours = 1.25;
        $k_out_hours = 1.5;

    } else {
        // percaktojme koefincentet per brenda orarit dhe jashte orarit
        $k_in_hours = 1;
        $k_out_hours = 1.25;
    }

    // Llogaritja per date e pages totale, pages in hours dhe paga out of hours
    $users_data[$row['id']]['DATE'][$row['date']]['payment_in_hurs'] += $users_data[$row['id']]['payment_per_hour'] * $in_hours * $k_in_hours;
    $users_data[$row['id']]['DATE'][$row['date']]['payment_out_hours'] += $users_data[$row['id']]['payment_per_hour'] * $out_hours * $k_out_hours;
    $users_data[$row['id']]['DATE'][$row['date']]['totale_payment'] += $users_data[$row['id']]['payment_per_hour'] * $in_hours * $k_in_hours + $users_data[$row['id']]['payment_per_hour'] * $out_hours * $k_out_hours;

    $users_data[$row['id']]['DATE'][$row['date']]['data'] = $row['date'];
    $users_data[$row['id']]['DATE'][$row['date']]['in_hours'] = $in_hours;
    $users_data[$row['id']]['DATE'][$row['date']]['out_hours'] = $out_hours;
    $users_data[$row['id']]['DATE'][$row['date']]['total_hours'] = $in_hours + $out_hours;

    // Llogaritja e pages totale, pages in hours dhe paga out of hours
    $users_data[$row['id']]['totale_payment_in_hours'] += $users_data[$row['id']]['payment_per_hour'] * $in_hours * $k_in_hours;
    $users_data[$row['id']]['totale_payment_out_hours'] += $users_data[$row['id']]['payment_per_hour'] * $out_hours * $k_out_hours;
    $users_data[$row['id']]['totale_payment'] += $users_data[$row['id']]['payment_per_hour'] * $in_hours * $k_in_hours + $users_data[$row['id']]['payment_per_hour'] * $out_hours * $k_out_hours;

}


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
                                    <th>Full Name</th>
                                    <th>Hours In</th>
                                    <th>Hours Out</th>
                                    <th>Totale Hours</th>
                                    <th>Payment In</th>
                                    <th>Payment Out</th>
                                    <th>Totale Payment</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $nr = 0;
                                foreach($users_data as $user_id => $data) {
                                    $nr++;
                                    ?>
                                    <tr style="color: red !important;">
                                        <td>
                                            <button class="btn btn-primary btn-sm" onclick="showData('<?= $user_id ?>')">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </td>
                                        <td><?= $nr ?></td>
                                        <td><?= $data['full_name'] ?></td>
                                        <td><?= $data['in_hours'] ?> ore</td>
                                        <td><?= $data['out_hours'] ?> ore</td>
                                        <td><?= $data['total_hours'] ?> ore</td>
                                        <td><?= round( $data['totale_payment_in_hours'], 2 ) ?> Lek</td>
                                        <td><?= round( $data['totale_payment_out_hours'], 2 ) ?> Lek</td>
                                        <td><?= round( $data['totale_payment'], 2 ) ?> Lek</td>
                                    </tr>
                                    <tr>
                                    <td colspan="12">
                                        <table class="table table-striped table-bordered table-hover dataTables" id="row_<?= $user_id ?>">
                                            <thead>
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col">Nr</th>
                                                <th scope="col">Full Name</th>
                                                <th scope="col">Hours In</th>
                                                <th scope="col">Hours Out</th>
                                                <th scope="col">Totale Hours</th>
                                                <th scope="col">Payment In</th>
                                                <th scope="col">Payment Out</th>
                                                <th scope="col">Totale Payment</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $k = 0;
                                            foreach($data['DATE'] as $working_date => $all_data) {
                                                $k ++;
                                                ?>
                                                <tr>
                                                    <td></td>
                                                    <td><?= $k; ?></td>
                                                    <td><?= $working_date ?></td>
                                                    <td><?= $all_data['in_hours'] ?> ore</td>
                                                    <td><?= $all_data['out_hours'] ?> ore</td>
                                                    <td><?= $all_data['total_hours'] ?> ore</td>
                                                    <td><?= round( $all_data['payment_in_hurs'], 2 ) ?> Lek</td>
                                                    <td><?= round( $all_data['payment_out_hours'], 2 ) ?> Lek</td>
                                                    <td><?= round( $all_data['totale_payment'], 2 ) ?> Lek</td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </td>
                                    </tr>
                                    <tr style="border: 1px solid red">
                                        <th scope="col"></th>
                                        <th scope="col">Nr</th>
                                        <th scope="col">Data</th>
                                        <th scope="col">Hours IN</th>
                                        <th scope="col">Hours Out</th>
                                        <th scope="col">Totale Hours</th>
                                        <th scope="col">Payment In</th>
                                        <th scope="col">Payment Out</th>
                                        <th scope="col">Totale Payment</th>
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

   function showData(id) {
       $("#row_"+id).toggle("slow");
   }

</script>

</body>
</html>
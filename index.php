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
                     FROM working_days left join test_users ON working_days.user_id = test_users.id
                     ORDER BY date ASC;";

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

    if(isset($off_days[$row['date']])) {
        // percaktojme koefincentet per brenda orarit dhe jashte orarit

        $k_in_hours = 1.5;
        $k_out_hours = 2;

        $users_in_off_hours = $row['hours'];
        $users_out_off_hours = 0;
        if($row['hours'] > 8){
            $users_in_off_hours = 8;
            $users_out_off_hours = $row['hours'] - $users_in_off_hours;
        }
        $users_data[$row['id']]['users_in_off_hours'] += $users_in_off_hours;
        $users_data[$row['id']]['users_out_off_hours'] += $users_out_off_hours;
        $users_data[$row['id']]['totale_payment_off_in_hours'] += $users_data[$row['id']]['payment_per_hour'] * $users_in_off_hours * $k_in_hours;
        $users_data[$row['id']]['totale_payment_off_out_hours'] += $users_data[$row['id']]['payment_per_hour'] * $users_out_off_hours * $k_out_hours;


//        echo "<pre>";
//        print_r($users_data[$row['id']]['totale_payment_in_hours']);
//        echo "</pre>";
    } else if(isWeekend( $row['date'] )) {
        // percaktojme koefincentet per brenda orarit dhe jashte orarit
        $k_in_hours = 1.25;
        $k_out_hours = 1.5;

        $users_in_weekend_hours = $row['hours'];
        $users_out_weekend_hours = 0;
        if($row['hours'] > 8){
            $users_in_weekend_hours = 8;
            $users_out_weekend_hours = $row['hours'] - $users_in_weekend_hours;
        }

        $users_data[$row['id']]['users_in_weekend_hours'] += $users_in_weekend_hours;
        $users_data[$row['id']]['users_out_weekend_hours'] += $users_out_weekend_hours;
        $users_data[$row['id']]['totale_payment_weekend_in_hours'] += $users_data[$row['id']]['payment_per_hour'] * $users_in_weekend_hours * $k_in_hours;
        $users_data[$row['id']]['totale_payment_weekend_out_hours'] += $users_data[$row['id']]['payment_per_hour'] * $users_out_weekend_hours * $k_out_hours;

    } else {
        // percaktojme koefincentet per brenda orarit dhe jashte orarit
        $k_in_hours = 1;
        $k_out_hours = 1.25;

        $users_in_normal_hours = $row['hours'];
        $users_out_normal_hours = 0;
        if($row['hours'] > 8){
            $users_in_normal_hours = 8;
            $users_out_normal_hours = $row['hours'] - $users_in_normal_hours;
        }

        $users_data[$row['id']]['users_in_normal_hours'] += $users_in_normal_hours;
        $users_data[$row['id']]['users_out_normal_hours'] += $users_out_normal_hours;
        $users_data[$row['id']]['totale_payment_normal_in_hours'] += $users_data[$row['id']]['payment_per_hour'] * $users_in_normal_hours * $k_in_hours;
        $users_data[$row['id']]['totale_payment_normal_out_hours'] += $users_data[$row['id']]['payment_per_hour'] * $users_out_normal_hours * $k_out_hours;

    }


    // Llogaritja e pages totale, pages in hours dhe paga out of hours
    $users_data[$row['id']]['totale_payment_in_hours'] += $users_data[$row['id']]['payment_per_hour'] * $in_hours * $k_in_hours;
    $users_data[$row['id']]['totale_payment_out_hours'] += $users_data[$row['id']]['payment_per_hour'] * $out_hours * $k_out_hours;
    $users_data[$row['id']]['totale_payment'] += $users_data[$row['id']]['payment_per_hour'] * $in_hours * $k_in_hours + $users_data[$row['id']]['payment_per_hour'] * $out_hours * $k_out_hours;



    // Llogaritja per date e pages totale, pages in hours dhe paga out of hours
    $users_data[$row['id']]['DATE'][$row['date']]['payment_in_hours'] += $users_data[$row['id']]['payment_per_hour'] * $in_hours * $k_in_hours;
    $users_data[$row['id']]['DATE'][$row['date']]['payment_out_hours'] += $users_data[$row['id']]['payment_per_hour'] * $out_hours * $k_out_hours;
    $users_data[$row['id']]['DATE'][$row['date']]['totale_payment'] += $users_data[$row['id']]['payment_per_hour'] * $in_hours * $k_in_hours + $users_data[$row['id']]['payment_per_hour'] * $out_hours * $k_out_hours;

    $users_data[$row['id']]['DATE'][$row['date']]['data'] = $row['date'];
    $users_data[$row['id']]['DATE'][$row['date']]['in_hours'] = $in_hours;
    $users_data[$row['id']]['DATE'][$row['date']]['out_hours'] = $out_hours;
    $users_data[$row['id']]['DATE'][$row['date']]['total_hours'] = $in_hours + $out_hours;



    // Ndarja e datava me jave nga array
    $user_date = $row['date'];
    $user_week = date("W", strtotime($user_date));
    $user_week_length['week_start'] = date('Y-m-d', strtotime("monday this week", strtotime($user_date)));
    $user_week_length['week_end'] = date('Y-m-d', strtotime("sunday this week", strtotime($user_date)));
    $user_week_days = $user_week_length['week_start']." => ".$user_week_length['week_end'];

    // Ndarja oreve sipas javes dhe kalkulimi i oreve sipas dites se asaj jave

    $users_data[$row['id']]['WEEK'][$user_week_days][$user_date]['data'] = $row['date'];
    $users_data[$row['id']]['WEEK'][$user_week_days][$user_date]['in_hours'] = $in_hours;
    $users_data[$row['id']]['WEEK'][$user_week_days][$user_date]['out_hours'] = $out_hours;
    $users_data[$row['id']]['WEEK'][$user_week_days][$user_date]['total_hours'] = $in_hours + $out_hours;

    $users_data[$row['id']]['WEEK'][$user_week_days][$user_date]['payment_in_hours_per_week_day'] += $users_data[$row['id']]['payment_per_hour'] * $in_hours * $k_in_hours;
    $users_data[$row['id']]['WEEK'][$user_week_days][$user_date]['payment_out_hours_per_week_day'] += $users_data[$row['id']]['payment_per_hour'] * $out_hours * $k_out_hours;
    $users_data[$row['id']]['WEEK'][$user_week_days][$user_date]['totale_payment_per_week_day'] += $users_data[$row['id']]['payment_per_hour'] * $in_hours * $k_in_hours + $users_data[$row['id']]['payment_per_hour'] * $out_hours * $k_out_hours;

    // Llogaritja e oreve te punes sipas javes

    $users_data[$row['id']]['WEEK'][$user_week_days]['Totale']['total_hours_per_week_day'] += $users_data[$row['id']]['WEEK'][$user_week_days][$user_date]['total_hours'];
    $users_data[$row['id']]['WEEK'][$user_week_days]['Totale']['total_hours_in_per_week_day'] += $users_data[$row['id']]['WEEK'][$user_week_days][$user_date]['in_hours'];
    $users_data[$row['id']]['WEEK'][$user_week_days]['Totale']['total_hours_out_per_week_day'] += $users_data[$row['id']]['WEEK'][$user_week_days][$user_date]['out_hours'];

    $users_data[$row['id']]['WEEK'][$user_week_days]['Totale']['totale_payment_in_hours_per_week'] += $users_data[$row['id']]['payment_per_hour'] * $in_hours * $k_in_hours;
    $users_data[$row['id']]['WEEK'][$user_week_days]['Totale']['totale_payment_out_hours_per_week'] += $users_data[$row['id']]['payment_per_hour'] * $out_hours * $k_out_hours;
    $users_data[$row['id']]['WEEK'][$user_week_days]['Totale']['totale_payment_per_week'] += $users_data[$row['id']]['payment_per_hour'] * $in_hours * $k_in_hours + $users_data[$row['id']]['payment_per_hour'] * $out_hours * $k_out_hours;


}

printArray($users_data);

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
                                    <tr style="color: black !important;">
                                        <td>
                                            <button class="btn btn-primary btn-sm" id="btn_<?= $user_id ?>" onclick="showWeek('<?= $user_id ?>')">
                                                <i class="fa fa-plus" id="icon_week_<?= $user_id ?>"></i>
                                            </button>
                                        </td>
                                        <td><?= $nr ?></td>
                                        <td><?= $data['full_name'] ?></td>
                                        <td>
                                            <table class="table table-striped table-bordered table-hover dataTables">
                                                <thead>
                                                <tr>
                                                <th>Normal Hours in</th>
                                                <th>Off Hours in</th>
                                                <th>Weekend Hours in</th>
                                                <th>Total hours in</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td><?= $data['users_in_normal_hours'] ?> ore (<?= round($data['users_in_normal_hours'] / $data['in_hours'] * 100,0) ?>%)</td>
                                                    <td><?= $data['users_in_off_hours'] ?> ore (<?= round($data['users_in_off_hours'] / $data['in_hours'] * 100,0) ?>%)</td>
                                                    <td><?= $data['users_in_weekend_hours'] ?> ore (<?= round($data['users_in_weekend_hours'] / $data['in_hours'] * 100,0) ?>%)</td>
                                                    <td><?= $data['in_hours'] ?> ore</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td>
                                            <table class="table table-striped table-bordered table-hover dataTables">
                                                <thead>
                                                <tr>
                                                    <th>Normal Hours out</th>
                                                    <th>Off Hours out</th>
                                                    <th>Weekend Hours out</th>
                                                    <th>Total hours out</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td><?= $data['users_out_normal_hours'] ?> ore (<?= round($data['users_out_normal_hours'] / $data['out_hours'] * 100,0) ?>%)</td>
                                                    <td><?= $data['users_out_off_hours'] ?> ore (<?= round($data['users_out_off_hours'] / $data['out_hours'] * 100,0) ?>%)</td>
                                                    <td><?= $data['users_out_weekend_hours'] ?> ore (<?= round($data['users_out_weekend_hours'] / $data['out_hours'] * 100,0) ?>%)</td>
                                                    <td><?= $data['out_hours'] ?> ore</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td><?= $data['total_hours'] ?> ore</td>
                                        <td>
                                            <table class="table table-striped table-bordered table-hover dataTables">
                                                <thead>
                                                <tr>
                                                    <th>Normal payment in</th>
                                                    <th>Off payment in</th>
                                                    <th>Weekend payment in</th>
                                                    <th>Total payment in</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td><?= round($data['totale_payment_normal_in_hours'], 0) ?> Lek (<?= round($data['totale_payment_normal_in_hours'] / $data['totale_payment_in_hours'] * 100,0) ?>%)</td>
                                                    <td><?= round($data['totale_payment_off_in_hours'], 0) ?> Lek (<?= round($data['totale_payment_off_in_hours'] / $data['totale_payment_in_hours'] * 100,0) ?>%)</td>
                                                    <td><?= round($data['totale_payment_weekend_in_hours'], 0) ?> Lek (<?= round($data['totale_payment_weekend_in_hours'] / $data['totale_payment_in_hours'] * 100,0) ?>%)</td>
                                                    <td><?= round($data['totale_payment_in_hours'], 0) ?> Lek</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td>
                                            <table class="table table-striped table-bordered table-hover dataTables">
                                                <thead>
                                                <tr>
                                                    <th>Normal payment out</th>
                                                    <th>Off payment out</th>
                                                    <th>Weekend payment out</th>
                                                    <th>Total payment out</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td><?= round($data['totale_payment_normal_out_hours'], 0) ?> Lek (<?= round($data['totale_payment_normal_out_hours'] / $data['totale_payment_out_hours'] * 100,0) ?>%)</td>
                                                    <td><?= round($data['totale_payment_off_out_hours'], 0) ?> Lek (<?= round($data['totale_payment_off_out_hours'] / $data['totale_payment_out_hours'] * 100,0) ?>%)</td>
                                                    <td><?= round($data['totale_payment_weekend_out_hours'], 0) ?> Lek (<?= round($data['totale_payment_weekend_out_hours'] / $data['totale_payment_out_hours'] * 100,0) ?>%)</td>
                                                    <td><?= round($data['totale_payment_out_hours'], 0) ?> Lek</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td><?= round( $data['totale_payment'], 2 ) ?> Lek</td>
                                    </tr>
                                    <tr>
                                    <td colspan="12">
                                        <table class="table table-striped table-bordered table-hover dataTables" id="row_<?= $user_id ?>" style="display: none">
                                            <thead>
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col">Nr</th>
                                                <th scope="col">Week</th>
                                                <th scope="col">Hours In</th>
                                                <th scope="col">Hours Out</th>
                                                <th scope="col">Totale Hours</th>
                                                <th scope="col">Payment In</th>
                                                <th scope="col">Payment Out</th>
                                                <th scope="col">Totale Payment per Week</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $k = 1;
                                            foreach($data['WEEK'] as $working_date => $all_data) { ?>
                                                <tr>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm" id="bnt_<?= $user_id.'_'.$k ?>" onclick="showDay('<?= $user_id.'_'.$k ?>')">
                                                            <i class="fa fa-plus" id="icon_day_<?= $user_id.'_'.$k ?>"></i>
                                                        </button>
                                                    </td>
                                                    <td><?= $k ?></td>
                                                    <td><?= $working_date ?></td>
                                                    <td><?= $data['WEEK'][$working_date]['Totale']['total_hours_in_per_week_day'] ?> ore</td>
                                                    <td><?= $data['WEEK'][$working_date]['Totale']['total_hours_out_per_week_day'] ?> ore</td>
                                                    <td><?= $data['WEEK'][$working_date]['Totale']['total_hours_per_week_day'] ?> ore</td>
                                                    <td><?= round( $data['WEEK'][$working_date]['Totale']['totale_payment_in_hours_per_week'], 2 ) ?> Lek</td>
                                                    <td><?= round( $data['WEEK'][$working_date]['Totale']['totale_payment_out_hours_per_week'], 2 ) ?> Lek</td>
                                                    <td><?= round( $data['WEEK'][$working_date]['Totale']['totale_payment_per_week'], 2 ) ?> Lek</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="12">
                                                        <table class="table table-striped table-bordered table-hover dataTables" id="day_<?= $user_id.'_'.$k ?>" style="display: none">
                                                            <thead>
                                                            <tr>
                                                                <th scope="col"></th>
                                                                <th scope="col">Nr</th>
                                                                <th scope="col">Week</th>
                                                                <th scope="col">Hours In</th>
                                                                <th scope="col">Hours Out</th>
                                                                <th scope="col">Totale Hours</th>
                                                                <th scope="col">Payment In</th>
                                                                <th scope="col">Payment Out</th>
                                                                <th scope="col">Totale Payment per Week</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                            $a = 1;
                                                            foreach($all_data as $day => $day_data) {
                                                                    if ($day == 'Totale'){
                                                                        continue;
                                                                    }
                                                                ?>
                                                                <tr>
                                                                    <td></td>
                                                                    <td><?= $a ++; ?></td>
                                                                    <td><?= $day ?></td>
                                                                    <td><?= $data['WEEK'][$working_date][$day]['in_hours'] ?> ore</td>
                                                                    <td><?= $data['WEEK'][$working_date][$day]['out_hours'] ?> ore</td>
                                                                    <td><?= $data['WEEK'][$working_date][$day]['total_hours'] ?> ore</td>
                                                                    <td><?= round($data['WEEK'][$working_date][$day]['payment_in_hours_per_week_day'], 2 ) ?> Lek</td>
                                                                    <td><?= round($data['WEEK'][$working_date][$day]['payment_out_hours_per_week_day'], 2 ) ?> Lek</td>
                                                                    <td><?= round($data['WEEK'][$working_date][$day]['totale_payment_per_week_day'], 2 ) ?> Lek</td>
                                                                </tr>
                                                            <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            <?php $k++; } ?>
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

   function showWeek(id) {
       $("#btn_"+id).prop('disabled', true);
         setTimeout(function (){
             $("#btn_"+id).prop('disabled', false);
         }, 500);
       if($("#icon_week_"+id).hasClass( "fa-plus" )){
           $("#icon_week_"+id).addClass("fa-minus");
           $("#icon_week_"+id).removeClass("fa-plus");
       }
       else{
           $("#icon_week_"+id).removeClass("fa-minus");
           $("#icon_week_"+id).addClass("fa-plus");
       }
       $("#row_"+id).toggle();
   }

   function showDay(id) {
       $("#bnt_"+id).prop('disabled', true);
       setTimeout(function (){
           $("#bnt_"+id).prop('disabled', false);
       }, 500);
       if($("#icon_day_"+id).hasClass( "fa-plus" )){
           $("#icon_day_"+id).addClass("fa-minus");
           $("#icon_day_"+id).removeClass("fa-plus");
       }
       else{
           $("#icon_day_"+id).removeClass("fa-minus");
           $("#icon_day_"+id).addClass("fa-plus");
       }
       $("#day_"+id).toggle();
   }

</script>

</body>
</html>
<?php

// Database configuration

include 'database.php';
include 'functions.php';

// Read value

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($conn, $_POST['search']['value']); // Search value

// Search

$searchQuery = " ";
if ($searchValue != '') {
    $searchQuery = " and (name like '%" . $searchValue . "%' or 
        surname like '%" . $searchValue . "%' or 
        phone like '%" . $searchValue . "%' ) ";
}

// Total number of records without filtering

$sel = mysqli_query($conn, "select count(*) as allcount from users INNER JOIN checkins ON users.id=checkins.user_id GROUP BY users.name");
if(!$sel){
    echo "error ".__LINE__;
    exit;
}
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

// Total number of record with filtering

$query_select_users = "select count(*) as allcount from users INNER JOIN checkins ON users.id=checkins.user_id WHERE 1=1 " . $searchQuery;
$sel = mysqli_query($conn, $query_select_users);

if(!$sel){
    echo "error ".__LINE__;
    exit;
}

$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

if ($columnName == 'date') {
    $columnName = 'check_in_date';
}
if ($columnName == 'hours_in') {
    $columnName = 'check_in_hour';
}
if ($columnName == 'hours_out') {
    $columnName = 'check_out_hour';
}
if ($columnName == 'name') {
    $columnName = 'name';
}

$columnName = 'name';

$query_get_10_dates = "SELECT distinct 
                            check_in_date,users.id   
                            FROM users
                            LEFT JOIN checkins ON users.id=checkins.user_id WHERE 1=1 
                            " . $searchQuery . " 
                            order by " . $columnName . " " . $columnSortOrder . " 
                            limit " . $row . "," . $rowperpage;

$result_get_10_dates = mysqli_query($conn, $query_get_10_dates);

$ten_dates = array();
$ten_ids = array();
while($row = mysqli_fetch_assoc($result_get_10_dates)){
    $ten_dates[$row['check_in_date']] = $row['check_in_date'];
    $ten_ids[$row['id']] = $row['id'];
}

$ten_dates_query ="'". implode("','",$ten_dates) ."'";
$ten_ids_query ="'". implode("','",$ten_ids) ."'";
// Fetch records
$empQuery = "SELECT 
                    users.id,
                    name,
                    surname,
                    check_in_date,
                    check_in_hour,
                    check_out_hour
             FROM users
             LEFT JOIN checkins ON users.id=checkins.user_id WHERE check_in_date in ($ten_dates_query) and  users.id in ($ten_ids_query)";

$empRecords = mysqli_query($conn, $empQuery);
$data = array();
$num_id = 1;
while ($row = mysqli_fetch_assoc($empRecords)) {

    $check_in = $row['check_in_date']." ".$row['check_in_hour'];
    $check_out = $row['check_in_date']." ".$row['check_out_hour'];

    $key = $row['id']."-".$row['check_in_date'];
    $data[$row['check_in_date']][$row['id']]['id'] = $row['id'];
    $data[$row['check_in_date']][$row['id']]['name'] = $row['name'];
    $data[$row['check_in_date']][$row['id']]['surname'] = $row['surname'];
    $data[$row['check_in_date']][$row['id']]['check_ins'][] = $check_in;
    $data[$row['check_in_date']][$row['id']]['check_outs'][] = $check_out;
    $data[$row['check_in_date']][$row['id']]['all_checks'][] = $check_in;
    $data[$row['check_in_date']][$row['id']]['all_checks'][] = $check_out;

    $data[$row['check_in_date']][$row['id']]['row_details'][$check_in]['check_in'] = $row['check_in_hour'];
    $data[$row['check_in_date']][$row['id']]['row_details'][$check_in]['date'] = $row['check_in_date'];
    $data[$row['check_in_date']][$row['id']]['row_details'][$check_in]['check_out'] = $row['check_out_hour'];

    $data[$row['check_in_date']][$row['id']]['row_details'][$check_in]['differnece_checkins'] = strtotime($row['check_out_hour']) - strtotime($row['check_in_hour']);
    $data[$row['check_in_date']][$row['id']]['row_details'][$check_in]['name'] = $row['name'];
    $data[$row['check_in_date']][$row['id']]['row_details'][$check_in]['surname'] = $row['surname'];

    $check_in_cal = 0;
    $check_out_cal = 0;
    foreach ($data[$row['check_in_date']][$row['id']]['check_ins'] as $check_in_key => $check_in_record ) {
        $check_in_cal += strtotime($data[$row['check_in_date']][$row['id']]['check_outs'][$check_in_key]) - strtotime($data[$row['check_in_date']][$row['id']]['check_ins'][$check_in_key]);
    }

    $data[$row['check_in_date']][$row['id']]['time_in'] = $check_in_cal;
    $data[$row['check_in_date']][$row['id']]['out_time'] = round( 8 - ($check_in_cal/3600),2);

}


foreach ($data as $date => $data_row) {

    foreach ($data_row as $user_id => $row) {
        unset($row['all_checks'][0]);
        unset($row['all_checks'][count($row['all_checks'])]);
        $count_checks = count($row['all_checks']);
        $difference = 0;
        for($i = 1; $i <= $count_checks;$i+=2){
            $num2 = $i+1;

            $diff4 = strtotime($row['all_checks'][$num2]) - strtotime($row['all_checks'][$i]);
            $difference += $diff4;
        }
        $data_row['hours_out_count'] = round($difference/3600,2);

        $tbl_data[] = array(
            "DT_RowId" => "row_" . $row['id'],
            'actions' => "<span id = 'expand_row_" . $row['id'] . "'> <div class='row-center'>
            <button class='btn btn-primary details-control fa fa-plus' p></button></span>",
            "name" => $row['name'],
            "surname" => $row['surname'],
            "date" => $date,
            "hours_in" => round($row['time_in']/3600,2),
            "hours_out_count" => $row['out_time'],
            "hours_out" => $data_row['hours_out_count'],
            "row_details" => $row['row_details']
        );
    }
}

## Response
$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $tbl_data
);

echo json_encode($response);
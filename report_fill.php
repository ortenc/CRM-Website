<?php
// Database configuration
include 'database.php';

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
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

// Total number of record with filtering
$query_select_users = "select count(*) as allcount from users INNER JOIN checkins ON users.id=checkins.user_id WHERE 1=1 " . $searchQuery;
$sel = mysqli_query($conn, $query_select_users);
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

// Fetch records
$empQuery = "SELECT 
                    users.id,
                    name,
                    surname,
                    check_in_date,
                    check_in_hour,
                    check_out_hour
             FROM users
             LEFT JOIN checkins ON users.id=checkins.user_id WHERE 1=1 " . $searchQuery . " order by " . $columnName . " " . $columnSortOrder . " limit " . $row . "," . $rowperpage;


$empRecords = mysqli_query($conn, $empQuery);
$data = array();
$num_id = 1;
while ($row = mysqli_fetch_assoc($empRecords)) {
//    $data[$row['check_in_date']][$row['id']]['id'] = $row['id'];
//    print_r($row_id);
//    exit;
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
    $data[$row['check_in_date']][$row['id']]['row_details'][$check_in]['name'] = $row['name'];
    $data[$row['check_in_date']][$row['id']]['row_details'][$check_in]['surname'] = $row['surname'];
//    $data[$row['check_in_date']][$row['id']]['row_details'][$check_out] = $check_in;



    $check_in_cal = 0;
    $check_out_cal = 0;
    foreach ($data[$row['check_in_date']][$row['id']]['check_ins'] as $check_in_key => $check_in_record ) {
        $check_in_cal += strtotime($data[$row['check_in_date']][$row['id']]['check_outs'][$check_in_key]) - strtotime($data[$row['check_in_date']][$row['id']]['check_ins'][$check_in_key]);
    }

    $data[$row['check_in_date']][$row['id']]['time_in'] = $check_in_cal;
    $data[$row['check_in_date']][$row['id']]['out_time'] = $check_out_cal;
    $check_out_cal = strtotime($data[$row['check_in_date']][$row['id']]['all_checks'][count($data[$row['check_in_date']][$row['id']]['all_checks']) - 1]) - strtotime($data[$row['check_in_date']][$row['id']]['all_checks'][0]) - $check_in_cal;

//    $temp = array();
//
//    $temp['check_in_date'] = $row['check_in_date'];
//    $temp['name'] = $row['name'];
//    $temp['surname'] = $row['surname'];
//    $temp[$row['check_in_date']]['check_in_hours'][] = $row['check_in_hour'];
//    $temp[$row['check_in_date']]['check_out_hours'][] = $row['check_out_hour'];
//    $temp['check_in_hour'] = $row['check_in_hour'];
//    $temp['check_out_hour'] = $row['check_out_hour'];
//
//    $diff1 = new DateTime($temp['check_in_hour']);
//    $diff2 = new DateTime($temp['check_out_hour']);
//    $totaldiff = $diff2->diff($diff1);
//    $temp['nrofhoursin'] = $totaldiff->format('%h:%i:%s');
//
//    $data[] = $temp;

}
//echo "<pre>";
//print_r($data);
//echo "</pre>";


foreach ($data as $date => $data_row) {
    foreach ($data_row as $user_id => $row) {

//        echo "<pre>";
//        print_r($row);
//        echo "</pre>";

        $tbl_data[] = array(
            "DT_RowId" => "row_" . $row['id'],
            'actions' => "<span id = 'expand_row_" . $row['id'] . "'> <div class='row-center'>
            <button class='btn btn-primary details-control fa fa-plus' p></button></span>",
            "name" => $row['name'],
            "surname" => $row['surname'],
            "date" => $date,
            "hours_in" => $row['time_in'],
            "hours_out" => $row['out_time'],
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
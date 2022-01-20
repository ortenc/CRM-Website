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
$searchValue = mysqli_real_escape_string($conn,$_POST['search']['value']); // Search value

// Search
$searchQuery = " ";
if($searchValue != ''){
    $searchQuery = " and (name like '%".$searchValue."%' or 
        surname like '%".$searchValue."%' or 
        phone like '%".$searchValue."%' ) ";
}

// Total number of records without filtering
$sel = mysqli_query($conn,"select count(*) as allcount from users INNER JOIN checkins ON users.id=checkins.user_id");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

// Total number of record with filtering
$query_select_users = "select count(*) as allcount from users INNER JOIN checkins ON users.id=checkins.user_id WHERE 1=1 ".$searchQuery;
$sel = mysqli_query($conn,$query_select_users);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

if($columnName=='date'){
    $columnName='check_in_date';
}
if($columnName=='hours_in'){
    $columnName='check_in_hour';
}
if($columnName=='hours_out'){
    $columnName='check_out_hour';
}

// Fetch records
$empQuery = "SELECT name, surname, check_in_date, check_in_hour, check_out_hour
FROM users
INNER JOIN checkins ON users.id=checkins.user_id WHERE 1=1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

$empRecords = mysqli_query($conn, $empQuery);
$data = array();
while ($row = mysqli_fetch_assoc($empRecords)) {
    $temp = array();

    $temp['check_in_date'] = $row['check_in_date'];
    $temp['name'] = $row['name'];
    $temp['surname'] = $row['surname'];
    $temp[$row['check_in_date']]['check_in_hours'][] = $row['check_in_hour'];
    $temp[$row['check_in_date']]['check_out_hours'][] = $row['check_out_hour'];
    $temp['check_in_hour'] = $row['check_in_hour'];
    $temp['check_out_hour'] = $row['check_out_hour'];

    $diff1 = new DateTime($temp['check_in_hour']);
    $diff2 = new DateTime($temp['check_out_hour']);
    $totaldiff = $diff2 ->diff($diff1);
    $temp['nrofhoursin'] = $totaldiff->format('%h:%i:%s');

    $data[] = $temp;

}

print_r($data);


foreach ($data as $date => $row){

    $tbl_data[] = array(
        "name"=>$row['name'],
        "surname"=>$row['surname'],
        "date"=>$row['check_in_date'],
        "hours_in"=>$row['nrofhoursin'],
        "hours_out"=>$row['check_out_hour']
    );
}

//print_r($data);
//print_r($diff1);
//exit;

## Response
$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $tbl_data
);

echo json_encode($response);
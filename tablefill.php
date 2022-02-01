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
        phone like '%".$searchValue."%' or 
        role like '%".$searchValue."%' or 
        gender like '%".$searchValue."%' or 
        email like'%".$searchValue."%' ) ";
}


if (!empty($_POST['p_flt_reg_date_start'])) {
    $date_filter = explode(" - ", $_POST['p_flt_reg_date_start']);
    $flt_reg_date_start = mysqli_real_escape_string($conn, $date_filter[0]);
    $flt_reg_date_end = mysqli_real_escape_string($conn, $date_filter[1]);
} else {
    $flt_reg_date_start = date('Y-m-d');
    $flt_reg_date_end = date('Y-m-d');
}

if (!empty($_POST['flt_semail'])) {
    $flt_semail = mysqli_real_escape_string($conn,$_POST['flt_semail']);
    $searchQuery = " and ( email like'%".$flt_semail."%' ) ";
}
$flt_sphone = mysqli_real_escape_string($conn,$_POST['flt_sphone']);

if($flt_reg_date_start != '' && $flt_reg_date_end != ''){
    $searchQuery .= " and (created_at between '".$flt_reg_date_start."' and '".$flt_reg_date_end."' ) ";
}

if($flt_sphone != ''){
    $searchQuery = " and ( phone like'%".$flt_sphone."%' ) ";
}


// Total number of records without filtering
$sel = mysqli_query($conn,"select count(*) as allcount from users");

$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

// Total number of record with filtering
$query_select_users = "select count(*) as allcount from users WHERE 1=1 ".$searchQuery;
$sel = mysqli_query($conn,$query_select_users);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

// Fetch records
$empQuery = "select * from users WHERE 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $empQuery);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
    if (empty($row['photo'])) {
        $row['photo'] = 'photos/default.jpg';
    }
    $data[] = array(
        "photo"=>$row['photo'],
        "name"=>$row['name'],
        "surname"=>$row['surname'],
        "atesia"=>$row['atesia'],
        "username"=>$row['username'],
        "email"=>$row['email'],
        "phone"=>$row['phone'],
        "role"=>$row['role'],
        "gender"=>$row['gender'],
        "created_at"=>$row['created_at'],
        "actions"=> "<center><nobr>	
                            <div class='btn-group' style='width:130px'>	
                                <input type='button' class='btn btn-primary' value='Update' data-toggle='modal' data-target='#edit_premission' onclick='fill_modal_user_data(\"$row[id]\")'>	
                            <div class='btn-group' style='width:130px'>	
                                <input type='button' class='btn btn-danger' value='Delete' onclick='fill_user_delete(\"$row[id]\")'>	
                            </div>	
                        </nobr></center>"
    );
}

## Response
$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);

echo json_encode($response);
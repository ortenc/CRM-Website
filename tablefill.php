<?php
## Database configuration
include 'database.php';

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($conn,$_POST['search']['value']); // Search value

## Search
$searchQuery = " ";
if($searchValue != ''){
    $searchQuery = " and (name like '%".$searchValue."%' or 
        surname like '%".$searchValue."%' or 
        email like'%".$searchValue."%' ) ";
}

## Total number of records without filtering
$sel = mysqli_query($conn,"select count(*) as allcount from users");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($conn,"select count(*) as allcount from users WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from users WHERE 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $empQuery);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array(
        "name"=>$row['name'],
        "surname"=>$row['surname'],
        "email"=>$row['email'],
        "role"=>$row['role'],
        "gender"=>$row['gender'],
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
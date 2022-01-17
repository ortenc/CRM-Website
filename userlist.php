<?php
include('functions.php');
?>
<?php
session_start();

if(!$_SESSION['id'])
{
    header('location : login.php');
}

$user_id = $_SESSION['id'];
$user_role = $_SESSION['role'];

require('database.php');

$query_photo ="SELECT * FROM users WHERE id= '$user_id'";

$result_photo = mysqli_query($conn, $query_photo);
$user = mysqli_fetch_assoc($result_photo);


// insert profile picture

if(isset($_FILES["profile_photo"]["name"])){
    $target_dir = "photos/";
    $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
        $sql = "UPDATE users SET photo = '$target_file' WHERE id = '$user_id' ";
        $rs = mysqli_query($conn,$sql);
        header("Location: profile.php");
    } else {
        echo "Sorry, there was an error uploading your file.";
        exit;
    }
}


// Store session id for user chat

$user_id_list = $_SESSION['id'];

$query_list = "SELECT * FROM users WHERE id != '".$user_id_list."'";
$result_list = mysqli_query($conn, $query_list);

if (!$result_list) {
    echo "Internal server error";
    exit;
}

// Display all users

$query_list = "SELECT * FROM users WHERE 1=1";
$result_list = mysqli_query($conn,$query_list);
$users = [];
while($row = mysqli_fetch_assoc($result_list)){
    $users[$row['id']]['id']= $row['id'];
    $users[$row['id']]['name']= $row['name'];
    $users[$row['id']]['surname'] = $row['surname'];
    $users[$row['id']]['atesia'] = $row['atesia'];
    $users[$row['id']]['username'] = $row['username'];
    $users[$row['id']]['email'] = $row['email'];
    $users[$row['id']]['phone'] = $row['phone'];
    $users[$row['id']]['gender'] = $row['gender'];
    $users[$row['id']]['role'] = $row['role'];
}
?>
<!DOCTYPE html>
<html>

<head>

    <title>Userlist</title>

<?php include "header.php"; ?>

</head>

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
                            <h5>List of users registered into the system</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover dataTables-example" id="emptable" >
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Surname</th>
                                        <th>Atesia</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Phone Number</th>
                                        <th>Role</th>
                                        <th>Gender</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                </table>
                                <div class='btn-group' style='width:130px'>
                                    <input type='button' class='btn btn-block blue-bg' value='Add New User' data-toggle='modal' data-target='#edit_premission' onclick='create()'>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<!--modal for user info update-->

<div class="modal inmodal" id="update_user_data" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <i class="fa fa-user modal-icon"></i>
                <h4 class="modal-title">Update user</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="user_modal_id" name="user_modal_id">
                <div class="form-group"><label>Name</label>
                    <input type="text" class="form-control" id="fname" name="fname"></div>
                <div class="form-group"><label>Surname</label>
                    <input type="text" class="form-control" id="surname" name="surname"></div>
                <div class="form-group"><label>Atesia</label>
                    <input type="text" class="form-control" id="atesia" name="atesia"></div>
                <div class="form-group"><label>Username</label>
                    <input type="text" class="form-control" id="username" name="username"></div>
                <div class="form-group"><label>Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone"></div>
                <div class="form-group"><label>Email</label>
                    <input type="text" class="form-control" id="email" name="email"></div>
                <b>Select role</b>
                <select class="form-control" name="role" id="role">
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" name="update" onclick="update()">Save changes</button>
            </div>
        </div>
    </div>


    <!--modal for user Creation-->

</div><div class="modal inmodal" id="create_user_data" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <i class="fa fa-user modal-icon"></i>
                <h4 class="modal-title">Update user</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="user_modal_id" name="user_modal_id">
                <div class="form-group"><label>Name</label>
                    <input type="text" class="form-control" id="firstname" name="firstname"></div>
                <div class="form-group"><label>Surname</label>
                    <input type="text" class="form-control" id="lastname" name="lastname"></div>
                <div class="form-group"><label>Atesia</label>
                    <input type="text" class="form-control" id="fathername" name="fathername"></div>
                <div class="form-group"><label>Username</label>
                    <input type="text" class="form-control" id="user" name="user"></div>
                <div class="form-group"><label>Phone Number</label>
                    <input type="text" class="form-control" id="telephone" name="telephone"></div>
                <div class="form-group"><label>Date of birth</label>
                    <input type="text" class="form-control datepicker" id="birthday" name="birthday"></div>
                <div class="form-group"><label>Select Gender</label>
                <select class="form-control" name="gender" id="gender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select></div>
                <div class="form-group"><label>Email</label>
                    <input type="text" class="form-control" id="mail" name="mail"></div>
                <div class="form-group"><label>Password</label>
                    <input type="text" class="form-control" id="password1" name="password1"></div>
                <div class="form-group"><label>Re-Password</label>
                    <input type="text" class="form-control" id="password2" name="password2"></div>
                <b>Select role</b>
                <select class="form-control" name="roles" id="roles">
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" name="create" onclick="create()">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!--modal for user info delete-->

<div class="modal inmodal" id="delete_user_data" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <i class="fa fa-warning modal-icon"></i>
                <h4 class="modal-title">Delete User</h4>
                <h4 class="font-bold">Are you sure you want to delete user (<span id="d_fname"></span>)?</h4>
                <input type="hidden" id="user_modal_id_delete" name="user_modal_id_delete">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" name="update" onclick="delete_user()">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>

<script>

    $(function () {
        $('.datepicker').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            calendarWeeks: true,
            autoclose: true,
            dateFormat: "yy-mm-dd",
            changeYear: true,
            changeMonth: true
        });
    });

    function create(){
        $("#create_user_data").modal("show");


    }

    function fill_modal_user_data(user_id) {
        $("#update_user_data").modal("show");

        var data = {
            "action": "fill_modal_user_data",
            user_id : user_id
        };

        $.ajax({
            url: "ajax.php",
            method: 'POST',
            data: data,
            cache: false,
            success: function(result){
                var response = JSON.parse(result);
                if (response.code == 200) {
                    $("#fname").val(response.name);
                    $("#surname").val(response.surname);
                    $("#atesia").val(response.atesia);
                    $("#username").val(response.username);
                    $("#phone").val(response.phone);
                    $("#email").val(response.email);
                    $("#role").val(response.role);
                    $("#user_modal_id").val(response.id);
                }

                if (response.code == 422) {
                    alert.fire(response.message);
                }

            }
        });

    }
    function fill_user_delete(user_id_delete) {
        $("#delete_user_data").modal("show");

        var data = {
            "action": "fill_user_delete",
            user_id : user_id_delete
        };

        $.ajax({
            url: "ajax.php",
            method: 'POST',
            data: data,
            cache: false,
            success: function(result){
                var response = JSON.parse(result);
                if (response.code == 200) {
                    $("#d_fname").text(response.name +' '+response.surname);
                    $("#user_modal_id_delete").val(response.id);
                }

                if (response.code == 422) {
                    Swal.fire(response.message);
                }



            }
        });

    }

    $(document).ready(function () {

        $('#emptable').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            serverMethod: 'POST',
            ajax: {
                url: "tablefill.php",
            },
            columns: [
                {data: "name"},
                {data: "surname"},
                {data: "atesia"},
                {data: "username"},
                {data: "email"},
                {data: "phone"},
                {data: "role"},
                {data: "gender"},
                {
                    data: "actions",
                    // render: function(data, meta, row) {
                    //     return '<button>Test</button>';
                    // }
                }

            ],
        });

    });


    function isEmpty(value) {
        return typeof value == 'string' && !value.trim() || typeof value == 'undefined' || value === null;
    }

    function update() {
        var user_id = $("#user_modal_id").val();
        var fname = $("#fname").val();
        var lname = $("#surname").val();
        var atesia = $("#atesia").val();
        var username = $("#username").val();
        var phone = $("#phone").val();
        var email = $("#email").val();
        var role = $("#role").val();

        var data = {
            "action": "update",
            "id": user_id,
            "name": fname,
            "surname": lname,
            "atesia": atesia,
            "username": username,
            "phone": phone,
            "email": email,
            "role": role
        };

        $.ajax({
            url: "ajax.php",
            method: 'POST',
            type: 'POST',
            data: data,
            cache: false,
            success: function(result){
                var response = JSON.parse(result);

                if (response.code == 200) {
                    window.location.href = "userlist.php";
                }

                if (response.code == 404) {
                    Swal.fire(response.message);
                }



            }
        });
    }
    function delete_user() {

        var user_id = $("#user_modal_id_delete").val();

        var data = {
            "action": "erase",
            "id": user_id

        };

        $.ajax({
            url: "ajax.php",
            method: 'POST',
            type: 'POST',
            data: data,
            cache: false,
            success: function (result) {
                var response = JSON.parse(result);

                if (response.code == 200) {
                    window.location.href = "userlist.php";
                }

                if (response.code == 404) {
                    Swal.fire(response.message);
                }


            }
        });
    }

</script>

</body>

</html>

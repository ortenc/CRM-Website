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

// Store all users except for the one logged in inside an array

$user_list = array();
while($row = mysqli_fetch_assoc($result_list)) {
    $tmp = array();

    $tmp["id"] = $row["id"];
    $tmp["name"] = $row["name"];

    $user_list[$row['id']] = $tmp;

}

$query_list = "SELECT * FROM users WHERE 1=1";

if(!empty($_POST['filterByGender'])) {
    $query_list .= " AND gender = '".mysqli_real_escape_string($conn, $_POST['filterByGender'])."'";
}

if(!empty($_POST['filterByName'])) {
    $data_name = explode(' ', $_POST['filterByName']);
    $query_list .= " AND name = '".mysqli_real_escape_string($conn, $data_name[0])."' AND surname ='".mysqli_real_escape_string($conn, $data_name[1])."'";
}


$result_list = mysqli_query($conn,$query_list);
$users = [];
while($row = mysqli_fetch_assoc($result_list)){
    $users[$row['id']]['id']= $row['id'];
    $users[$row['id']]['name']= $row['name'];
    $users[$row['id']]['surname'] = $row['surname'];
    $users[$row['id']]['email'] = $row['email'];
    $users[$row['id']]['gender'] = $row['gender'];
    $users[$row['id']]['role'] = $row['role'];
}
?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Userlist</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="css/plugins/dataTables/datatables.min.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

</head>

<body>

<div id="wrapper">

    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                        <span>
                            <img alt="image" class="img-circle" src="<?= $user['photo']?>"/>
                        </span>
                        <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?= $_SESSION['name']?></strong>
                             </span> <span class="text-muted text-xs block"><?= $user['role']?><b class="fa fa-user"></b></span> </span>
                    <div class="logo-element">
                        IN+
                    </div>
                </li>
                <li class="active">
                    <a href="profile.php"><i class="fa fa-user"></i> <span class="nav-label">Profile</span></a>
                </li>
                <?php if($user_role == "Admin") { ?>
                    <li class="active">
                        <a href="userlist.php"><i class="fa fa-table"></i> <span class="nav-label">Admin</span></a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </nav>

    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li>
                        <a href="login.php">
                            <i class="fa fa-sign-out"></i> Log out
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>User-List</h2>
            </div>
            <div class="col-lg-2">
            </div>
        </div>
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
                                <table class="table table-striped table-bordered table-hover dataTables-example" >
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Surname</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Gender</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($users as $user){ ?>
                                    <tr class="gradeX">
                                        <td>
                                            <input type="text" id="fname_<?= $user['id']?>" name="fname" value="<?= $user['name']?>">
                                        </td>
                                        <td>
                                            <input type="text" id="lname_<?= $user['id']?>" name="lname" value="<?= $user['surname']?>">
                                        </td>
                                        <td>
                                            <input type="text" id="email_<?= $user['id']?>" name="email" value="<?= $user['email']?>">
                                        </td>
                                        <td class="center">
                                            <select class="form-control" name="role" id="role_<?= $user['id']?>">
                                                <option value="<?= $user['role']?>"><?= $user['role']?></option>
                                                <option value="Admin">Admin</option>
                                                <option value="User">User</option>
                                            </select>
                                        </td>
                                        <td class="center"><?= $user['gender']?></td>
                                        <td style="white-space: nowrap">
                                            <button class="btn btn-primary w-50" name="update" onclick="update('<?= $user['id']?>')">Update</button>
                                            <button class="btn btn-primary w-50" name="erase" onclick="erase('<?= $user['id']?>')">Delete</button>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Mainly scripts -->
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<script src="js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="js/inspinia.js"></script>
<script src="js/plugins/pace/pace.min.js"></script>

<!-- Page-Level Scripts -->
<script>
    $(document).ready(function(){
        $('.dataTables-example').DataTable({
            pageLength: 25,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                { extend: 'copy'},
                {extend: 'csv'},
                {extend: 'excel', title: 'ExampleFile'},
                {extend: 'pdf', title: 'ExampleFile'},

                {extend: 'print',
                    customize: function (win){
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');

                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    }
                }
            ]

        });

    });

    function isEmpty(value) {
        return typeof value == 'string' && !value.trim() || typeof value == 'undefined' || value === null;
    }

    function update(id) {
        var user_id = id;
        var fname = $("#fname_"+user_id).val();
        var lname = $("#lname_"+user_id).val();
        var email = $("#email_"+user_id).val();
        var role = $("#role_"+user_id).val();

        var data = {
            "action": "update",
            "id": user_id,
            "name": fname,
            "surname": lname,
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
    function erase(id) {
        var user_id = id;

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

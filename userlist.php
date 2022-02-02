<?php
include('functions.php');
?>
<?php
session_start();

if(!$_SESSION['id'])
{
    header('location : login.php');
}
require('database.php');

?>
<!DOCTYPE html>
<html>

<head>

    <title>Userlist</title>

<?php
include "header.php";
?>

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
                            <h5>List of users registered into the system</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <form method="post">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label>Date from</label>
                                        <?php
                                        if (empty($_POST['flt_reg_date_start'])) {
                                            $_POST['flt_reg_date_start'] = date('Y-m-d') ." - ". date('Y-m-d');
                                        }
                                        ?>
                                        <input class="form-control datepicker filter_date_picker"
                                               type="text"
                                               id="flt_reg_date_start"
                                               name="flt_reg_date_start"
                                               value="<?= $_POST['flt_reg_date_start'] ?>"
                                               autocomplete="off">
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Email</label>
                                        <select class="select2_email form-control" id="semail" name="semail">
                                            <?php if (!empty($_POST['semail'])) { ?>
                                                <option value="<?=$_POST['semail']?>"><?=$_POST['semail']?></option>
                                           <?php } ?>
                                            <option value=""></option>
                                            <?php
                                            $query_email = "SELECT DISTINCT email FROM users";
                                            $result_email = mysqli_query($conn, $query_email);
                                            while($row = mysqli_fetch_assoc($result_email)) { ?>
                                                <option value="<?=$row['email']?>"><?=$row['email']?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Phone</label>
                                        <select class="select2_phone form-control" id="sphone" name="sphone">
                                            <?php if (!empty($_POST['sphone'])) { ?>
                                                <option value="<?=$_POST['sphone']?>"><?=$_POST['sphone']?></option>
                                            <?php } ?>
                                            <option value=""></option>
                                            <?php
                                            $query_phone = "SELECT DISTINCT phone FROM users";
                                            $result_phone = mysqli_query($conn, $query_phone);
                                            while($row = mysqli_fetch_assoc($result_phone)) { ?>
                                                <option value="<?=$row['phone']?>"><?=$row['phone']?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-sm-6">
<!--                                        <input type="submit" class="btn btn-primary" name="filter" id="filter" value="Filter">-->
                                        <input type='button' class="btn btn-primary" id="filter" value="Search">
                                    </div>
                                    <div class="col-sm-6">
                                    <div class="btn-group create_user_button_id" style='width:150px'>
                                        <input type='button' class='btn btn-block blue-bg' value='Add New User' data-toggle='modal' data-target='#edit_premission' onclick='showcreatemodal()'>
                                    </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="ibox-content">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover dataTables" id="emptable" >
                                    <thead>
                                    <tr>
                                        <th>Profile picture</th>
                                        <th>Name</th>
                                        <th>Surname</th>
                                        <th>Atesia</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Phone Number</th>
                                        <th>Role</th>
                                        <th>Gender</th>
                                        <th>Date registered</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                </table>
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
                <div class="form-group"><label>Name<p id="errorufname" style="color: red"></p></label>
                    <input type="text" class="form-control" id="fname" name="fname"></div>
                <div class="form-group"><label>Surname<p id="errorulname" style="color: red"></p></label>
                    <input type="text" class="form-control" id="surname" name="surname"></div>
                <div class="form-group"><label>Atesia<p id="erroruatesia" style="color: red"></p></label>
                    <input type="text" class="form-control" id="atesia" name="atesia"></div>
                <div class="form-group"><label>Username<p id="errorusername" style="color: red"></p></label>
                    <input type="text" class="form-control" id="username" name="username"></div>
                <div class="form-group"><label>Phone Number<p id="erroruphonenumber" style="color: red"></p></label>
                    <input type="text" class="form-control" id="phone" name="phone"></div>
                <div class="form-group"><label>Email<p id="erroruemail" style="color: red"></p></label>
                    <input type="text" class="form-control" id="email" name="email"></div>
                <b>Select role</b>
                <select class="form-control" name="role" id="role">
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>
                <p id="errorurole" style="color: red"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" name="update" onclick="update()">Save changes</button>
            </div>
        </div>
    </div>
</div>


<!--modal for user Creation-->

<div class="modal inmodal" id="create_user_data" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <i class="fa fa-user modal-icon"></i>
                <h4 class="modal-title">Create user</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="user_modal_id" name="user_modal_id">
                <div class="form-group"><label>Name</label>
                    <input type="text" class="form-control" id="firstname" name="firstname">
                    <p id="errorcfname" style="color: red"></p>
                </div>
                <div class="form-group"><label>Surname</label>
                    <input type="text" class="form-control" id="lastname" name="lastname">
                    <p id="errorclname" style="color: red"></p>
                </div>
                <div class="form-group"><label>Atesia</label>
                    <input type="text" class="form-control" id="fathername" name="fathername">
                    <p id="errorcatesia" style="color: red"></p>
                </div>
                <div class="form-group"><label>Phone Number</label>
                    <input type="text" class="form-control" id="telephone" name="telephone">
                    <p id="errorcphone" style="color: red"></p>
                </div>
                <div class="form-group"><label>Date of birth</label>
                    <input type="text" class="form-control datepicker" id="birthday" name="birthday" autocomplete="off">
                    <p id="errorcbirth" style="color: red"></p>
                </div>
                <div class="form-group"><label>Select Gender</label>
                    <select class="form-control" name="gender" id="gender">
                        <option disabled selected value> -- select gender -- </option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                    <p id="errorcgender" style="color: red"></p>
                </div>
                <div class="form-group"><label>Email</label>
                    <input type="text" class="form-control" id="mail" name="mail">
                    <p id="errorcemail" style="color: red"></p>
                </div>
                <div class="form-group"><label>Password</label>
                    <input type="password" class="form-control" id="password1" name="password1" autocomplete="off">
                    <p id="errorcpass1" style="color: red"></p>
                </div>
                <div class="form-group"><label>Re-Password</label>
                    <input type="password" class="form-control" id="password2" name="password2" autocomplete="off">
                    <p id="errorcpass2" style="color: red"></p>
                </div>
                <b>Select role</b>
                <select class="form-control" name="roles" id="roles">
                    <option disabled selected value> -- select role -- </option>
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>
                <p id="errorcrole" style="color: red"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" name="create" onclick="create()">Create User</button>
            </div>
        </div>
    </div>
</div>

<!--modal for user info delete-->

<div class="modal inmodal" id="delete_user_data" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated fadeIn">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <i class="fa fa-warning modal-icon"></i>
                <h4 class="modal-title">Delete User</h4>
                <h4 class="font-bold">Are you sure you want to delete user (<span id="d_fname"></span>)?</h4>
                <input type="hidden" id="user_modal_id_delete" name="user_modal_id_delete">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal" onclick="notification()">Close</button>
                <button type="button" class="btn btn-primary" name="update" onclick="delete_user()">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>

<script>

    function isEmpty(value) {
        return typeof value == 'string' && !value.trim() || typeof value == 'undefined' || value === null;
    }

    function showcreatemodal(){
        $("#create_user_data").modal("show");
    }

    function validate_data(val, validation_rule, message, error_field ){

        if (isEmpty(val)) {
            error = "*This field is required.";
            $("#"+error_field).text(error);
            return false;
        }else{
            error = "";
            $("#"+error_field).text(error);
        }

        if (!validation_rule.test(val)) {
            error = message;
            $("#"+error_field).text(error);
            return false;
        }else{
            error = "";
            $("#"+error_field).text(error);
        }
    }


    function create() {

        var firstname = $("#firstname").val();
        var lastname = $("#lastname").val();
        var fathername = $("#fathername").val();
        var mail = $("#mail").val();
        var birthday = $('input[name="birthday"]').val();
        var telephone = $("#telephone").val();
        var date_change = birthday.replaceAll('/', '-');
        var password1 = $("#password1").val();
        var password2 = $("#password2").val();
        var gender = $("#gender").val();
        var role = $("#roles").val();
        var alphanumeric_validation = /^[a-zA-Z]{3,}$/;
        var phoneno_validation = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;
        var email_validation = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        var pass_validation = /^[a-zA-Z0-9!@#$%^&*.]{8,16}$/;
        var birthday_validation = /(((19|20)\d\d)\/(0[1-9]|1[0-2])\/((0|1)[0-9]|2[0-9]|3[0-1]))$/;



        /**
         * Validimi i fushave
         */

        // Validimi i Emrit
        validate_data(firstname, alphanumeric_validation, "Name should be only letters",  "errorcfname");

        // Validimi i mbiemrit
        validate_data(lastname, alphanumeric_validation, "Last name should be only letters.",  "errorclname");

        // Validimi Atesise
        validate_data(fathername, alphanumeric_validation, "Atesia should be only letters.",  "errorcatesia");

        // Validimi i numrit
        validate_data(telephone, phoneno_validation, "Phone should contain only numbers and 10-12 digits.",  "errorcphone");

        // Validimi i ditelindjes
        validate_data(birthday, birthday_validation , "Not correct format.",  "errorcbirth");

        // Validimi i Gjinise
        validate_data(gender, alphanumeric_validation , "Select a gender for the user.",  "errorcgender");

        // Validimi i Emailit
        validate_data(mail, email_validation, "Email should be .",  "errorcemail");

        // Validimi i Fushes se pare te passwordit
        validate_data(password1, pass_validation, "Password should contain 1 uper casse, lower casse and between 6 and 16 chars.",  "errorcpass1");

        // Validimi i Fushes se dyte te passwordit
        validate_data(password2, pass_validation , "Not the same password.",  "errorcpass2");

        // Validimi i Rolit
        validate_data(role, alphanumeric_validation , "Select a role for the user",  "errorcrole");

        $.ajax({
            url: "ajax.php",
            type: 'POST',
            data: {
                "action": "create",
                "fname": firstname,
                "lname": lastname,
                "atesia": fathername,
                "email": mail,
                "phone": telephone,
                "date_change": date_change,
                "password1": password1,
                "password2": password2,
                "gender": gender,
                "role": role
            },
            cache: false,
            success: function (result) {
                var response = JSON.parse(result);
                if (response.code == 200) {
                    window.location.href = "userlist.php";
                } else if (response.code == 422) {
                    window.alert(response.message)
                }
            }
        });
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

       var dataTable =  $('#emptable').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            serverMethod: 'POST',
            ajax: {
                url: "tablefill.php",
                data: function (data) {
                    // Read values
                    var flt_reg_date_start = $('#flt_reg_date_start').val();
                    var semail = $('#semail').val();
                    var sphone = $('#sphone').val();

                    // Append to data
                    data.p_flt_reg_date_start = flt_reg_date_start;
                    data.flt_semail = semail;
                    data.flt_sphone = sphone;
                }
            },
            columns: [
                {
                    data: "photo",
                    render: function (data, meta, row) {
                        return '<img src="' + data + '" alt="' + data + '"height="50" width="50"/>';
                    }
                },
                {data: "name"},
                {data: "surname"},
                {data: "atesia"},
                {data: "username"},
                {data: "email"},
                {data: "phone"},
                {data: "role"},
                {data: "gender"},
                {data: "created_at"},
                {data: "actions",}

            ],
        });

        $('#filter').click(function () {
            dataTable.draw();

        });

        var dateToday = new Date();
        $('.filter_date_picker').daterangepicker({
            today: dateToday,
            todayHighlight: true,
            format: "YYYY/MM/DD",
        });

        var dateBirthday = new Date();
        $('.datepicker').datepicker({
            today: dateBirthday,
            todayHighlight: true,
            format: "yyyy/mm/dd",
        });

        $(".select2_email").select2({
            placeholder: "Select email",
            width: "100%",
            allowClear: true
        });

        $(".select2_phone").select2({
            placeholder: "Select phone",
            width: "100%",
            allowClear: true
        });
    });

    function update() {
        var user_id = $("#user_modal_id").val();
        var fname = $("#fname").val();
        var lname = $("#surname").val();
        var atesia = $("#atesia").val();
        var username = $("#username").val();
        var phone = $("#phone").val();
        var email = $("#email").val();
        var role = $("#role").val();
        var alphanumeric_validation = /^[a-zA-Z]{3,}$/;
        var phoneno_validation = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;
        var email_validation = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        var pass_validation = /^[a-zA-Z0-9!@#$%^&*.]{8,16}$/;
        var birthday_validation = /(((19|20)\d\d)\/(0[1-9]|1[0-2])\/((0|1)[0-9]|2[0-9]|3[0-1]))$/;

        /**
         * Validimi i fushave
         */


        // Validimi i emrit
        validate_data(fname, alphanumeric_validation , "Name should contain only letters",  "errorufname");

        // Validimi i mbiemrit
        validate_data(lname, alphanumeric_validation , "Surname should contain only letters",  "errorulname");

        // Validimi i atesise
        validate_data(atesia, alphanumeric_validation , "Atesia should contain only letters",  "erroruatesia");

        // Validimi i username
        validate_data(username, alphanumeric_validation , "Atesia should contain only letters",  "errorusername");

        // Validimi i telefonit
        validate_data(phone, phoneno_validation , "Phone wrong format",  "erroruphonenumber");

        // Validimi i emailit
        validate_data(email, email_validation , "Email wrong format",  "erroruemail");

        // Validimi i rolit
        validate_data(role, alphanumeric_validation , "Select role for user",  "errorurole");

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
                if (response.code == 422) {
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
                    Swal.fire("User deleted successfully",response.message,"success");
                    setTimeout(function(){
                        location.reload();
                    },2000);
                }

                if (response.code == 404) {
                    Swal.fire(response.message);
                }
            }
        });
    }

    function notification() {
        Swal.fire("Canceled","",'error');
    }

</script>

</body>

</html>

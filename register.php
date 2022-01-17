<!DOCTYPE html>
<html>

<head>

    <title>INSPINIA | Register</title>

    <?php include "header.php"; ?>

</head>

<body class="gray-bg">

<div class="middle-box text-center loginscreen   animated fadeInDown">
    <div>
<!--        <div>-->
<!--            <h5 class="logo-name">LW</h5>-->
<!---->
<!--        </div>-->
        <h3>Register to Local Web</h3>
        <p>Create account to see it in action.</p>

        <div class="form-group">
            <input type="text" class="form-control" placeholder="Name" id="fname" required="">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Surname" id="lname" required="">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Atesia" id="atesia" required="">
        </div>
        <div class="form-group">
            <input type="email" class="form-control" placeholder="Email" id="email" required="">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Phone" id="phone" required="">
        </div>
        <div class="form-group">
            <input type="text" class="form-control datepicker" placeholder="YY-MM-DD" id="birthday" name="birthday" required="">
        </div>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="Password" id="password1" required="">
        </div>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="Re-Password" id="password2" required="">
        </div>
        <div class="form-group">
            <label class="text m-r-45">Gender</label>
            <div class="p-t-10">
                <label class="radio-container">Male
                    <input class="form-check-input" type="radio" name="gender" value="male" required checked>
                    <span class="checkmark"></span>
                </label>
                <label class="radio-container">Female
                    <input class="form-check-input" type="radio" name="gender" value="female" required>
                    <span class="checkmark"></span>
                </label>
            </div>
        </div>
        <button type="button" class="btn btn-primary block full-width m-b" onclick="register()">Register</button>
        <p
                class="form-control" id="errorid">
        </p>
        <p class="text-muted text-center"><small>Already have an account?</small></p>
        <a class="btn btn-sm btn-white btn-block" href="login.php">Login</a>
    </div>
</div>

<?php include 'footer.php' ?>

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

    $(document).ready(function () {
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
    });

    function isEmpty(value) {
        return typeof value == 'string' && !value.trim() || typeof value == 'undefined' || value === null;
    }

    function register() {
        var fname = $("#fname").val();
        var lname = $("#lname").val();
        var atesia = $("#atesia").val();
        var email = $("#email").val();
        var birthday = $('input[name="birthday"]').val();
        var phone = $("#phone").val();
        var date_change = birthday.replaceAll('/', '-');
        var password1 = $("#password1").val();
        var password2 = $("#password2").val();
        var gender = $('input[name=gender]:checked').val();

        /**
         * Validojme emrin
         */

        if (isEmpty(fname)) {
            error = "Name must be entered.";
            $("#errorid").text(error);
            return false;
        }
        filter_name = /^[a-zA-Z\s]+$/;
        if (!filter_name.test(fname)) {
            error = "name should be only letters.";
            $("#errorid").text(error);
            return false;
        }if (isEmpty(lname)) {
            error = "Surname must be entered.";
            $("#errorid").text(error);
            return false;
        }if (!filter_name.test(lname)) {
            error = "last name should be only letters.";
            $("#errorid").text(error);
            return false;
        }if (isEmpty(atesia)) {
            error = "atesia must be entered.";
            $("#errorid").text(error);
            return false;
        }if (!filter_name.test(atesia)) {
            error = "atesia should be only letters.";
            $("#errorid").text(error);
            return false;
        }if (isEmpty(email)) {
            error = "Email must be entered.";
            $("#errorid").text(error);
            return false;
        }if (isEmpty(phone)) {
            error = "phone must be entered.";
            $("#errorid").text(error);
            return false;
        }
        var phoneno = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;
        if (!phoneno.test(phone)) {
            error = "Phone not correct format.";
            $("#errorid").text(error);
            return false;

        }if (isEmpty(date_change)) {
            error = "birthdate must be entered.";
            $("#errorid").text(error);
            return false;
        }
        filter_email = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!filter_email.test(email)) {
            error = "Email not correct format.";
            $("#errorid").text(error);
            return false;

        }if (isEmpty(password1)) {
            error = "Password1 must be entered.";
            $("#errorid").text(error);
            return false;
        }if (isEmpty(password2)) {
            error = "Password2 must be entered.";
            $("#errorid").text(error);
            return false;
        }if (password1 != password2) {
            error = "Password is not the same.";
            $("#errorid").text(error);
            return false;
        }if (password1 != password2) {
            error = "Password is not the same.";
            $("#errorid").text(error);
            return false;
        }
        var minNumberofChars = 6;
        var maxNumberofChars = 16;
        var regularExpression = /^[a-zA-Z0-9!@#$%^&*.]{6,16}$/;
        if (password1.length < minNumberofChars || password1.length > maxNumberofChars) {
            error = "Password should contain One upper case one lower case one special character and 8 min characters.";
            $("#errorid").text(error);
            return false;
        }if (!regularExpression.test(password1)) {
            alert("password should contain at least one number and one special character");
            return false;
        }

        $.ajax({
            url: "ajax.php",
            type: 'POST',
            data: {
                "action": "register",
                "fname": fname,
                "lname": lname,
                "atesia": atesia,
                "email": email,
                "phone": phone,
                "date_change": date_change,
                "password1": password1,
                "password2": password2,
                "gender": gender
            },
            cache: false,
            success: function (result) {
                var response = JSON.parse(result);
                if (response.code == 200) {
                    window.location.href = "login.php";
                } else if (response.code == 422) {
                    window.alert(response.message)
                }
            }
        });
    }

</script>
</body>
</html>

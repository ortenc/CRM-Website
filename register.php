<!DOCTYPE html>
<html>

<head>

    <title>INSPINIA | Register</title>

    <?php include "header.php"; ?>

</head>

<body class="gray-bg">

<div class="middle-box text-center loginscreen   animated fadeInDown">
    <div>

        <h3>Register to Local Web</h3>
        <p>Create account to see it in action.</p>

        <div class="form-group">
            <p id="errorfname" style="color: red"></p>
            <input type="text" class="form-control" placeholder="Name" id="fname" required="">
        </div>
        <div class="form-group">
            <p id="errorlname" style="color: red"></p>
            <input type="text" class="form-control" placeholder="Surname" id="lname" required="">
        </div>
        <div class="form-group">
            <p id="erroratesia" style="color: red"></p>
            <input type="text" class="form-control" placeholder="Atesia" id="atesia" required="">
        </div>
        <div class="form-group">
            <p id="erroremail" style="color: red"></p>
            <input type="email" class="form-control" placeholder="Email" id="email" required="">
        </div>
        <div class="form-group">
            <p id="errorphone" style="color: red"></p>
            <input type="text" class="form-control" placeholder="Phone" id="phone" required="">
        </div>
        <div class="form-group">
            <p id="errorbirthday" style="color: red"></p>
            <input type="text" class="form-control datepicker" placeholder="YY-MM-DD" id="birthday" name="birthday" required="">
        </div>
        <div class="form-group">
            <p id="errorpass1" style="color: red"></p>
            <input type="password" class="form-control" placeholder="Password" id="password1" required="">
        </div>
        <div class="form-group">
            <p id="errorpass2" style="color: red"></p>
            <input type="password" class="form-control" placeholder="Re-Password" id="password2" required="">
        </div>
        <div class="form-group">
            <p id="errorgender" style="color: red"></p>
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
        <p class="text-muted text-center"><small>Already have an account?</small></p>
        <a class="btn btn-sm btn-white btn-block" href="login.php">Login</a>
    </div>
</div>

<?php include 'footer.php' ?>

<script>

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
        var alphanumeric_validation = /^[a-zA-Z]{3,}$/;
        var phoneno_validation = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;
        var email_validation = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        var pass_validation = /^[a-zA-Z0-9!@#$%^&*.]{8,16}$/;
        var birthday_validation = /(((19|20)\d\d)\/(0[1-9]|1[0-2])\/((0|1)[0-9]|2[0-9]|3[0-1]))$/;

        /**
         * Validojme emrin
         */

        // Validojme emrin
        validate_data(fname, alphanumeric_validation, "Name should be only letters",  "errorfname");

        // Validojme mbiemrin
        validate_data(lname, alphanumeric_validation, "Surname should be only letters",  "errorlname");

        // Validojme atesine
        validate_data(atesia, alphanumeric_validation, "Atesia should be only letters",  "erroratesia");

        // Validojme emailin
        validate_data(email, email_validation, "Email wrong format",  "erroremail");

        // Validojme ditelindjen
        validate_data(birthday, birthday_validation, "Date wrong format",  "errorbirthday");

        // Validojme telefonin
        validate_data(phone, phoneno_validation, "Phone number wrong format",  "errorphone");

        // Validojme pass1
        validate_data(password1, pass_validation, "Password should be 8 or 16 letters long and contain one uper case and lower case edhe nji shenj piksimi",  "errorpass1");

        // Validojme pass2
        validate_data(password2, pass_validation, "Password should be 8 or 16 letters long and contain one uper case and lower case edhe nji shenj piksimi",  "errorpass2");

        // Validojme gjinine
        validate_data(gender, alphanumeric_validation, "Gjinia should not be empty",  "errorgender");

        /**
         * Bejme thirrjen ne ajax te te dhenave mbasi mbarojm kontrollin e te dhenave
         */

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
                    swal.fire(response.message)
                }
            }
        });
    }

</script>
</body>
</html>

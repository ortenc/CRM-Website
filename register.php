<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INSPINIA | Register</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

</head>

<body class="gray-bg">

<div class="middle-box text-center loginscreen   animated fadeInDown">
    <div>
        <div>
            <h1 class="logo-name">LW</h1>

        </div>
        <h3>Register to Local Web</h3>
        <p>Create account to see it in action.</p>

        <div class="form-group">
            <input type="text" class="form-control" placeholder="Name" id="fname" required="">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Surname" id="lname" required="">
        </div>
        <div class="form-group">
            <input type="email" class="form-control" placeholder="Email" id="email" required="">
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

<!-- Mainly scripts -->
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="js/plugins/iCheck/icheck.min.js"></script>
<script>
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
        var email = $("#email").val();
        var password1 = $("#password1").val();
        var password2 = $("#password2").val();
        var gender = $('input[name=gender]:checked').val();

        /**
         * Validojme emrin
         */

        if (isEmpty(fname)) {
            error = "Name must be entered.";
            document.getElementById("errorid").innerHTML = error;
            return false;
        }if (isEmpty(lname)) {
            error = "Surname must be entered.";
            document.getElementById("errorid").innerHTML = error;
            return false;
        }if (isEmpty(email)) {
            error = "Email must be entered.";
            document.getElementById("errorid").innerHTML = error;
            return false;
        }
        filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!filter.test(email)) {
            error = "Email not correct format.";
            document.getElementById("errorid").innerHTML = error;
            return false;
        }if (isEmpty(password1)) {
            error = "Password1 must be entered.";
            document.getElementById("errorid").innerHTML = error;
            return false;
        }if (isEmpty(password2)) {
            error = "Password2 must be entered.";
            document.getElementById("errorid").innerHTML = error;
            return false;
        }if(password1!=password2) {
            error = "Password is not the same.";
            document.getElementById("errorid").innerHTML = error;
            return false;
        }if(password1!=password2) {
            error = "Password is not the same.";
            document.getElementById("errorid").innerHTML = error;
            return false;
        }
        var minNumberofChars = 6;
        var maxNumberofChars = 16;
        var regularExpression  = /^[a-zA-Z0-9!@#$%^&*.]{6,16}$/;
        if(password1.length < minNumberofChars || password1.length > maxNumberofChars) {
            error = "Password should contain One upper case one lower case one special character and 8 min characters.";
            document.getElementById("errorid").innerHTML = error;
            return false;
        }if(!regularExpression.test(password1)) {
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
                "email": email,
                "password1": password1,
                "password2": password2,
                "gender": gender
            },
            cache: false,
            success: function (result) {
                var response = JSON.parse(result);
                if (response.code == 200) {
                    window.location.href = "login.php";
                }
                else if(response.code == 422){
                     window.alert(response.message)
                }
            }
        });
    }

</script>
</body>
</html>

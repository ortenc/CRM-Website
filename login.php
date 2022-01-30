<!DOCTYPE html>
<html>

<head>

    <title>INSPINIA | Login</title>
    <?php
    include "header.php";
    ?>

</head>

<body class="gray-bg">

<div class="middle-box text-center loginscreen animated fadeInDown">
    <div>
        <h3>Welcome to Ortenc Project</h3>
        <p>Login in.</p>
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Email or Phone" name="email" id="email" required="">
        </div>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="Password" name="password" id="password" required="">
        </div>
        <button type="submit" class="btn btn-primary block full-width m-b" onclick="login()">Login</button>
        <p class="form-control" id="errorid"></p>
        <p class="text-muted text-center">
            <small>Do not have an account?</small>
        </p>
        <a class="btn btn-sm btn-white btn-block" href="register.php">Create an account</a>
    </div>
</div>

<!-- Mainly scripts -->
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
    function isEmpty(value) {
        return typeof value == 'string' && !value.trim() || typeof value == 'undefined' || value === null;
    }
    function login() {
        var email = $("#email").val();
        var password = $("#password").val();
        let errors = 0;

        if (isEmpty(email)) {
            error = "Email must be entered.";
            $("#staticEmail").addClass("input-error");
            $("#errorid").text(error);
            errors++;
        } else {
            $("#staticEmail").removeClass("input-error");
        }
        if (isEmpty(password)) {
            error = "Password must be entered.";
            $("#password").addClass("input-error");
            $("#errorid").text(error);
            errors++;
        } else {
            $("#password").removeClass("input-error");
        }
        if (errors) {
            return false;
        }

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: {
                "action": "login",
                "email": email,
                "password": password
            },
            cache: false
        }).then(function (result) {
            var response = JSON.parse(result);
            if (response.code == 200) {
                window.location.href = "profile.php";
            }
            else if(response.code == 422){
                swal.fire(response.message)
            }
        });
    }
</script>

</body>

</html>

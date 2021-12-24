<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INSPINIA | Login</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">

</head>

<body class="gray-bg">

<div class="middle-box text-center loginscreen animated fadeInDown">
    <div>
        <div>

            <h1 class="logo-name">LW</h1>

        </div>
        <h3>Welcome to Ortenc Project</h3>
        <p>Login in.</p>
        <div class="form-group">
            <input type="email" class="form-control" placeholder="Email" name="email" id="email" required="">
        </div>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="Password" name="password" id="password" required="">
        </div>
        <button type="submit" class="btn btn-primary block full-width m-b" onclick="login()">Login</button>

        <p class="text-muted text-center"><small>Do not have an account?</small></p>
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
            $("#staticEmail").addClass("input-error");
            errors++;
        } else {
            $("#staticEmail").removeClass("input-error");
        }
        if (isEmpty(password)) {
            $("#password").addClass("input-error");
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
        }).then(function (response) {
            var response = JSON.parse(response);
            //TODO: Url te vije nga backend
            window.location.href = "profile.php";
        }).catch(function (response) {

        });
    }
</script>

</body>

</html>

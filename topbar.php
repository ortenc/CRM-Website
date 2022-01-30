<div id="page-wrapper" class="gray-bg">
    <div class="row border-bottom">
        <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i>
                </a>
            </div>
            <ul class="nav navbar-top-links navbar-right">
                <li>

                    <span class="badge badge-primary">
                        Active for: <span id="active_time"></span>
                    </span>
                </li>
                <li>
                    <a href="logout.php">
                        <i class="fa fa-sign-out"></i> Log out
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <script>
        setInterval(function () {
            $.ajax({
                url: "ajax.php",
                method: "POST",
                data: {"action": "last_active_time"},
                success: function (res) {
                    $("#active_time").html(res);
                }
            })
        }, 1000);
    </script>
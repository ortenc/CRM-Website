<?php
require('database.php');

if(!$_SESSION['id'])
{
    header('location : login.php');
}
$_SESSION['last_activity'] = time();
$user_id = $_SESSION['id'];
$user_role = $_SESSION['role'];

$query ="SELECT * FROM users WHERE id= '$user_id'";

$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Store session id for user chat

$user_id_list = $_SESSION['id'];


?>
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
            <li class="active">
                <a href="chat_room.php"><i class="fa fa-user-plus"></i> <span class="nav-label">Chat Room</span></a>
            </li>
            <?php if($user_role == "Admin") { ?>
                <li class="active">
                    <a href="userlist.php"><i class="fa fa-table"></i> <span class="nav-label">Admin</span></a>
                </li>
            <?php } ?>
            <li class="active">
                <a href="report.php"><i class="fa fa-file"></i> <span class="nav-label">Report</span></a>
            </li>
            <li class="active">
                <a href="index.php"><i class="fa fa-money"></i> <span class="nav-label">Wages</span></a>
            </li>
            <li class="active">
                <a href="shop.php"><i class="fa fa-shopping-bag"></i> <span class="nav-label">Shop</span></a>
            </li>
        </ul>
    </div>
</nav>
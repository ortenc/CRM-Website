
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
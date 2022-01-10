<?php
require('database.php');


if(!$_SESSION['id'])
{
    header('location : login.php');
}

$user_id = $_SESSION['id'];
$user_role = $_SESSION['role'];

$query ="SELECT * FROM users WHERE id= '$user_id'";

$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);


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
            <?php if($user_role == "Admin") { ?>
                <li class="active">
                    <a href="userlist.php"><i class="fa fa-table"></i> <span class="nav-label">Admin</span></a>
                </li>
            <?php } ?>
            <li class="text-box">
                <a><i class="fa fa-user-circle"></i> <span class="nav-label"><?php echo $_SESSION['last_activity'] ?></span></a>
            </li>
        </ul>
    </div>
</nav>
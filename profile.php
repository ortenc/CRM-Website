<?php
include('functions.php');
require('database.php');
session_start();

if (!$_SESSION['id']) {
    header('location : login.php');
}

$user_id = $_SESSION['id'];
$user_role = $_SESSION['role'];

$query = "SELECT * FROM users WHERE id= '$user_id'";

$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Get session id for user chat
$user_id_list = $_SESSION['id'];

$query_get_ids = "SELECT DISTINCT to_user_id FROM chat_message WHERE from_user_id='$user_id_list'";
$result_get_ids = mysqli_query($conn, $query_get_ids);

if (!$result_get_ids) {
    echo "Internal server error";
    exit;
}

$ids_arr = array();
while ($row = mysqli_fetch_assoc($result_get_ids)) {
    $ids_arr[] = $row['to_user_id'];
}

$ids_string = implode("','", $ids_arr);

$query_list = "SELECT id, name FROM users WHERE id IN ('$ids_string')";
$result_list = mysqli_query($conn, $query_list);

if (!$result_list) {
    echo "Internal server error";
    exit;
}

// Store all users except for the one logged in inside an array

$chat_list = array();
while ($row = mysqli_fetch_assoc($result_list)) {
    $tmp = array();

    $tmp["id"] = $row["id"];
    $tmp["name"] = $row["name"];

    $chat_list[$row['id']] = $tmp;

}

?>
<!DOCTYPE html>
<html>
<head>
    <title>INSPINIA | Profile</title>

    <?php
    include "header.php";
    ?>
</head>
<body>

<div id="wrapper">

    <?php
        include "navbar.php";
        include "topbar.php";
    ?>
        <div class="wrapper wrapper-content">
            <div class="row animated fadeInRight">
                <div class="col-md-4">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Profile Detail</h5>
                        </div>
                        <div>
                            <div class="ibox-content no-padding border-left-right">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="profile-container-user">
                                            <img id="profileImage-user" src="<?= $user['photo'] ?>">
                                        </div>
                                        <input id="profile_photo" type="file" name="profile_photo">
                                    </div>
                                </div>
                            </div>
                            <div class="ibox-content profile-content">
                                <h4><strong><?= $user['name'] . " " . $user['surname'] ?></strong></h4>
                                <p>
                                    <i class="fa fa-envelope"></i>
                                    <span style="margin-left: 5px;"><?= $user['email'] ?></span>
                                </p>
                            </div>
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3>Edit Profile</h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Email address</label>
                                            <input type="email" id="email" name="email" class="form-control"
                                                   placeholder="Email" value="<?= $user['email'] ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input type="text" id="name" name="name" class="form-control"
                                                   placeholder="Name" value="<?= $user['name'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-1">
                                        <div class="form-group">
                                            <label>Last Name</label>
                                            <input type="text" id="surname" name="surname" class="form-control"
                                                   placeholder="Last Name" value="<?= $user['surname'] ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <label>Role</label>
                                            <input type="text" class="form-control" value="<?= $user['role'] ?>"
                                                   disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6 px-1">
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <input type="text" class="form-control" value="<?= $user['gender'] ?>"
                                                   disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-round"
                                                onclick="userUpdate('<?= $user['id'] ?>')">Update Profile
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>User Chat</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($chat_list as $key => $row) {
                                        $status = '';
                                        $current_timestamp = strtotime(date("Y-m-d H:i:s") . '- 10 second');
                                        $current_timestamp = date('Y-m-d H:i:s', $current_timestamp);
                                        $user_last_activity = fetch_user_last_activity($row['id'], $conn);

                                        if (strtotime($user_last_activity) > strtotime($current_timestamp)) {
                                            $status = '<span class="badge badge-primary">Online</span>';
                                        } else {
                                            $status = '<span class="badge badge-danger">Offline</span>';
                                        }

                                        ?>
                                        <tr>
                                            <td><?= $row['name'] ?></td>
                                            <td><?= $status ?></td>
                                            <td>
                                                <button type="button"
                                                        class="btn btn-info btn-xs start_chat"
                                                        data-touserid="<?= $row['id'] ?>"
                                                        data-tousername="<?= $row['name'] ?>">
                                                    Start Chat
                                                </button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div id="user_model_details"
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php
include "footer.php";
?>

<script>

        function isEmpty(value) {
            return typeof value == 'string' && !value.trim() || typeof value == 'undefined' || value === null;
        }

        // User profile info update from the user himself
        function userUpdate(id) {

            var post_data = new FormData();
            post_data.append('action', 'userUpdate');
            post_data.append('id', id);
            post_data.append("name", $("#name").val());
            post_data.append("surname", $("#surname").val());
            post_data.append("email", $("#email").val());

            var profile_photo = $("#profile_photo").prop('files')[0];
            post_data.append("profile_photo", profile_photo);

            $.ajax({
                url: 'ajax.php',
                method: 'POST',
                type: 'POST',
                cache: false,
                contentType: false,
                processData: false,
                data: post_data,
                success: function (response) {
                    try {
                        response = JSON.parse(response);
                        if (response.code === '200') {
                            window.location.href = "profile.php";
                        } else {
                            alert(response.message);
                        }
                    } catch (e) {
                        alert(response.message);
                    }
                }
            });
        }

        // User chat configuration

        $(document).ready(function () {

            // we call these 2 functions every 5 seconds in order to update user activity and chat history

            setInterval(function () {
                update_last_activity();
                update_chat_history_data();
            }, 5000);


            // Create update activity function

            function update_last_activity() {

                $.ajax({
                    url: "ajax.php",
                    method: "POST",
                    data: {"action": "update_last_activity"},
                    success: function () {

                    }
                })
            }

            // Create modal pop up box for chat interface function

            function make_chat_dialog_box(to_user_id, to_user_name) {
                var modal_content = '<div id="user_dialog_' + to_user_id + '" class="user_dialog" title="You have chat with ' + to_user_name + '">';
                modal_content += '<div style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;" class="chat_history" data-touserid="' + to_user_id + '" id="chat_history_' + to_user_id + '">';
                modal_content += '</div>';
                modal_content += '<div class="form-group">';
                modal_content += '<textarea name="chat_message_' + to_user_id + '" id="chat_message_' + to_user_id + '" class="form-control" placeholder="Type..."></textarea>';
                modal_content += '</div><div class="form-group" align="right">';
                modal_content += '<button type="button" name="send_chat" id="' + to_user_id + '" class="btn btn-info send_chat">Send</button></div></div>';
                $('#user_model_details').html(modal_content);
            }

            // initiate the pop up with click action and load chat history

            $(document).on('click', '.start_chat', function () {
                var to_user_id = $(this).data('touserid');
                var to_user_name = $(this).data('tousername');
                make_chat_dialog_box(to_user_id, to_user_name);
                $("#user_dialog_" + to_user_id).dialog({
                    autoOpen: false,
                    width: 400,
                });
                $('#user_dialog_' + to_user_id).dialog('open');

                var data = {
                    "action": 'get_chat_history',
                    "to_user_id": to_user_id
                }

                $.ajax({
                    url: "ajax.php",
                    method: 'POST',
                    type: 'POST',
                    data: data,
                    cache: false,
                    success: function (result) {
                        var res = JSON.parse(result);
                        var chating = res.chat;
                        $('#chat_history_' + to_user_id).html(chating);
                    }
                })
            });

            // Send chat with click action button

            $(document).on('click', '.send_chat', function () {
                var to_user_id = $(this).attr('id');
                var chat_message = $('#chat_message_' + to_user_id).val();
                $.ajax({
                    url: "ajax.php",
                    method: "POST",
                    data: {to_user_id: to_user_id, chat_message: chat_message, "action": "insert_chat"},
                    success: function (data) {
                        $('#chat_message_' + to_user_id).val('');
                        $('#chat_history_' + to_user_id).html(data);
                    }
                })
            });

            // Create update chat history function

            function update_chat_history_data() {
                $('.chat_history').each(function () {
                    var to_user_id = $(this).data('touserid');
                    var data = {
                        "action": 'get_chat_history',
                        "to_user_id": to_user_id
                    }
                    $.ajax({
                        url: "ajax.php",
                        method: 'POST',
                        data: data,
                        cache: false,
                        success: function (result) {
                            var res = JSON.parse(result);
                            var chating = res.chat;
                            $('#chat_history_' + to_user_id).html(chating);
                        }
                    })

                });
            }

            // Exit chat modal with x button in the top corner

            $(document).on('click', '.ui-button-icon', function () {
                $('.user_dialog').dialog('destroy').remove();
            });

            // template data table options

            $('.dataTables-example').DataTable({
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    {extend: 'copy'},
                    {extend: 'csv'},
                    {extend: 'excel', title: 'ExampleFile'},
                    {extend: 'pdf', title: 'ExampleFile'},

                    {
                        extend: 'print',
                        customize: function (win) {
                            $(win.document.body).addClass('white-bg');
                            $(win.document.body).css('font-size', '10px');

                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        }
                    }
                ]

            });
        });
    // User chat configuration

</script>
</body>

</html>
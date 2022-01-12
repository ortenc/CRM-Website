<?php
include('functions.php');
?>
<?php
session_start();

if(!$_SESSION['id'])
{
    header('location : login.php');
}

$user_id = $_SESSION['id'];
$user_role = $_SESSION['role'];

require('database.php');
// Get session id for user chat
$user_id_list = $_SESSION['id'];

$query_list = "SELECT * FROM users WHERE id != '".$user_id_list."'";
$result_list = mysqli_query($conn, $query_list);

if (!$result_list) {
    echo "Internal server error";
    exit;
}

$users = [];
while($row = mysqli_fetch_assoc($result_list)){
    $users[$row['id']]['id']= $row['id'];
    $users[$row['id']]['name']= $row['name'];
    $users[$row['id']]['surname'] = $row['surname'];
    $users[$row['id']]['email'] = $row['email'];
    $users[$row['id']]['gender'] = $row['gender'];
    $users[$row['id']]['role'] = $row['role'];
}

?>
<!DOCTYPE html>
<html>

<head>

    <title>Chat Room</title>

    <?php include "header.php"; ?>

</head>

<body>

<div id="wrapper">

    <?php
        include "navbar.php";
        include "topbar.php";
    ?>

    <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>List of users registered into the system</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover dataTables-example" id="emptable" >
                                    <thead>
                                    <tr>
                                <th>Username</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($result_list as $key => $row) {

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
                                <tr class="gradeX">
                                    <td><?= $row['name'] . ' ' . count_unseen_message($row['id'], $_SESSION['id'], $conn) ?></td>
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
                            </tfoot>
                        </table>
                    </div>
                    <div id="user_model_details"
                </div>

            </div>
        </div>

<?php include "footer.php"; ?>

                <script>

                    function isEmpty(value) {
                        return typeof value == 'string' && !value.trim() || typeof value == 'undefined' || value === null;
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

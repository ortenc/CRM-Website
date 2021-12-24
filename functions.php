<?php


include 'database.php';

// Create fetch user last activity for the purpose of the user online/offline status

function fetch_user_last_activity($user_id, $conn)
{
    $query_fetch_activity = "SELECT * FROM login_details 
                    WHERE user_id = '$user_id' 
                    ORDER BY last_activity ASC
                    LIMIT 1
                    ";
    $fetch_activity_statement = mysqli_query($conn, $query_fetch_activity);
    $result_fetch_activity = mysqli_fetch_all($fetch_activity_statement, MYSQLI_ASSOC);
    foreach($result_fetch_activity as $row)
    {
        return $row['last_activity'];
    }

}

// Create fetch user last activity for the purpose of the user online/offline status

// Create fetch chat history in order to get the chat between the 2 users


function fetch_user_chat_history($from_user_id, $to_user_id, $conn)
{
    $query_fetch_history = "SELECT * FROM chat_message 
              WHERE (from_user_id = '".$from_user_id."' 
              AND to_user_id = '".$to_user_id."') 
              OR (from_user_id = '".$to_user_id."' 
              AND to_user_id = '".$from_user_id."') 
              ORDER BY timestamp DESC 
              ";
    $statement_fetch_history = mysqli_query($conn, $query_fetch_history);
    $result_fetch_history = mysqli_fetch_all($statement_fetch_history, MYSQLI_ASSOC);

    // Depending on the result fetch history we assign the user id to user_name variable in order to tell who typed every message by comparing id of each message to user id and output them in a descending order

    $output = '<ul class="list-unstyled">';
    foreach($result_fetch_history as $row)
    {
        $user_name = '';

        if($row["from_user_id"] == $from_user_id)
        {
            $user_name = '<b class="text-success">You</b>';
        }
        else
        {
            $user_name = '<b class="text-danger">'.get_user_name($row['from_user_id'], $conn).'</b>';
        }
        $output .= '        
    <li style="border-bottom:1px dotted #ccc">
     <p>'.$user_name.' - '.$row["chat_message"].'  
        <div align="right">
         - <small><em>'.timeAgo($row['timestamp']).'</em></small>
        </div>
     </p>
    </li>
    ';
    }

    // Query for message status update if any message is sent and not read by the other user
    // we set status 0 if message goes from user to user and 1 if only from user

    $output .= '</ul>';
    $query = "UPDATE chat_message 
              SET status = '0' 
              WHERE from_user_id = '".$to_user_id."' 
              AND to_user_id = '".$from_user_id."' 
              AND status = '1'
              ";
    $statement = mysqli_query($conn, $query);
    return $output;
}

// Create fetch chat history in order to get the chat between the 2 users

// get username function for the chat history function so we can get the name according to the id for every row message in the chat

function get_user_name($user_id, $conn)
{
    $query = "SELECT name FROM users WHERE id = '$user_id'";
    $statement = mysqli_query($conn, $query);
    $result = mysqli_fetch_all($statement, MYSQLI_ASSOC);
    foreach($result as $row)
    {
        return $row['name'];
    }
}

// get username function for the chat history function so we can get the name according to the id for every row message in the chat

// Count unseen message function where we take all messages with the status 1 which are the messages sent and not read by the other user


function count_unseen_message($from_user_id, $to_user_id, $conn)
{
    $query = "SELECT status FROM chat_message 
              WHERE from_user_id = '$from_user_id'
              AND to_user_id = '$to_user_id'
              AND status = '1'
              ";
    $statement = mysqli_query($conn, $query);
    $count = mysqli_fetch_assoc($statement);
    $output = '';
    if($count > 0)
    {

        $output = '<span class="label label-success">'.$count["status"].'</span>';
    }
    return $output;
}

// Count unseen message function where we take all messages with the status 1 which are the messages sent and not read by the other user

// function for timestamp format hh


function timeAgo($timestamp)
{
    $datetime1=new DateTime("now");
    $datetime2=date_create($timestamp);
    $diff=date_diff($datetime1, $datetime2);
    $timemsg='';
    if($diff->y > 0){
        $timemsg = $diff->y .' year'. ($diff->y > 1?"'s":'');
    }
    else if($diff->m > 0){
        $timemsg = $diff->m . ' month'. ($diff->m > 1?"'s":'');
    }
    else if($diff->d > 0){
        $timemsg = $diff->d .' day'. ($diff->d > 1?"'s":'');
    }
    else if($diff->h > 0){
        $timemsg = $diff->h .' hour'.($diff->h > 1 ? "'s":'');
    }
    else if($diff->i > 0){
        $timemsg = $diff->i .' minute'. ($diff->i > 1?"'s":'');
    }
    else if($diff->s > 0){
        $timemsg = $diff->s .' second'. ($diff->s > 1?"'s":'');
    }

    $timemsg = $timemsg.' ago';
    return $timemsg;
}

// function for timestamp format hh


?>
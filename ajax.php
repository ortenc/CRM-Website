<?php
error_reporting(E_ERROR);
session_start();
require 'database.php';
include 'functions.php';


if ($_POST['action'] == "register") {

    $txtname = $conn->escape_string($_POST['fname']);
    $txtsurname = $conn->escape_string($_POST['lname']);
    $txtemail = $conn->escape_string($_POST['email']);
    $txtgender = $conn->escape_string($_POST['gender']);
    $txtpassword1 = $conn->escape_string($_POST['password1']);

    //TODO: Te ndertohet dhe te perdoret nje funksion qe ben validimin dhe match me confirm passw

    $number = preg_match('@[0-9]@', $txtpassword1);
    $uppercase = preg_match('@[A-Z]@', $txtpassword1);
    $lowercase = preg_match('@[a-z]@', $txtpassword1);
    $specialChars = preg_match('@[^\w]@', $txtpassword1);

    if (strlen($txtpassword1) < 8 || !$number || !$uppercase || !$lowercase || !$specialChars) {
        echo json_encode(array("code" => "422", "message" => "Password must be at least 8 characters in length and must contain at least one number, one upper case letter, one lower case letter and one special character."));
        exit;
    }
    $txtpassword2 = $conn->escape_string($_POST['password2']);

    if ($txtpassword1 != $txtpassword2) {
        echo json_encode(array("code" => "422", "message" => "Password fields are not the same"));
        exit;
    }
    $hash = password_hash($txtpassword1, PASSWORD_DEFAULT); // hashing the password with the default algorithm

    //Return error code if one of the fields is empty

    if (empty($txtname)) {
        echo json_encode(array("code" => "422", "message" => "Name cannot be empty!"));
        exit;
    }
    if (empty($txtsurname)) {
        echo json_encode(array("code" => "422", "message" => "Surname cannot be empty!"));
        exit;
    }
    if (empty($txtemail)) {
        echo json_encode(array("code" => "422", "message" => "Name cannot be empty!"));
        exit;
    }
    if (empty($txtgender)) {
        echo json_encode(array("code" => "422", "message" => "Gender cannot be empty!"));
        exit;
    }
    if (empty($txtpassword1)) {
        echo json_encode(array("code" => "422", "message" => "Password cannot be empty!"));
        exit;
    }
    // check if e-mail address is well-formed
    if (!filter_var($txtemail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(array("code" => "422", "message" => "Email is not correct"));
        exit;
    }

    $query_check = "SELECT id FROM users WHERE email='$txtemail'";
    $result_check = mysqli_query($conn, $query_check);
    $numRows = mysqli_num_rows($result_check);

    if ($numRows > 0) {
        echo json_encode(array("code" => "422", "message" => "This email already exist in our system!"));
        exit;
    }

    // database insert SQL code

    $query_insert = "INSERT INTO users
      SET name = '$txtname',
         surname = '$txtsurname',
         email = '$txtemail',
         gender = '$txtgender',
         password = '$hash',
         role = 'User'";

    $result_insert = mysqli_query($conn, $query_insert);

    if ($result_insert) {
        echo json_encode(array("code" => "200", "message" => "Success"));
        exit;
    } else {
        echo json_encode(array("code" => "422", "message" => "Error"));
    }



    // Log in user into the webapage action code


} elseif ($_POST['action'] == "login") {

    $txtpassword = $_POST['password'];
    $txtemail = mysqli_real_escape_string($conn, $_POST['email']);

    if (empty($txtemail)) {
        echo json_encode(array("code" => "422", "message" => "Email cannot be empty!"));
        exit;
    }
    if (empty($txtpassword)) {
        echo json_encode(array("code" => "422", "message" => "Password cannot be empty!"));
        exit;
    }

    $query_select = "SELECT * FROM users WHERE email='$txtemail'";
    $result = mysqli_query($conn, $query_select);
    $check = mysqli_fetch_assoc($result);

    if (password_verify($txtpassword, $check['password'])) {
        $_SESSION['id'] = $check['id'];
        $_SESSION['name'] = $check['name'];
        $_SESSION['role'] = $check['role'];
        $sub_query_insert = "INSERT INTO login_details 
                      SET user_id = '" . $check['id'] . "'
                      ";
        $sub_result = mysqli_query($conn, $sub_query_insert);
        $_SESSION['login_details_id'] = mysqli_insert_id($conn);

        if ($check['role'] == "User") {
            echo json_encode(array("code" => "200", "message" => "Success"));
            exit;
        }
        if ($check['role'] == "Admin") {
            echo json_encode(array("code" => "200", "message" => "Success"));
            exit;
        }
    }
    else{
        echo json_encode(array("code" => "422", "message" => "Password incorrect!"));
        exit;
    }



    // Update user details in the database from admin panel action code


} elseif ($_POST['action'] == "update") {

    $id = $conn->escape_string($_POST['id']);
    $fname = $conn->escape_string($_POST['name']);
    $lname = $conn->escape_string($_POST['surname']);
    $email = $conn->escape_string($_POST['email']);
    $role = $conn->escape_string($_POST['role']);

    $query_update = "UPDATE users
      SET name = '$fname',
          surname = '$lname',
          email = '$email',
          role = '$role'
         WHERE id = '$id'";


    $result_check = mysqli_query($conn, $query_update);

    if ($result_check) {
        echo json_encode(array("code" => "200", "message" => "Success"));
        exit;
    } else {
        echo json_encode(array("code" => "404", "message" => "Error"));
        exit;
    }



    // Delete user from database from admin panel action code


} elseif ($_POST['action'] == "erase") {

    $id = $conn->escape_string($_POST['id']);

    $query_delete = "delete from users where id = '$id'";

    $result_check = mysqli_query($conn, $query_delete);

    if ($result_check) {
        echo json_encode(array("code" => "200", "message" => "Success"));
        exit;
    } else {
        echo json_encode(array("code" => "404", "message" => "Error"));
        exit;
    }




    // Personal user details update from userpage panel code


} elseif ($_POST['action'] == "userUpdate") {

    $id = $conn->escape_string($_POST['id']);
    $fname = $conn->escape_string($_POST['name']);
    $lname = $conn->escape_string($_POST['surname']);
    $email = $conn->escape_string($_POST['email']);

    if (empty($fname)) {
        echo json_encode(array("code" => "422", "message" => "name cannot be empty!"));
        exit;
    }if (empty($lname)) {
        echo json_encode(array("code" => "422", "message" => "surname cannot be empty!"));
        exit;
    }if (empty($email)) {
        echo json_encode(array("code" => "422", "message" => "Email cannot be empty!"));
        exit;
    }

    $query_update_users = "UPDATE users
      SET name = '$fname',
          surname = '$lname',
          email = '$email'
         WHERE id = '$id'";

    $result_check = mysqli_query($conn, $query_update_users);

    if ($result_check) {
        echo json_encode(array("code" => "200", "message" => "Success"));
        exit;
    } else {
        echo json_encode(array("code" => "404", "message" => "Error"));
        exit;
    }




    // Get chat history when the pop up is initiated from start chat button click


} elseif ($_POST['action'] == "get_chat_history") {

    $to_user_id = $conn->escape_string($_POST['to_user_id']);
    $from_user_id = $conn->escape_string($_SESSION['id']);
    $data = fetch_user_chat_history($from_user_id, $to_user_id, $conn);

    echo json_encode(array("code" => "200", "message" => "Success", 'chat' => $data));


}




// Update last activity time of the user


elseif ($_POST['action'] == "update_last_activity") {

    $query = "UPDATE login_details 
              SET last_activity = now() 
              WHERE login_details_id = '" . $_SESSION["login_details_id"] . "'
              ";



    $result = mysqli_query($conn, $query);
}





// Insert chat from user to user with message body


elseif ($_POST['action'] == "insert_chat") {

    $to_user_id = $conn->escape_string($_POST['to_user_id']);
    $from_user_id = $conn->escape_string($_SESSION['id']);
    $chat_message = $conn->escape_string($_POST['chat_message']);
    $status = 1;

    $query = "INSERT INTO chat_message 
              SET to_user_id = '$to_user_id',
              from_user_id = '$from_user_id',
              chat_message = '$chat_message',
              status = '1'";

    $result = mysqli_query($conn, $query);

    if ($result) {
        echo fetch_user_chat_history($_SESSION['id'], $_POST['to_user_id'], $conn);
    }


} elseif ($_POST['action'] == "fill_modal_user_data") {
    $user_id = $conn->escape_string($_POST['user_id']);

    $query_modal = "SELECT * FROM users WHERE id = '$user_id'";
    $result_modal = mysqli_query($conn, $query_modal);
    $user = mysqli_fetch_assoc($result_modal);

    if(!$result_modal){
        echo json_encode(array("code" => "422", "message" => "Internal server error"));
        exit;
    }

    echo json_encode(array(
        "code" => "200",
        "message" => "Success",
        "id" => $user['id'],
        "email" => $user['email'],
        "name" => $user['name'],
        "surname" => $user['surname'],
        "role" => $user['role']
        ));


} elseif ($_POST['action'] == "fill_user_delete") {

    $user_id = $conn->escape_string($_POST['user_id']);

    $query_delete = "SELECT * FROM users WHERE id = '$user_id'";
    $result_modal = mysqli_query($conn, $query_delete);
    $user = mysqli_fetch_assoc($result_modal);

    if(!$result_modal){
        echo json_encode(array("code" => "422", "message" => "Internal server error"));
        exit;
    }

    echo json_encode(array(
        "code" => "200",
        "message" => "Success",
        "id" => $user['id'],
        "email" => $user['email'],
        "name" => $user['name'],
        "surname" => $user['surname'],
        "role" => $user['role']
        ));


}


?>
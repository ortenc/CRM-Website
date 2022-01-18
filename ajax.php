<?php
error_reporting(E_ERROR);
session_start();
require 'database.php';
include 'functions.php';

// Register code for new users into the system

if ($_POST['action'] == "register") {

    $txtname = $conn->escape_string($_POST['fname']);
    $txtsurname = $conn->escape_string($_POST['lname']);
    $atesia = $conn->escape_string($_POST['atesia']);
    $txtemail = $conn->escape_string($_POST['email']);
    $txtbirthday = $conn->escape_string($_POST['date_change']);
    $phone = $conn->escape_string($_POST['phone']);
    $txtgender = $conn->escape_string($_POST['gender']);
    $txtpassword1 = $conn->escape_string($_POST['password1']);

    //Return error code if one of the fields is empty

    $name_preg = preg_match("/^[a-zA-Z-'\s]*$/", $txtname);
    $surname_preg = preg_match("/^[a-zA-Z-'\s]*$/", $txtsurname);
    $atesia_preg = preg_match("/^[a-zA-Z-'\s]*$/", $atesia);
    $phone_preg = preg_match("/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im", $phone);
    if (empty($txtname)) {
        echo json_encode(array("code" => "422", "message" => "Name cannot be empty!"));
        exit;
    }if (!$name_preg) {
        echo json_encode(array("code" => "422", "message" => "Name must have only letters!"));
        exit;
    }if (empty($txtsurname)) {
        echo json_encode(array("code" => "422", "message" => "Surname cannot be empty!"));
        exit;
    }if (!$surname_preg) {
        echo json_encode(array("code" => "422", "message" => "Name must have only letters!"));
        exit;
    }if (empty($atesia)) {
        echo json_encode(array("code" => "422", "message" => "Atesia cannot be empty!"));
        exit;
    }if (!$atesia_preg) {
        echo json_encode(array("code" => "422", "message" => "Name must have only letters!"));
        exit;
    }if (empty($txtemail)) {
        echo json_encode(array("code" => "422", "message" => "email cannot be empty!"));
        exit;
    }
    // check if e-mail address is well-formed
    if (!filter_var($txtemail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(array("code" => "422", "message" => "Email is not correct"));
        exit;
    }
    // Check if email already exists
    $query_check = "SELECT id FROM users WHERE email='$txtemail'";
    $result_check = mysqli_query($conn, $query_check);
    $numRows = mysqli_num_rows($result_check);

    if ($numRows > 0) {
        echo json_encode(array("code" => "422", "message" => "This email already exist in our system!"));
        exit;
    }if (empty($txtbirthday)) {
        echo json_encode(array("code" => "422", "message" => "birthday cannot be empty!"));
        exit;
    }if (empty($phone)) {
        echo json_encode(array("code" => "422", "message" => "Phone cannot be empty!"));
        exit;
    }if (!$phone_preg) {
        echo json_encode(array("code" => "422", "message" => "Phone wrong format!"));
        exit;
    }if (empty($txtgender)) {
        echo json_encode(array("code" => "422", "message" => "Gender cannot be empty!"));
        exit;
    }if (empty($txtpassword1)) {
        echo json_encode(array("code" => "422", "message" => "Password cannot be empty!"));
        exit;
    }

    // password field validation and check for similarities between the two fields

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

    // Username creation

    $username = $txtname[0].$txtsurname ;
    $registerdate = date("Y-m-d H:i:s");

    // database insert SQL code

    $query_insert = "INSERT INTO users
      SET name = '$txtname',
         surname = '$txtsurname',
         atesia = '$atesia',
         email = '$txtemail',
         username = '$username',
         birthday = '$txtbirthday',
         phone = '$phone',
         gender = '$txtgender',
         password = '$hash',
         registerdate = '$registerdate',
         role = 'User'";

    $result_insert = mysqli_query($conn, $query_insert);

    // If data inputed successfully into the database following conditions applied
    if ($result_insert) {
        echo json_encode(array("code" => "200", "message" => "Success"));
        exit;
    } else {
        echo json_encode(array("code" => "422", "message" => "Error"));
    }



// Create user from admin panel


}if ($_POST['action'] == "create") {

    $txtname = $conn->escape_string($_POST['fname']);
    $txtsurname = $conn->escape_string($_POST['lname']);
    $atesia = $conn->escape_string($_POST['atesia']);
    $txtemail = $conn->escape_string($_POST['email']);
    $txtbirthday = $conn->escape_string($_POST['date_change']);
    $phone = $conn->escape_string($_POST['phone']);
    $txtgender = $conn->escape_string($_POST['gender']);
    $txtpassword1 = $conn->escape_string($_POST['password1']);
    $txtpassword2 = $conn->escape_string($_POST['password2']);
    $role = $conn->escape_string($_POST['role']);

    //Return error code if one of the fields is empty

    $name_preg = preg_match("/^[a-zA-Z-'\s]*$/", $txtname);
    $surname_preg = preg_match("/^[a-zA-Z-'\s]*$/", $txtsurname);
    $atesia_preg = preg_match("/^[a-zA-Z-'\s]*$/", $atesia);
    $phone_preg = preg_match("/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im", $phone);
    if (empty($txtname)) {
        echo json_encode(array("code" => "422", "message" => "Name cannot be empty!"));
        exit;
    }if (!$name_preg) {
        echo json_encode(array("code" => "422", "message" => "Name must have only letters!"));
        exit;
    }if (empty($txtsurname)) {
        echo json_encode(array("code" => "422", "message" => "Surname cannot be empty!"));
        exit;
    }if (!$surname_preg) {
        echo json_encode(array("code" => "422", "message" => "Name must have only letters!"));
        exit;
    }if (empty($atesia)) {
        echo json_encode(array("code" => "422", "message" => "Atesia cannot be empty!"));
        exit;
    }if (!$atesia_preg) {
        echo json_encode(array("code" => "422", "message" => "Name must have only letters!"));
        exit;
    }if (empty($txtemail)) {
        echo json_encode(array("code" => "422", "message" => "email cannot be empty!"));
        exit;
    }
    // check if e-mail address is well-formed
    if (!filter_var($txtemail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(array("code" => "422", "message" => "Email is not correct"));
        exit;
    }
    // Check if email already exists
    $query_check = "SELECT id FROM users WHERE email='$txtemail'";
    $result_check = mysqli_query($conn, $query_check);
    $numRows = mysqli_num_rows($result_check);

    if ($numRows > 0) {
        echo json_encode(array("code" => "422", "message" => "This email already exist in our system!"));
        exit;
    }if (empty($txtbirthday)) {
        echo json_encode(array("code" => "422", "message" => "birthday cannot be empty!"));
        exit;
    }if (empty($phone)) {
        echo json_encode(array("code" => "422", "message" => "Phone cannot be empty!"));
        exit;
    }if (!$phone_preg) {
        echo json_encode(array("code" => "422", "message" => "Phone wrong format!"));
        exit;
    }if (empty($txtpassword1)) {
        echo json_encode(array("code" => "422", "message" => "Password cannot be empty!"));
        exit;
    }

    // password field validation and check for similarities between the two fields

    $number = preg_match('@[0-9]@', $txtpassword1);
    $uppercase = preg_match('@[A-Z]@', $txtpassword1);
    $lowercase = preg_match('@[a-z]@', $txtpassword1);
    $specialChars = preg_match('@[^\w]@', $txtpassword1);

    if (strlen($txtpassword1) < 8 || !$number || !$uppercase || !$lowercase || !$specialChars) {
        echo json_encode(array("code" => "422", "message" => "Password must be at least 8 characters in length and must contain at least one number, one upper case letter, one lower case letter and one special character."));
        exit;
    }

    if ($txtpassword1 != $txtpassword2) {
        echo json_encode(array("code" => "422", "message" => "Password fields are not the same"));
        exit;
    }
    $hash = password_hash($txtpassword1, PASSWORD_DEFAULT); // hashing the password with the default algorithm



    // Username creation

    $username = $txtname[0].$txtsurname ;
    $registerdate = date("Y-m-d H:i:s");

    // database insert SQL code

    $query_insert = "INSERT INTO users
      SET name = '$txtname',
         surname = '$txtsurname',
         atesia = '$atesia',
         email = '$txtemail',
         username = '$username',
         birthday = '$txtbirthday',
         phone = '$phone',
         gender = '$txtgender',
         password = '$hash',
         registerdate = '$registerdate',
         role = '$role'";

    $result_insert = mysqli_query($conn, $query_insert);
    // If data inputed successfully into the database following conditions applied
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

    $query_select = "SELECT * FROM users WHERE email='$txtemail' OR phone='$txtemail'";
    $result = mysqli_query($conn, $query_select);
    $check = mysqli_fetch_assoc($result);

    if (password_verify($txtpassword, $check['password'])) {
        $_SESSION['id'] = $check['id'];
        $_SESSION['name'] = $check['name'];
        $_SESSION['role'] = $check['role'];
        $_SESSION['last_login'] = date('Y-m-d H:i:s');
        $sub_query_insert = "INSERT INTO login_details 
                             SET user_id = '" . $check['id'] . "',
                                 last_login = '" . date('Y-m-d H:i:s') . "'
                             ";
        $sub_result = mysqli_query($conn, $sub_query_insert);
        $_SESSION['login_details_id'] = mysqli_insert_id($conn);
        echo json_encode(array("code" => "200", "message" => "Success"));

    }
    else{
        echo json_encode(array("code" => "422", "message" => "Password or Email incorrect!"));
        exit;
    }



// Update user details in the database from admin panel action code


}
elseif ($_POST['action'] == "update") {

    $id = $conn->escape_string($_POST['id']);
    $fname = $conn->escape_string($_POST['name']);
    $lname = $conn->escape_string($_POST['surname']);
    $atesia = $conn->escape_string($_POST['atesia']);
    $username = $conn->escape_string($_POST['username']);
    $phone = $conn->escape_string($_POST['phone']);
    $email = $conn->escape_string($_POST['email']);
    $role = $conn->escape_string($_POST['role']);

    $name_preg = preg_match("/^[a-zA-Z-'\s]*$/", $fname);
    $surname_preg = preg_match("/^[a-zA-Z-'\s]*$/", $lname);
    $atesia_preg = preg_match("/^[a-zA-Z-'\s]*$/", $atesia);
    $username_preg = preg_match("/^[a-zA-Z-'\s]*$/", $username);
    $phone_preg = preg_match("/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im", $phone);

    if (empty($fname)){
        echo json_encode(array("code" => "422", "message" => "Name cannot be empty"));
        exit;
    }if (!$name_preg){
        echo json_encode(array("code" => "422", "message" => "Name must be only letters"));
        exit;
    }if (empty($lname)){
        echo json_encode(array("code" => "422", "message" => "Surname cannot be empty"));
        exit;
    }if (!$surname_preg){
        echo json_encode(array("code" => "422", "message" => "Surname must be only letters"));
        exit;
    }if (empty($atesia)){
        echo json_encode(array("code" => "422", "message" => "Atesia cannot be empty"));
        exit;
    }if (!$atesia_preg){
        echo json_encode(array("code" => "422", "message" => "Atesia must be only letters"));
        exit;
    }if (empty($username)){
        echo json_encode(array("code" => "422", "message" => "Username cannot be empty"));
        exit;
    }if (!$username_preg){
        echo json_encode(array("code" => "422", "message" => "Username must be only letters"));
        exit;
    }if (empty($phone)){
        echo json_encode(array("code" => "422", "message" => "Phone cannot be empty"));
        exit;
    }if (!$phone_preg){
        echo json_encode(array("code" => "422", "message" => "Phone wrong format"));
        exit;
    }if (empty($email)){
        echo json_encode(array("code" => "422", "message" => "Email cannot be empty"));
        exit;
    }
    // check if e-mail address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(array("code" => "422", "message" => "Email is not correct"));
        exit;
    }
    // Check if email already exists
    $query_check = "SELECT id FROM users WHERE email='$email'";
    $result_check = mysqli_query($conn, $query_check);
    $numRows = mysqli_num_rows($result_check);

    if ($numRows > 0) {
        echo json_encode(array("code" => "422", "message" => "This email already exist in our system!"));
        exit;
    }

    $query_update = "UPDATE users
      SET name = '$fname',
          surname = '$lname',
          atesia = '$atesia',
          username = '$username',
          phone = '$phone',
          email = '$email',
          role = '$role'
         WHERE id = '$id'";


    $result_check = mysqli_query($conn, $query_update);

    if ($result_check) {
        echo json_encode(array("code" => "200", "message" => "Success"));
        exit;
    } else {
        echo json_encode(array("code" => "422", "message" => "Error"));
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
        echo json_encode(array("code" => "422", "message" => "Error"));
        exit;
    }




// Personal user details update from userpage panel code


}
elseif ($_POST['action'] == "userUpdate") {
    $id = $conn->escape_string($_POST['id']);
    $fname = $conn->escape_string($_POST['name']);
    $lname = $conn->escape_string($_POST['surname']);
    $username = $conn->escape_string($_POST['username']);
    $phone = $conn->escape_string($_POST['phonenum']);
    $email = $conn->escape_string($_POST['email']);
    $birthday = $conn->escape_string($_POST['birthday']);
    $photo_path = $conn->escape_string($_POST['photo_path']);

    $profile_photo = uploadPhoto($_FILES, $id, $photo_path);

    if (empty($profile_photo)) {
        echo json_encode(array("code" => "422", "message" => "photo cannot be empty!"));
        exit;
    }if (empty($fname)) {
        echo json_encode(array("code" => "422", "message" => "name cannot be empty!"));
        exit;
    }if (empty($lname)) {
        echo json_encode(array("code" => "422", "message" => "surname cannot be empty!"));
        exit;
    }if (empty($username)) {
        echo json_encode(array("code" => "422", "message" => "username cannot be empty!"));
        exit;
    }if (empty($phone)) {
        echo json_encode(array("code" => "422", "message" => "phone cannot be empty!"));
        exit;
    }if (empty($email)) {
        echo json_encode(array("code" => "422", "message" => "Email cannot be empty!"));
        exit;
    }if (empty($birthday)) {
        echo json_encode(array("code" => "422", "message" => "Birthday cannot be empty!"));
        exit;
    }
    $query_check = "SELECT id FROM users WHERE email='$email' AND id != '$id'";
    $result_check = mysqli_query($conn, $query_check);
    $numRows = mysqli_num_rows($result_check);

    if ($numRows > 0) {
        echo json_encode(array("code" => "422", "message" => "This email already exist in our system!"));
        exit;
    }


    $query_update_users = "UPDATE users
      SET name = '$fname',
          surname = '$lname',
          username = '$username',
          phone = '$phone',
          email = '$email',
          birthday = '$birthday',
          photo = '$profile_photo'
         WHERE id = '$id'";

    $result_check = mysqli_query($conn, $query_update_users);

    if ($result_check) {
        echo json_encode(array("code" => "200", "message" => "Success"));
        exit;
    } else {
        echo json_encode(array("code" => "422", "message" => "Error"));
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
        "atesia" => $user['atesia'],
        "username" => $user['username'],
        "phone" => $user['phone'],
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

} elseif ($_POST['action'] == "last_active_time") {

    $last_login =  strtotime($_SESSION['last_login']);
    $now = strtotime(date('Y/m/d H:i:s'));
    $active_now = $now - $last_login;
    $active_now = date('i:s', $active_now) . " min";

    echo $active_now;

}



?>
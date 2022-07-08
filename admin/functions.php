<?php

function redirect($location)
{
    header("Location:" . $location);
    exit;
}

function query($query)
{
    global $connection;
    $result = mysqli_query($connection, $query);
    confirmQuery($result);
    return $result;
}

function fetchRecords($result)
{
    return mysqli_fetch_array($result);
}

function get_username()
{
    return isset($_SESSION["username"]) ? $_SESSION["username"] : null;
}

function is_admin()
{
    if (isLoggedIn()) {
        $result = query("SELECT user_role FROM users WHERE user_id = " . $_SESSION["user_id"] . "");
        $row = fetchRecords($result);

        if ($row["user_role"] == "Admin") {
            return true;
        } else {
            return false;
        }
    };
    return false;
}

function ifItIsMethod($method = null)
{
    if ($_SERVER["REQUEST_METHOD"] == strtoupper($method)) {
        return true;
    }
    return false;
}

function isLoggedIn()
{
    if (isset($_SESSION["user_role"])) {
        return true;
    }
    return false;
}

function checkIfUserIsLoggedInAndRedirect($redirectLocation = null)
{
    if (isLoggedIn()) {
        redirect($redirectLocation);
    }
}

function escape($string)
{
    global $connection;
    return mysqli_real_escape_string($connection, trim($string));
}

function confirmQuery($result)
{
    global $connection;

    if (!$result) {
        die("QUERY FAILED ." . mysqli_error($connection));
    }
}

function insert_categories()
{
    global $connection;

    if (isset($_POST["submit"])) {
        $cat_title = $_POST["cat_title"];

        if ($cat_title == "" || empty($cat_title)) {
            echo "Cannot be blank.";
        } else {
            $query = "INSERT INTO categories(cat_title) ";
            $query .= "VALUE('{$cat_title}')  ";

            $create_category_query = mysqli_query($connection, $query);

            if (!$create_category_query) {
                die("Query Failed" . mysqli_error($connection));
            }
        }
    }
}

function find_all_categories()
{
    global $connection;

    $query = "SELECT * FROM categories";
    $select_categories = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_assoc($select_categories)) {
        $cat_id = $row["cat_id"];
        $cat_title = $row["cat_title"];
        echo "<tr>";
        echo "<td>{$cat_id}</td>";
        echo "<td>{$cat_title}</td>";
        echo "<td><a href='categories.php?delete={$cat_id}'>Delete</a></td>";
        echo "<td><a href='categories.php?edit={$cat_id}'>Edit</a></td>";
        echo "</tr>";
    }
}

function delete_categories()
{
    global $connection;

    if (isset($_GET["delete"])) {
        $the_cat_id = $_GET["delete"];
        $query = "DELETE  FROM categories WHERE cat_id = {$the_cat_id} ";
        mysqli_query($connection, $query);
        header("Location: categories.php");
    }
}

function record_count($table)
{
    global $connection;
    $query = "SELECT * FROM " . $table;
    $select_all_post = mysqli_query($connection, $query);
    return mysqli_num_rows($select_all_post);
}

function check_status($table, $column, $status)
{

    global $connection;
    $query = "SELECT * FROM $table WHERE $column = '$status'";
    $result = mysqli_query($connection, $query);
    return mysqli_num_rows($result);
}

function check_user_role($table, $column, $role)
{
    global $connection;
    $query = "SELECT * FROM $table WHERE $column = '$role'";
    $select_all_subscribers = mysqli_query($connection, $query);
    return mysqli_num_rows($select_all_subscribers);
}

function username_exists($username)
{
    global $connection;
    $query = "SELECT username FROM users WHERE username = '$username'";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) < 0) {
        return true;
    } else {
        return false;
    }
}

function email_exists($email)
{
    global $connection;
    $query = "SELECT user_email FROM users WHERE user_email = '$email'";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) > 0) {
        return true;
    } else {
        return false;
    }
}

function register_user($username, $email, $password)
{
    global $connection;

    $username =  mysqli_real_escape_string($connection, $username);
    $email = mysqli_real_escape_string($connection, $email);
    $password = mysqli_real_escape_string($connection, $password);

    $password = password_hash($password, PASSWORD_BCRYPT, array("cost" => 12));

    $query = "INSERT INTO users (username, user_email, user_password, user_role)";
    $query .= "VALUES('{$username}', '{$email}', '{$password}', 'subscriber')";
    mysqli_query($connection, $query);
}

function login_user($username, $password)
{
    global $connection;

    $username = trim($username);
    $password = trim($password);

    $username = mysqli_real_escape_string($connection, $username);
    $password = mysqli_real_escape_string($connection, $password);

    $query = "SELECT * FROM users WHERE username = '{$username}'";
    $select_user_query = mysqli_query($connection, $query);

    if (!$select_user_query) {
        die("FAILED" . mysqli_error($connection));
    }

    while ($row = mysqli_fetch_array($select_user_query)) {
        echo $db_user_id = $row["user_id"];
        echo $db_username = $row["username"];
        echo $db_user_password = $row["user_password"];
        echo $db_user_firstname = $row["user_firstname"];
        echo $db_user_lastname = $row["user_lastname"];
        echo $db_user_role = $row["user_role"];

        $password = crypt($password, $db_user_password); // ?

        if ($username === $db_username && $password === $db_user_password) {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $_SESSION["user_id"] = $db_user_id;
            $_SESSION["username"] = $db_username;
            $_SESSION["firstname"] = $db_user_firstname;
            $_SESSION["lastname"] = $db_user_lastname;
            $_SESSION["user_role"] = $db_user_role;

            redirect("../admin/index.php");
        } else {
            return false;
        }
    }
    return true;
}

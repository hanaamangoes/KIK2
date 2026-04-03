<?php
session_start();

$users = [
    ['email' => 'admin@mountster.com', 'password' => 'admin123', 'role' => 'admin'],
    ['email' => 'cust1@gmail.com', 'password' => 'user123', 'role' => 'user']
];

function login($email, $password) {
    global $users;
    foreach ($users as $user) {
        if ($user['email'] == $email && $user['password'] == $password) {
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            return $user['role'];
        }
    }
    return false;
}

function logout() {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
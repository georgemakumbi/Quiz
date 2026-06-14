<?php
session_start();
require_once __DIR__ . '/db.php';

if (isset($_SESSION['user'])) {
    header('Location: home.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = [];

    if ($name === '') {
        $errors[] = 'Name is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if ($password === '' || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = 'Email already registered.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');

            if ($insert->execute([$name, $email, $passwordHash])) {
                $_SESSION['user'] = [
                    'id' => $pdo->lastInsertId(),
                    'name' => $name,
                    'email' => $email,
                ];
                header('Location: home.php');
                exit;
            }

            $errors[] = 'Unable to create account. Please try again.';
        }
    }

    foreach ($errors as $error) {
        echo htmlspecialchars($error) . '<br>';
    }
} else {
    echo 'No form data submitted.';
}

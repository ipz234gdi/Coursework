<?php

require_once __DIR__ . '/../../config/database.php';

class AuthController
{
    public function showRegister()
    {
        ob_start();
        require __DIR__ . '/../Views/auth/register.php';
        $Register = ob_get_clean();
        require __DIR__ . '/../Views/main.php';
    }

    public function showLogin()
    {
        ob_start();
        require __DIR__ . '/../Views/auth/login.php';
        $Login = ob_get_clean();
        require __DIR__ . '/../Views/main.php';
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Метод не дозволено";
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $avatar = null; // Поки без аватару

        // Перевірка
        if (!$username || !$email || !$password) {
            echo "Усі поля обов'язкові!";
            return;
        }

        try {
            $pdo = db();

            // Перевірка, чи такий email вже є
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                echo "Користувач з таким email вже існує.";
                return;
            }

            // Хешування пароля
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Додавання користувача
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, avatar_url, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$username, $email, $hash, $avatar]);

            // Редірект
            header("Location: /home");
            exit;

        } catch (PDOException $e) {
            echo "Помилка БД: " . $e->getMessage();
        }
    }

    public function login()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Метод не дозволено";
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            echo "Введіть email та пароль.";
            return;
        }

        try {
            $pdo = db();

            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'avatar_url' => $user['avatar_url']
                ];
                header("Location: /home");
                exit;
            } else {
                echo "Невірний логін або пароль.";
            }

        } catch (PDOException $e) {
            echo "Помилка БД: " . $e->getMessage();
        }
    }
}

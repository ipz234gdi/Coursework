<?php

$title = "Реєстрація";
?>

<h2>Реєстрація</h2>

<form method="POST" action="/register" style="max-width: 400px; margin: auto;">
    <label for="username">Ім’я користувача:</label><br>
    <input type="text" name="username" id="username" required><br><br>

    <label for="email">Email:</label><br>
    <input type="email" name="email" id="email" required><br><br>

    <label for="password">Пароль:</label><br>
    <input type="password" name="password" id="password" required><br><br>

    <button type="submit">Зареєструватися</button>
</form>

<p>Вже маєте акаунт? <a href="/login">Увійдіть</a></p>

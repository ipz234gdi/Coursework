<?php

$title = "Логін";

?>

<h2>Вхід</h2>

<form method="POST" action="/login" style="max-width: 400px; margin: auto;">
    <label for="email">Email:</label><br>
    <input type="email" name="email" id="email" required><br><br>

    <label for="password">Пароль:</label><br>
    <input type="password" name="password" id="password" required><br><br>

    <button type="submit">Увійти</button>
</form>

<p>Ще не маєш акаунту? <a href="/register">Зареєструватись</a></p>
<?php
$title = "Створити пост";
?>

<h2>Створити новий пост</h2>

<form method="POST" action="/posts/create" style="max-width: 600px; margin: auto;">
    <label>Заголовок:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Контент:</label><br>
    <textarea name="content" rows="5" required></textarea><br><br>

    <label>URL до зображення (опціонально):</label><br>
    <input type="text" name="media_url"><br><br>

    <button type="submit">Опублікувати</button>
</form>


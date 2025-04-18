<?php
/**
 * Простий рендер шаблону: 
 *  - $view — шлях від папки Views без розширення, наприклад "posts/post_list"
 *  - $data — асоціативний масив змінних для шаблону
 */
function view(string $view, array $data = []): string {
    extract($data, EXTR_SKIP);
    ob_start();
    require __DIR__ . '/Views/' . $view . '.php';
    return ob_get_clean();
}

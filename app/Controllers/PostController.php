<?php
class PostController {
    public function index() {
        echo json_encode(["message" => "Ось список постів"], JSON_UNESCAPED_UNICODE);
    }
}

?>
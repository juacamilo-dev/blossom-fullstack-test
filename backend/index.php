<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', trim($uri, '/'));

$resource = $uri[0] ?? '';
$id = $uri[1] ?? null;

if ($resource === 'transactions') {
    require_once 'routes/transactions.php';
} else {
    http_response_code(404);
    echo json_encode(["error" => "Route not found"]);
}
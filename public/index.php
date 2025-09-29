<?php
// Carrega autoload do Composer
require __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

// Carrega .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// Configurações CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

use App\Core\Router;
use App\Modules\Auth\AuthController;
use App\Modules\Posts\PostController;
use App\Modules\Categories\CategoryController;
use App\Modules\Contacts\ContactController;


$router = new Router();

// Rotas Auth
$router->add("POST", "/auth/register", [new AuthController, "register"]);
$router->add("POST", "/auth/login", [new AuthController, "login"]);
$router->add("GET", "/auth/me", [new AuthController, "me"]);


// Rotas Posts
$router->add("GET", "/posts/category/{category}", [new PostController, "getByCategory"]);
$router->add("GET", "/posts", [new PostController, "index"]);
$router->add("POST", "/posts", [new PostController, "create"]);
$router->add("GET", "/posts/{id}", [new PostController, "show"]);
$router->add("PUT", "/posts/{id}", [new PostController, "update"]);
$router->add("DELETE", "/posts/{id}", [new PostController, "delete"]);

// Rotas de ações específicas
$router->add("PATCH", "/posts/{id}/views", [new PostController, "incrementViews"]);
$router->add("PATCH", "/posts/{id}/rates", [new PostController, "incrementRates"]);

// Rotas Categories
$router->add("GET", "/categories", [new CategoryController, "index"]);
$router->add("GET", "/categories/{id}", [new CategoryController, "show"]);
$router->add("POST", "/categories", [new CategoryController, "create"]);
$router->add("PUT", "/categories/{id}", [new CategoryController, "update"]);
$router->add("DELETE", "/categories/{id}", [new CategoryController, "delete"]);

//Rotas de Contacto
$router->add("POST", "/contacts", [new ContactController, "create"]);
$router->add("GET", "/contacts", [new ContactController, "getAll"]);
$router->add("GET", "/contacts/{id}", [new ContactController, "getById"]);
$router->add("DELETE", "/contacts/{id}", [new ContactController, "delete"]);
$router->add("PATCH", "/contacts/{id}/viewed", [new ContactController, "markAsViewed"]);


$router->run();

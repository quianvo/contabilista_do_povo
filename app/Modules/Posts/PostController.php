<?php

namespace App\Modules\Posts;

use App\Core\Response;
use App\Core\Request;
use App\Core\RequestParser;
use App\Middleware\AuthMiddleware;

class PostController
{
    private $postService;

    public function __construct()
    {
        (new AuthMiddleware())->handle(Request::capture(), function ($request) {});
        $this->postService = new PostService();
    }

    public function index()
    {
        try {
            $page = $_GET['page'] ?? 1;
            $page = max(1, (int)$page);

            $result = $this->postService->getPosts(null, $page);

            Response::json($result);
        } catch (\Exception $e) {
            Response::json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $post = $this->postService->getPostById($id);
        Response::json($post);
    }

    public function create()
    {
        try {
            $validatedData = PostRequest::validateCreate();
            $post = $this->postService->createPost($validatedData);

            Response::json([
                'success' => true,
                'message' => 'Post created successfully',
                'data' => $post
            ], 201);
        } catch (\Exception $e) {
            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function update($id)
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'multipart/form-data') !== false && $_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = RequestParser::parsePutFormData();
        } else {
            $data = array_merge($_POST, $_FILES);
        }

        $validatedData = PostRequest::validateUpdate($data);

        if (isset($data['img']) && is_array($data['img'])) {
            $tempFile = $this->createTempFile($data['img']);
            $validatedData['img'] = $this->postService->handleImageUpload($tempFile);
        }

        $updatedPost = $this->postService->updatePost($id, $validatedData);

        Response::json([
            'message' => 'Post updated successfully',
            'data' => $updatedPost
        ]);
    }

    public function delete($id)
    {
        $this->postService->deletePost($id);
        Response::json(['message' => 'Post deleted']);
    }

    public function incrementViews($id)
    {
        $post = $this->postService->getPostById($id);
        $this->postService->updatePost($id, ['views' => $post['views'] + 1]);
        Response::json(['message' => 'Views incremented']);
    }

    public function incrementRates($id)
    {
        $post = $this->postService->getPostById($id);
        $this->postService->updatePost($id, ['rate' => $post['rate'] + 1]);
        Response::json(['message' => 'Rate incremented']);
    }

    private function createTempFile(array $fileData): array
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'putupload');
        file_put_contents($tempPath, $fileData['content']);

        return [
            'name' => $fileData['filename'],
            'type' => $fileData['content-type'],
            'tmp_name' => $tempPath,
            'error' => 0,
            'size' => $fileData['size']
        ];
    }

    public function getByCategory($category)
    {
        try {
            $page = $_GET['page'] ?? 1;
            $page = max(1, (int)$page);

            $posts = $this->postService->getPosts($category, $page);

            Response::json($posts);
        } catch (\Exception $e) {
            Response::json(['error' => $e->getMessage()], 500);
        }
    }

    public function categories()
    {
        try {
            $categories = $this->postService->getAllCategories();
            Response::json($categories);
        } catch (\Exception $e) {
            Response::json(['error' => $e->getMessage()], 500);
        }
    }
}

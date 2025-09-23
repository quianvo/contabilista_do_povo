<?php

namespace App\Modules\Posts;

class PostService
{
    protected $fileUploadService;

    public function __construct()
    {
        $this->fileUploadService = new PostFileUploadService();
    }

    public function getPosts(?string $category = null, int $page = 1, int $perPage = 3): array
    {
        return PostModel::getPosts($category, $page, $perPage);
    }

    public function getPostById($id)
    {
        $post = PostModel::find($id);

        if (!$post) {
            throw new \RuntimeException('Post not found', 404);
        }

        return $post;
    }

    public function createPost($data)
    {
        if (isset($_FILES['img'])) {
            $data['img'] = $this->fileUploadService->upload($_FILES['img']);
        }

        return PostModel::create($data);
    }

    public function updatePost($id, $data): array
    {
        $currentPost = $this->getPostById($id);

        if (isset($data['img']) && is_array($data['img'])) {
            if (!empty($currentPost['img'])) {
                $this->deleteImage($currentPost['img']);
            }
            $data['img'] = $this->fileUploadService->upload($data['img']);
        }

        $updateData = array_merge($currentPost, $data);
        unset($updateData['id'], $updateData['created_at']);

        $success = PostModel::update($id, $updateData);

        if (!$success) {
            throw new \RuntimeException('Failed to update post', 500);
        }

        return $this->getPostById($id);
    }

    public function deletePost($id)
    {
        $post = $this->getPostById($id);

        if ($post['img']) {
            $this->deleteImage($post['img']);
        }

        return PostModel::delete($id);
    }

    protected function deleteImage($imagePath)
    {
        $fullPath = __DIR__ . '/../../../public' . $imagePath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    public function handleImageUpload(array $file): string
    {
        try {
            return $this->fileUploadService->upload($file);
        } finally {
            if (file_exists($file['tmp_name'])) {
                unlink($file['tmp_name']);
            }
        }
    }

    public function getAllCategories(): array
    {
        return PostModel::getAllCategories();
    }
}

<?php

namespace App\Modules\Posts;

class PostFileUploadService
{
    protected $uploadPath = __DIR__ . '/../../../public/uploads/posts/';
    
public function upload(array $file): string
{
    // Verificações iniciais
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        throw new \RuntimeException('Arquivo temporário inválido', 400);
    }

    // Verificar se o diretório de upload existe
    if (!is_dir($this->uploadPath)) {
        if (!mkdir($this->uploadPath, 0755, true)) {
            throw new \RuntimeException('Não foi possível criar o diretório de upload', 500);
        }
    }

    // Verificar permissões de escrita
    if (!is_writable($this->uploadPath)) {
        throw new \RuntimeException('Diretório de upload não tem permissão de escrita', 500);
    }

    // Gerar nome único para o arquivo
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('post_', true) . '.' . $extension;
    $destination = $this->uploadPath . DIRECTORY_SEPARATOR . $filename;

    // Debug: Verificar caminhos
    error_log("Tentando mover arquivo de: " . $file['tmp_name']);
    error_log("Para: " . $destination);

    // Mover o arquivo
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return '/uploads/posts/' . $filename;
    }

    // Se falhar, tentar cópia alternativa
    if (copy($file['tmp_name'], $destination)) {
        unlink($file['tmp_name']);
        return '/uploads/posts/' . $filename;
    }

    // Obter último erro do PHP
    $error = error_get_last();
    throw new \RuntimeException(sprintf(
        'Falha ao mover arquivo. Erro: %s',
        $error['message'] ?? 'Desconhecido'
    ), 500);
}
}


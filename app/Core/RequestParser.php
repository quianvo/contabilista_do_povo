<?php

namespace App\Core;

class RequestParser
{
    public static function parsePutFormData(): array
    {
        $input = file_get_contents('php://input');
        $boundary = substr($input, 0, strpos($input, "\r\n"));
        
        $parts = array_slice(explode($boundary, $input), 1);
        $data = [];
        
        foreach ($parts as $part) {
            if ($part == "--\r\n") break;
            
            $part = ltrim($part, "\r\n");
            list($headers, $body) = explode("\r\n\r\n", $part, 2);
            
            // Parse headers
            $headers = explode("\r\n", $headers);
            $headerData = [];
            foreach ($headers as $header) {
                if (strpos($header, ':') !== false) {
                    list($name, $value) = explode(':', $header, 2);
                    $headerData[strtolower(trim($name))] = trim($value);
                }
            }
            
            // Parse content-disposition
            if (isset($headerData['content-disposition'])) {
                preg_match('/name="([^"]+)"/', $headerData['content-disposition'], $matches);
                $fieldName = $matches[1] ?? null;
                
                if (isset($headerData['content-disposition']) && strpos($headerData['content-disposition'], 'filename') !== false) {
                    // É um arquivo
                    $fileInfo = [
                        'name' => $fieldName,
                        'filename' => preg_match('/filename="([^"]+)"/', $headerData['content-disposition'], $fmatches) 
                            ? $fmatches[1] 
                            : '',
                        'content-type' => $headerData['content-type'] ?? '',
                        'content' => $body,
                        'error' => 0,
                        'size' => strlen($body)
                    ];
                    
                    $data[$fieldName] = $fileInfo;
                } else {
                    // É um campo normal
                    $data[$fieldName] = substr($body, 0, strlen($body) - 2);
                }
            }
        }
        
        return $data;
    }
}
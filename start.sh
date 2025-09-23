#!/bin/bash
echo "Iniciando containers..."
docker-compose up -d --build

echo "Instalando dependências do composer..."
docker-compose exec app composer install

echo "Aplicação rodando em: http://localhost:8000"
echo "MySQL rodando na porta: 3306"
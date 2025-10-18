# 🚀 Guia de Instalação - LitroCerto API

## Passo a Passo para Instalação

### 1. Construir e Iniciar Containers Docker

```bash
docker-compose up -d --build
```

Aguarde a construção das imagens. Isso pode levar alguns minutos na primeira vez.

### 2. Instalar Dependências do Composer

```bash
docker-compose exec app composer install
```

### 3. Copiar Arquivo de Ambiente

```bash
cp .env.example .env
```

### 4. Gerar Chave da Aplicação

```bash
docker-compose exec app php artisan key:generate
```

### 5. Gerar Chave JWT

```bash
docker-compose exec app php artisan jwt:secret
```

### 6. Executar Migrations

```bash
docker-compose exec app php artisan migrate
```

### 7. Executar Seeders

```bash
docker-compose exec app php artisan db:seed
```

### 8. Gerar Documentação Swagger

```bash
docker-compose exec app php artisan l5-swagger:generate
```

### 9. Ajustar Permissões (se necessário)

```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R litrocerto:www-data storage bootstrap/cache
```

## ✅ Verificação

### Testar API

```bash
curl http://localhost:8000/api/v1/admin/health
```

Resposta esperada:
```json
{
  "status": "healthy",
  "timestamp": "2024-01-01T00:00:00+00:00",
  "services": {
    "database": {
      "healthy": true,
      "message": "Database connection successful"
    },
    "cache": {
      "healthy": true,
      "message": "Cache working"
    }
  },
  "version": "1.0.0"
}
```

### Acessar Swagger

Abra no navegador: http://localhost:8000/api/documentation

### Executar Testes

```bash
docker-compose exec app php artisan test
```

## 🎯 Instalação Rápida (com Make)

Se você tem `make` instalado:

```bash
make install
```

Este comando executa todos os passos acima automaticamente.

## 🐛 Troubleshooting

### Erro: "Connection refused" ao acessar banco

Aguarde alguns segundos para o PostgreSQL inicializar completamente:

```bash
docker-compose logs db
```

### Erro: "Class not found"

Execute:

```bash
docker-compose exec app composer dump-autoload
```

### Erro de permissão no storage

```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Container não inicia

Verifique os logs:

```bash
docker-compose logs app
```

Reconstrua os containers:

```bash
docker-compose down -v
docker-compose up -d --build
```

## 📝 Próximos Passos

1. Importe a Collection Postman: `LitroCerto_API.postman_collection.json`
2. Faça login com um dos usuários padrão (veja README.md)
3. Teste os endpoints da API
4. Explore a documentação Swagger

## 🔗 Links Úteis

- **API:** http://localhost:8000/api/v1
- **Swagger:** http://localhost:8000/api/documentation
- **RabbitMQ Management:** http://localhost:15672 (litrocerto/secret)
- **Health Check:** http://localhost:8000/api/v1/admin/health

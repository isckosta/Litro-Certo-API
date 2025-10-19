# 🚀 Guia de Instalação - LitroCerto API

## 🎯 Instalação Rápida

### Opção 1: Makefile (Linux/Mac/WSL) - **Recomendado**
```bash
make install
```

### Opção 2: Script Batch (Windows)
```bash
install.bat
```

### Opção 3: Manual (Qualquer SO)
Siga os passos abaixo.

---

## 📋 Passo a Passo Manual

### 1. Construir e Iniciar Containers Docker

```bash
docker compose up -d --build
```

Aguarde a construção das imagens. Isso pode levar alguns minutos na primeira vez.

### 2. Copiar Arquivo de Ambiente

```bash
cp .env.example .env
```

### 3. Instalar Dependências do Composer

```bash
docker compose exec app composer update
docker compose exec app composer install
```

### 4. Publicar Configurações dos Vendors

```bash
docker compose exec app php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
docker compose exec app php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
docker compose exec app php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
```

### 5. Gerar Chave da Aplicação

```bash
docker compose exec app php artisan key:generate
```

### 6. Gerar Chave JWT

```bash
docker compose exec app php artisan jwt:secret
```

### 7. Executar Migrations

```bash
docker compose exec app php artisan migrate
```

### 8. Executar Seeders

```bash
docker compose exec app php artisan db:seed
```

### 9. Gerar Documentação Swagger

```bash
docker compose exec app php artisan l5-swagger:generate
```

### 10. Ajustar Permissões (se necessário)

```bash
docker compose exec app chmod -R 775 storage bootstrap/cache
docker compose exec app chown -R litrocerto:www-data storage bootstrap/cache
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
docker compose exec app php artisan test
```

## ⏱️ Tempo Estimado

| Método | Tempo |
|--------|-------|
| `make install` ou `install.bat` | ~5-10 minutos |
| Manual | ~10-15 minutos |

**Nota:** Primeira instalação demora mais devido ao download das imagens Docker.

## 🐛 Troubleshooting

### Erro: "Connection refused" ao acessar banco

Aguarde alguns segundos para o PostgreSQL inicializar completamente:

```bash
docker compose logs db
```

### Erro: "Class not found"

Execute:

```bash
docker compose exec app composer dump-autoload
```

### Erro de permissão no storage

```bash
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### Container não inicia

Verifique os logs:

```bash
docker compose logs app
```

Reconstrua os containers:

```bash
docker compose down -v
docker compose up -d --build
```

## 📝 Próximos Passos

1. **Importe a Collection Postman:** `LitroCerto_API.postman_collection.json`
2. **Faça login com usuário padrão:**
   - Email: `usuario@teste.com.br`
   - Senha: `usuario123`
3. **Teste os endpoints da API**
4. **Explore a documentação Swagger**

## 🔗 Links Úteis

- **API:** http://localhost:8000/api/v1
- **Swagger:** http://localhost:8000/api/documentation
- **RabbitMQ Management:** http://localhost:15672 (litrocerto/secret)
- **pgAdmin:** http://localhost:5050 (admin@litrocerto.com.br/admin123)
- **Grafana:** http://localhost:3000 (admin/admin123)
- **Prometheus:** http://localhost:9090
- **Loki:** http://localhost:3100
- **Redis Insight:** http://localhost:5540
- **Mailhog:** http://localhost:8025
- **MinIO Console:** http://localhost:9001 (litrocerto/litrocerto123)
- **Portainer:** https://localhost:9443
- **Health Check:** http://localhost:8000/api/v1/admin/health

## 👥 Usuários Padrão

| Email | Senha | Role |
|-------|-------|------|
| admin@litrocerto.com.br | admin123 | admin |
| moderador@litrocerto.com.br | moderador123 | moderator |
| usuario@teste.com.br | usuario123 | user |

## 📚 Documentação Adicional

- **README.md** - Visão geral do projeto e comandos Makefile
- **Postman Collection** - Testes prontos para importar
- **Swagger UI** - Documentação interativa da API

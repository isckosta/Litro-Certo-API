# LitroCerto API

API REST para gerenciamento de postos de combustível, preços e avaliações. Sistema completo com geolocalização (PostGIS), autenticação JWT, RBAC e mensageria RabbitMQ.

## 🚀 Stack Tecnológica

- **Framework:** Laravel 12 (PHP 8.3)
- **Banco de Dados:** PostgreSQL 16 + PostGIS
- **Cache/Session:** Redis
- **Mensageria:** RabbitMQ
- **Autenticação:** JWT (tymon/jwt-auth)
- **Autorização:** RBAC (spatie/laravel-permission)
- **Documentação:** L5-Swagger (OpenAPI 3.1)
- **Infraestrutura:** Docker Compose + Nginx + PHP-FPM
- **Testes:** PHPUnit
- **Code Style:** Laravel Pint

## 📋 Pré-requisitos

- Docker & Docker Compose
- Make (opcional, mas recomendado)
- Git

## 🔧 Instalação

### 1. Clone o repositório

```bash
git clone <repository-url>
cd LitroCerto-API
```

### 2. Configure o ambiente

```bash
cp .env.example .env
```

### 3. Instale e inicie o projeto

```bash
make install
```

Este comando irá:
- Construir e iniciar os containers Docker
- Instalar dependências do Composer
- Executar migrations e seeders
- Gerar documentação Swagger
- Configurar chaves JWT

### Instalação Manual (sem Make)

```bash
# Construir e iniciar containers
docker-compose up -d --build

# Instalar dependências
docker-compose exec app composer install

# Copiar .env
cp .env.example .env

# Gerar chave da aplicação
docker-compose exec app php artisan key:generate

# Gerar chave JWT
docker-compose exec app php artisan jwt:secret

# Executar migrations
docker-compose exec app php artisan migrate

# Executar seeders
docker-compose exec app php artisan db:seed

# Gerar documentação Swagger
docker-compose exec app php artisan l5-swagger:generate
```

## 🎯 Comandos Disponíveis (Makefile)

```bash
make up              # Iniciar containers
make down            # Parar containers
make restart         # Reiniciar containers
make logs            # Ver logs dos containers
make shell           # Acessar shell do container app
make composer        # Instalar dependências
make migrate         # Executar migrations
make migrate-fresh   # Recriar banco com seed
make seed            # Executar seeders
make test            # Executar testes PHPUnit
make pint            # Executar Laravel Pint (code style)
make swagger         # Gerar documentação Swagger
make queue           # Iniciar queue worker
make install         # Instalação completa
make clean           # Limpar containers e volumes
make cache-clear     # Limpar caches do Laravel
make optimize        # Otimizar Laravel (cache configs)
```

## 🌐 Endpoints da API

### Base URL
```
http://localhost:8000/api/v1
```

### Autenticação

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| POST | `/auth/register` | Registrar novo usuário |
| POST | `/auth/login` | Login (retorna JWT) |
| POST | `/auth/logout` | Logout (requer autenticação) |
| POST | `/auth/refresh` | Refresh token (requer autenticação) |

### Postos de Combustível

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/stations/nearby` | Buscar postos próximos (requer lat/lng) |
| GET | `/stations/{id}` | Detalhes do posto |
| GET | `/stations/{id}/prices` | Preços do posto |

### Admin

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/admin/health` | Health check do sistema |

## 📚 Documentação

### 🔗 Links Úteis

- **API:** http://localhost:8000/api/v1
- **Swagger UI:** http://localhost:8000/api/documentation
- **RabbitMQ Management:** http://localhost:15672 (litrocerto/secret)
- **pgAdmin:** http://localhost:5050 (admin@litrocerto.com.br/admin123)
- **Grafana:** http://localhost:3000 (admin/admin123)
- **Prometheus:** http://localhost:9090
- **Loki:** http://localhost:3100
- **Redis Insight:** http://localhost:5540
- **Mailhog:** http://localhost:8025
- **MinIO Console:** http://localhost:9001 (litrocerto/litrocerto123)
- **Portainer:** https://localhost:9443 (criar senha no primeiro acesso)

### Postman Collection
Importe o arquivo `LitroCerto_API.postman_collection.json` no Postman.

**Variáveis da Collection:**
- `base_url`: http://localhost:8000/api/v1
- `jwt_token`: (preenchido automaticamente após login)

## 🧪 Testes

### Executar todos os testes
```bash
make test
```

### Executar testes específicos
```bash
docker-compose exec app php artisan test --filter=AuthTest
docker-compose exec app php artisan test --filter=StationTest
```

### Cobertura de Testes
Os testes cobrem:
- ✅ Autenticação (register, login, logout, refresh)
- ✅ Busca de postos por geolocalização
- ✅ Ordenação por distância
- ✅ Detalhes e preços de postos
- ✅ Health check

## 👥 Usuários Padrão (Seeders)

| Email | Senha | Role |
|-------|-------|------|
| admin@litrocerto.com.br | admin123 | admin |
| moderador@litrocerto.com.br | moderador123 | moderator |
| usuario@teste.com.br | usuario123 | user |

## 🗄️ Estrutura do Banco de Dados

### Principais Tabelas

- **users** - Usuários do sistema
- **fuel_stations** - Postos de combustível (com geolocalização)
- **fuel_prices** - Preços dos combustíveis
- **price_reports** - Relatórios de preços enviados por usuários
- **reviews** - Avaliações de postos
- **favorite_stations** - Postos favoritos dos usuários
- **refuels** - Histórico de abastecimentos
- **promotions** - Promoções dos postos
- **notification_tokens** - Tokens para push notifications

## 🔐 Autenticação JWT

### Obter Token

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"usuario@teste.com.br","password":"usuario123"}'
```

### Usar Token nas Requisições

```bash
curl -X GET http://localhost:8000/api/v1/stations/nearby?latitude=-23.561684&longitude=-46.655981 \
  -H "Authorization: Bearer {seu_token_aqui}"
```

## 🐰 RabbitMQ

### Management UI
```
http://localhost:15672
```

**Credenciais:**
- Usuário: litrocerto
- Senha: secret

### Iniciar Queue Worker
```bash
make queue
```

## 🔍 Geolocalização (PostGIS)

A API utiliza PostGIS para consultas geoespaciais eficientes.

### Exemplo de Busca Nearby

```bash
GET /api/v1/stations/nearby?latitude=-23.561684&longitude=-46.655981&radius=10
```

**Parâmetros:**
- `latitude` (required): Latitude do ponto de busca
- `longitude` (required): Longitude do ponto de busca
- `radius` (optional): Raio de busca em km (padrão: 10km, máximo: 50km)

**Resposta:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Posto Shell Paulista",
      "brand": "Shell",
      "address": "Av. Paulista, 1000",
      "latitude": -23.561684,
      "longitude": -46.655981,
      "distance_km": 0.15,
      "current_prices": [...]
    }
  ],
  "meta": {
    "total": 3,
    "radius_km": 10,
    "center": {
      "latitude": -23.561684,
      "longitude": -46.655981
    }
  }
}
```

## 🛠️ Troubleshooting

### Erro de permissão no storage

```bash
make permissions
```

### Limpar todos os caches

```bash
make cache-clear
```

### Recriar banco de dados

```bash
make migrate-fresh
```

### Ver logs em tempo real

```bash
make logs
```

### Container não inicia

```bash
# Verificar logs
docker-compose logs app

# Reconstruir containers
make clean
make build
```

## 📦 Serviços Docker

| Serviço | Porta | Descrição |
|---------|-------|-----------|
| nginx | 8000 | Servidor web |
| app | 9000 | PHP-FPM |
| db | 5432 | PostgreSQL + PostGIS |
| redis | 6379 | Cache/Session |
| rabbitmq | 5672, 15672 | Mensageria + Management UI |

## 🔒 Segurança

- ✅ JWT com refresh token e blacklist
- ✅ RBAC (Role-Based Access Control)
- ✅ Rate limiting configurável
- ✅ CORS configurado
- ✅ Validação de uploads (mimetype)
- ✅ Senhas hasheadas (bcrypt)
- ✅ LGPD compliance ready

## 📝 Logs

Logs estruturados em JSON com correlation-id.

```bash
# Ver logs da aplicação
docker-compose exec app tail -f storage/logs/laravel.log
```

## 🚀 Deploy

Para ambiente de produção:

1. Configure variáveis de ambiente adequadas
2. Desabilite debug: `APP_DEBUG=false`
3. Configure domínio correto em `APP_URL`
4. Use HTTPS
5. Configure backup do banco de dados
6. Configure monitoramento (logs, métricas)

## 📄 Licença

MIT License

## 👨‍💻 Suporte

Para dúvidas ou problemas, abra uma issue no repositório.

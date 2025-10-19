# 🔧 Processo de Instalação Completo - LitroCerto API

## 📋 Usando Makefile (Recomendado)

### Instalação Completa com Um Comando

```bash
make install
```

Este comando executa automaticamente na seguinte ordem:

### 1️⃣ **build** - Construir e Iniciar Containers
```bash
docker-compose up -d --build
```
- Constrói as imagens Docker (PHP 8.3, Nginx, PostgreSQL+PostGIS, Redis, RabbitMQ)
- Inicia todos os containers em background
- Aguarda ~30-60 segundos para todos os serviços iniciarem

### 2️⃣ **composer** - Instalar Dependências PHP
```bash
docker-compose exec app composer install
```
Instala todas as dependências do `composer.json`:
- `laravel/framework` (^12.0)
- `tymon/jwt-auth` (^2.1) - Autenticação JWT
- `spatie/laravel-permission` (^6.0) - RBAC
- `darkaonline/l5-swagger` (^8.6) - OpenAPI/Swagger
- `jhaoda/laravel-postgis` (^6.0) - Geolocalização
- `vladimir-yuldashev/laravel-queue-rabbitmq` (^14.0) - Filas
- `predis/predis` (^2.2) - Redis client

### 3️⃣ **key-generate** - Gerar Chave da Aplicação
```bash
docker-compose exec app php artisan key:generate
```
- Gera a chave `APP_KEY` no arquivo `.env`
- Necessária para criptografia de sessões e cookies

### 4️⃣ **jwt-secret** - Gerar Chave JWT
```bash
docker-compose exec app php artisan jwt:secret
```
- Gera a chave `JWT_SECRET` no arquivo `.env`
- Usada para assinar e verificar tokens JWT

### 5️⃣ **publish-vendors** - Publicar Configurações dos Vendors
```bash
docker-compose exec app php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider" --force
docker-compose exec app php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --force
docker-compose exec app php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider" --force
```
Publica os arquivos de configuração:
- `config/jwt.php` - Configurações JWT
- Migrations do Spatie Permission (roles, permissions)
- `config/l5-swagger.php` - Configurações Swagger

### 6️⃣ **migrate** - Executar Migrations
```bash
docker-compose exec app php artisan migrate
```
Cria todas as tabelas no PostgreSQL:
- `users` - Usuários (com soft deletes)
- `roles` e `permissions` - RBAC (Spatie)
- `fuel_stations` - Postos (com geolocalização PostGIS)
- `fuel_prices` - Preços dos combustíveis
- `price_reports` - Relatórios de preços
- `reviews` - Avaliações
- `favorite_stations` - Favoritos
- `refuels` - Histórico de abastecimentos
- `promotions` - Promoções
- `notification_tokens` - Tokens para push

### 7️⃣ **seed** - Popular Banco de Dados
```bash
docker-compose exec app php artisan db:seed
```
Executa os seeders na ordem:
1. **RolePermissionSeeder** - Cria roles (admin, moderator, user) e permissões
2. **UserSeeder** - Cria 3 usuários padrão
3. **FuelStationSeeder** - Cria 3 postos de exemplo em São Paulo
4. **FuelPriceSeeder** - Adiciona preços para cada posto

### 8️⃣ **swagger** - Gerar Documentação
```bash
docker-compose exec app php artisan l5-swagger:generate
```
- Escaneia as anotações OpenAPI nos Controllers
- Gera `storage/api-docs/api-docs.json`
- Disponibiliza Swagger UI em `/api/documentation`

### ✅ Mensagem Final
```
=========================================
Installation complete!
=========================================
API: http://localhost:8000/api/v1
Swagger UI: http://localhost:8000/api/documentation
RabbitMQ Management: http://localhost:15672 (user: litrocerto, pass: secret)

Default users:
- admin@litrocerto.com.br / admin123
- usuario@teste.com.br / usuario123
```

---

## 🛠️ Comandos Individuais do Makefile

### Gerenciamento de Containers
```bash
make up              # Iniciar containers
make down            # Parar containers
make restart         # Reiniciar containers
make build           # Construir e iniciar
make clean           # Limpar tudo (containers + volumes)
make logs            # Ver logs em tempo real
make shell           # Acessar shell do container
```

### Banco de Dados
```bash
make migrate         # Executar migrations
make migrate-fresh   # Recriar banco com seed
make seed            # Executar seeders
```

### Desenvolvimento
```bash
make test            # Executar testes PHPUnit
make pint            # Verificar code style
make swagger         # Gerar documentação
make queue           # Iniciar queue worker
make queue-listen    # Queue em modo dev
```

### Manutenção
```bash
make cache-clear     # Limpar todos os caches
make optimize        # Otimizar (cache configs)
make permissions     # Corrigir permissões storage
make key-generate    # Gerar APP_KEY
make jwt-secret      # Gerar JWT_SECRET
```

---

## 🔄 Fluxo Completo de Instalação (Passo a Passo)

### Opção 1: Makefile (Linux/Mac/WSL)
```bash
make install
```

### Opção 2: Script Batch (Windows)
```bash
install.bat
```

### Opção 3: Manual (Qualquer SO)
```bash
# 1. Construir containers
docker-compose up -d --build

# 2. Aguardar inicialização (30-60 segundos)
docker-compose logs -f db

# 3. Instalar dependências
docker-compose exec app composer install

# 4. Copiar .env
cp .env.example .env

# 5. Gerar chaves
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan jwt:secret

# 6. Publicar vendors
docker-compose exec app php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
docker-compose exec app php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
docker-compose exec app php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"

# 7. Migrations e seeds
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

# 8. Gerar Swagger
docker-compose exec app php artisan l5-swagger:generate

# 9. Ajustar permissões (se necessário)
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

---

## 🧪 Verificação Pós-Instalação

### 1. Testar Health Check
```bash
curl http://localhost:8000/api/v1/admin/health
```

### 2. Testar Login
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"usuario@teste.com.br","password":"usuario123"}'
```

### 3. Executar Testes
```bash
make test
```

### 4. Acessar Swagger
Abra no navegador: http://localhost:8000/api/documentation

---

## 📊 Tempo Estimado de Instalação

| Etapa | Tempo Estimado |
|-------|----------------|
| Build containers | 3-5 minutos (primeira vez) |
| Composer install | 2-3 minutos |
| Migrations + Seeds | 10-20 segundos |
| Swagger generation | 5-10 segundos |
| **TOTAL** | **~5-10 minutos** |

---

## 🐛 Troubleshooting

### Erro: "Connection refused" ao acessar banco
**Solução:** Aguarde o PostgreSQL inicializar completamente
```bash
docker-compose logs db
# Aguarde até ver: "database system is ready to accept connections"
```

### Erro: "Class not found"
**Solução:** Recarregar autoload
```bash
docker-compose exec app composer dump-autoload
```

### Erro: Permissão negada no storage
**Solução:**
```bash
make permissions
```

### Container não inicia
**Solução:** Verificar logs e reconstruir
```bash
docker-compose logs app
make clean
make build
```

@echo off
echo ========================================
echo  LitroCerto API - Instalacao Automatica
echo ========================================
echo.

echo [1/10] Construindo containers Docker...
docker compose up -d --build
if %errorlevel% neq 0 (
    echo ERRO: Falha ao construir containers
    pause
    exit /b 1
)
echo OK
echo.

echo [2/10] Aguardando containers iniciarem...
timeout /t 10 /nobreak
echo OK
echo.

echo [3/10] Criando arquivo .env...
if not exist .env (
    copy .env.example .env
    echo .env criado com sucesso
) else (
    echo .env ja existe
)
echo OK
echo.

echo [4/10] Instalando dependencias do Composer...
docker compose exec app composer update
docker compose exec app composer install
if %errorlevel% neq 0 (
    echo ERRO: Falha ao instalar dependencias
    pause
    exit /b 1
)
echo OK
echo.

echo [5/10] Gerando chave da aplicacao...
docker compose exec app php artisan key:generate
echo OK
echo.

echo [6/10] Gerando chave JWT...
docker compose exec app php artisan jwt:secret
echo OK
echo.

echo [7/10] Executando migrations...
docker compose exec app php artisan migrate
if %errorlevel% neq 0 (
    echo ERRO: Falha ao executar migrations
    pause
    exit /b 1
)
echo OK
echo.

echo [7/9] Executando seeders...
docker compose exec app php artisan db:seed
if %errorlevel% neq 0 (
    echo ERRO: Falha ao executar seeders
    pause
    exit /b 1
)
echo OK
echo.

echo [9/10] Publicando vendors...
docker compose exec app php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
docker compose exec app php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
docker compose exec app php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
echo OK
echo.

echo [10/10] Gerando documentacao Swagger...
docker compose exec app php artisan l5-swagger:generate
echo OK
echo.

echo [11/11] Ajustando permissoes...
docker compose exec app chmod -R 775 storage bootstrap/cache
echo OK
echo.

echo ========================================
echo  Instalacao concluida com sucesso!
echo ========================================
echo.
echo Acesse:
echo - API: http://localhost:8000/api/v1
echo - Swagger: http://localhost:8000/api/documentation
echo.
echo Monitoring:
echo - Grafana: http://localhost:3000 (admin/admin123)
echo - Prometheus: http://localhost:9090
echo.
echo Database:
echo - pgAdmin: http://localhost:5050 (admin@litrocerto.com.br/admin123)
echo - Redis Insight: http://localhost:5540
echo.
echo Dev Tools:
echo - RabbitMQ: http://localhost:15672 (litrocerto/secret)
echo - Mailhog: http://localhost:8025
echo - MinIO: http://localhost:9001 (litrocerto/litrocerto123)
echo - Portainer: https://localhost:9443
echo.
echo Usuarios padrao:
echo - admin@litrocerto.com.br / admin123
echo - usuario@teste.com.br / usuario123
echo.
pause

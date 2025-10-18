@echo off
echo ========================================
echo  LitroCerto API - Instalacao Automatica
echo ========================================
echo.

echo [1/9] Construindo containers Docker...
docker-compose up -d --build
if %errorlevel% neq 0 (
    echo ERRO: Falha ao construir containers
    pause
    exit /b 1
)
echo OK
echo.

echo [2/9] Aguardando containers iniciarem...
timeout /t 10 /nobreak
echo OK
echo.

echo [3/9] Instalando dependencias do Composer...
docker-compose exec app composer install
if %errorlevel% neq 0 (
    echo ERRO: Falha ao instalar dependencias
    pause
    exit /b 1
)
echo OK
echo.

echo [4/9] Gerando chave da aplicacao...
docker-compose exec app php artisan key:generate
echo OK
echo.

echo [5/9] Gerando chave JWT...
docker-compose exec app php artisan jwt:secret
echo OK
echo.

echo [6/9] Executando migrations...
docker-compose exec app php artisan migrate
if %errorlevel% neq 0 (
    echo ERRO: Falha ao executar migrations
    pause
    exit /b 1
)
echo OK
echo.

echo [7/9] Executando seeders...
docker-compose exec app php artisan db:seed
if %errorlevel% neq 0 (
    echo ERRO: Falha ao executar seeders
    pause
    exit /b 1
)
echo OK
echo.

echo [8/9] Gerando documentacao Swagger...
docker-compose exec app php artisan l5-swagger:generate
echo OK
echo.

echo [9/9] Ajustando permissoes...
docker-compose exec app chmod -R 775 storage bootstrap/cache
echo OK
echo.

echo ========================================
echo  Instalacao concluida com sucesso!
echo ========================================
echo.
echo Acesse:
echo - API: http://localhost:8000/api/v1
echo - Swagger: http://localhost:8000/api/documentation
echo - RabbitMQ: http://localhost:15672
echo.
echo Usuarios padrao:
echo - admin@litrocerto.com.br / admin123
echo - usuario@teste.com.br / usuario123
echo.
pause

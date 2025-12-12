# Demo Projects

Esta carpeta contiene proyectos de demostración de diferentes frameworks PHP para probar el Composer Update Helper.

## Frameworks Incluidos

- **Laravel 12** - Framework moderno (PHP 8.5)
- **Symfony 8.0** - Framework enterprise (PHP 8.5)
- **Yii 2** - Framework rápido (PHP 8.5)
- **CodeIgniter 5** - Framework ligero (PHP 8.5)
- **Slim 5** - Micro-framework (PHP 8.5)
- **Legacy** - Laravel 5.8 con PHP 7.4 (proyecto legacy)

## Requisitos

- Docker y Docker Compose
- Puerto 8001 disponible (configurable en `.env`)

## Configuración

Cada demo tiene su propio archivo `.env.example`. Para configurar un demo:

1. Entra en la carpeta del demo:

```bash
cd laravel  # o symfony, yii, codeigniter, slim, legacy
```

2. Copia el archivo de ejemplo de configuración:

```bash
cp .env.example .env
```

3. Edita el archivo `.env` para configurar el puerto si es necesario:

```env
# Puerto del demo (por defecto según el demo)
PORT=8001
```

## Uso

Cada demo es completamente independiente y tiene su propio `docker-compose.yml`. Puedes levantar cada uno por separado.

### Levantar un demo específico

```bash
# Laravel 11
cd laravel
cp .env.example .env  # Editar PORT si es necesario
docker-compose up -d

# Symfony 7.1
cd symfony
cp .env.example .env  # Editar PORT si es necesario
docker-compose up -d

# Yii 2
cd yii
cp .env.example .env  # Editar PORT si es necesario
docker-compose up -d

# CodeIgniter 4
cd codeigniter
cp .env.example .env  # Editar PORT si es necesario
docker-compose up -d

# Slim 4
cd slim
cp .env.example .env  # Editar PORT si es necesario
docker-compose up -d

# Legacy (Laravel 5.8 + PHP 7.4)
cd legacy
cp .env.example .env  # Editar PORT si es necesario
docker-compose up -d
```

**Nota:** Al iniciar los contenedores por primera vez, se ejecutará automáticamente `composer install` en cada demo, lo que instalará el script `generate-composer-require.sh` del Composer Update Helper en la raíz de cada proyecto.

### Levantar todos los demos

Si quieres levantar todos los demos a la vez, puedes usar un script o ejecutarlos en paralelo:

```bash
# Desde la raíz de demo/
cd demo

# Laravel
cd laravel && docker-compose up -d && cd ..

# Symfony
cd symfony && docker-compose up -d && cd ..

# Yii
cd yii && docker-compose up -d && cd ..

# CodeIgniter
cd codeigniter && docker-compose up -d && cd ..

# Slim
cd slim && docker-compose up -d && cd ..

# Legacy
cd legacy && docker-compose up -d && cd ..
```

### Ver los logs

```bash
# Desde dentro de cada demo
cd laravel
docker-compose logs -f

# Ver logs de un servicio específico
docker-compose logs -f app
docker-compose logs -f db
```

### Detener los demos

```bash
# Desde dentro de cada demo
cd laravel
docker-compose down

# Detener y eliminar volúmenes
docker-compose down -v
```

## Acceso a los Demos

Una vez levantados, los demos estarán disponibles en:

- **Laravel 11**: http://localhost:8001
- **Symfony 7.1**: http://localhost:8002
- **Yii 2**: http://localhost:8003
- **CodeIgniter 4**: http://localhost:8004
- **Slim 4**: http://localhost:8005
- **Legacy (Laravel 5.8)**: http://localhost:8006

## Ejecutar Tests

Cada demo incluye una suite de tests básicos. Para ejecutarlos:

```bash
# Desde dentro de cada demo
cd laravel
docker-compose exec app composer test

# Symfony
cd symfony
docker-compose exec app composer test

# Yii
cd yii
docker-compose exec app composer test

# CodeIgniter
cd codeigniter
docker-compose exec app composer test

# Slim
cd slim
docker-compose exec app composer test

# Legacy
cd legacy
docker-compose exec app composer test
```

## Probar Composer Update Helper

Cada demo tiene el Composer Update Helper instalado como dependencia de desarrollo. El script `generate-composer-require.sh` se instala automáticamente al ejecutar `composer install` (que se ejecuta automáticamente al iniciar los contenedores).

Para probarlo:

```bash
# Desde dentro de cada demo
cd laravel

# Entrar al contenedor
docker-compose exec app sh

# Dentro del contenedor, ejecutar el script (ya está instalado en la raíz)
./generate-composer-require.sh

# O ejecutar directamente con --run para aplicar los cambios
./generate-composer-require.sh --run
```

### Verificar que el script está instalado

```bash
# Desde dentro de cada demo
cd laravel

# Verificar que el script existe
docker-compose exec app ls -la generate-composer-require.sh

# Ver el contenido del script
docker-compose exec app head -20 generate-composer-require.sh
```

### Reinstalar el script manualmente

Si necesitas reinstalar el script manualmente:

```bash
# Desde dentro de cada demo
cd laravel

# Entrar al contenedor
docker-compose exec app sh

# Ejecutar composer install o update
composer install

# O forzar la reinstalación del plugin
composer update nowo-tech/composer-update-helper
```

## Estructura de Carpetas

```
demo/
├── README.md               # Este archivo
├── laravel/                # Demo Laravel 11 (independiente)
│   ├── docker-compose.yml  # Docker Compose específico de Laravel
│   ├── .env.example        # Configuración de ejemplo
│   ├── Dockerfile
│   ├── composer.json
│   └── public/
├── symfony/                # Demo Symfony 7.1 (independiente)
│   ├── docker-compose.yml  # Docker Compose específico de Symfony
│   ├── .env.example        # Configuración de ejemplo
│   ├── Dockerfile
│   ├── composer.json
│   └── public/
├── yii/                    # Demo Yii 2 (independiente)
│   ├── docker-compose.yml  # Docker Compose específico de Yii
│   ├── .env.example        # Configuración de ejemplo
│   ├── Dockerfile
│   ├── composer.json
│   └── web/
├── codeigniter/           # Demo CodeIgniter 4 (independiente)
│   ├── docker-compose.yml  # Docker Compose específico de CodeIgniter
│   ├── .env.example        # Configuración de ejemplo
│   ├── Dockerfile
│   ├── composer.json
│   └── public/
├── slim/                   # Demo Slim 4 (independiente)
│   ├── docker-compose.yml  # Docker Compose específico de Slim
│   ├── .env.example        # Configuración de ejemplo
│   ├── Dockerfile
│   ├── composer.json
│   └── public/
└── legacy/                 # Demo Legacy (Laravel 5.8 + PHP 7.4) (independiente)
    ├── docker-compose.yml  # Docker Compose específico de Legacy
    ├── .env.example        # Configuración de ejemplo
    ├── Dockerfile
    ├── composer.json
    └── public/
```

Cada demo es completamente independiente y puede ejecutarse por separado sin necesidad de los demás.

## Notas

- Cada demo tiene su propia base de datos MySQL
- Los volúmenes de las bases de datos persisten entre reinicios
- Para reiniciar desde cero, usa `docker-compose down -v`
- Los demos son proyectos mínimos para pruebas, no incluyen todas las características de los frameworks


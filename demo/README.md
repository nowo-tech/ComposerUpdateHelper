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

Cada demo es completamente independiente y tiene su propio `docker-compose.yml`. Puedes levantar cada uno por separado usando el Makefile incluido o directamente con docker-compose.

### Usando Makefile (Recomendado)

El Makefile simplifica el manejo de los demos:

```bash
# Ver ayuda
make help

# Levantar un demo específico
make laravel
make symfony
make yii
make codeigniter
make slim
make legacy

# Levantar todos los demos
make all

# Comandos genéricos (requieren DEMO=<nombre>)
make up DEMO=laravel      # Levantar un demo
make down DEMO=laravel    # Detener un demo
make logs DEMO=laravel    # Ver logs
make test DEMO=laravel    # Ejecutar tests
make shell DEMO=laravel   # Abrir shell en el contenedor

# Limpiar todos los demos (detener y eliminar volúmenes)
make clean
```

**Nota:** Al iniciar los contenedores por primera vez, se ejecutará automáticamente `composer install` en cada demo, lo que instalará el script `generate-composer-require.sh` del Composer Update Helper en la raíz de cada proyecto.

### Usando Docker Compose directamente

Si prefieres usar docker-compose directamente:

```bash
# Laravel 12
cd laravel
cp .env.example .env  # Editar PORT si es necesario
docker-compose up -d

# Symfony 8.0
cd symfony
cp .env.example .env  # Editar PORT si es necesario
docker-compose up -d

# Yii 2
cd yii
cp .env.example .env  # Editar PORT si es necesario
docker-compose up -d

# CodeIgniter 5
cd codeigniter
cp .env.example .env  # Editar PORT si es necesario
docker-compose up -d

# Slim 5
cd slim
cp .env.example .env  # Editar PORT si es necesario
docker-compose up -d

# Legacy (Laravel 5.8 + PHP 7.4)
cd legacy
cp .env.example .env  # Editar PORT si es necesario
docker-compose up -d
```

### Ver los logs

```bash
# Usando Makefile
make logs DEMO=laravel

# O directamente con docker-compose
cd laravel
docker-compose logs -f

# Ver logs de un servicio específico
docker-compose logs -f app
docker-compose logs -f db
```

### Detener los demos

```bash
# Usando Makefile
make down DEMO=laravel

# O directamente con docker-compose
cd laravel
docker-compose down

# Detener y eliminar volúmenes
docker-compose down -v
```

## Acceso a los Demos

Una vez levantados, los demos estarán disponibles en:

- **Laravel 11**: http://localhost:8001
- **Symfony 7.1**: http://localhost:8001
- **Yii 2**: http://localhost:8001
- **CodeIgniter 4**: http://localhost:8001
- **Slim 4**: http://localhost:8001
- **Legacy (Laravel 5.8)**: http://localhost:8001

## Ejecutar Tests

Cada demo incluye una suite de tests básicos. Para ejecutarlos:

```bash
# Usando Makefile
make test DEMO=laravel
make test DEMO=symfony
make test DEMO=yii
make test DEMO=codeigniter
make test DEMO=slim
make test DEMO=legacy

# O directamente con docker-compose
cd laravel
docker-compose exec app composer test
```

## Probar Composer Update Helper

Cada demo tiene el Composer Update Helper instalado como dependencia de desarrollo. El script `generate-composer-require.sh` se instala automáticamente al ejecutar `composer install` (que se ejecuta automáticamente al iniciar los contenedores).

Para probarlo:

```bash
# Usando Makefile para abrir shell
make shell DEMO=laravel

# Dentro del contenedor, ejecutar el script (ya está instalado en la raíz)
./generate-composer-require.sh

# O ejecutar directamente con --run para aplicar los cambios
./generate-composer-require.sh --run
```

### Verificar que el script está instalado

```bash
# Usando Makefile
make shell DEMO=laravel
# Dentro del contenedor:
ls -la generate-composer-require.sh
head -20 generate-composer-require.sh

# O directamente con docker-compose
cd laravel
docker-compose exec app ls -la generate-composer-require.sh
docker-compose exec app head -20 generate-composer-require.sh
```

### Reinstalar el script manualmente

Si necesitas reinstalar el script manualmente:

```bash
# Usando Makefile
make shell DEMO=laravel
# Dentro del contenedor:
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


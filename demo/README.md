# Demo Projects

Esta carpeta contiene proyectos de demostración de diferentes frameworks PHP para probar el Composer Update Helper.

## Frameworks Incluidos

- **Laravel 12** - Framework moderno (PHP 8.5) - Última versión estable
- **Symfony 8.0** - Framework enterprise (PHP 8.5) - Última versión estable
- **Yii 2** - Framework rápido (PHP 8.5) - Última versión estable (Yii 3 en desarrollo)
- **CodeIgniter 4.6** - Framework ligero (PHP 8.5) - Última versión estable
- **Slim 4.12** - Micro-framework (PHP 8.5) - Última versión estable
- **Legacy** - Laravel 12 (PHP 8.5) - Actualizado desde Laravel 5.8

## Requisitos

- Docker y Docker Compose
- Puerto 8001 disponible (configurable en `.env`)

## Configuración

Cada demo tiene su propio archivo `.env.example`. Para configurar un demo:

1. Entra en la carpeta del demo:

```bash
cd laravel  # o symfony, yii, codeigniter, slim, legacy
```

2. Copia el archivo de ejemplo de configuración y renómbralo (quita el `.example`):

```bash
cp .env.example .env
```

3. Edita el archivo `.env` para configurar el puerto y las contraseñas de MySQL si es necesario:

```env
# Puerto del demo (por defecto según el demo)
PORT=8001

# MySQL Docker container configuration (¡IMPORTANTE: Cambia estas contraseñas en producción!)
MYSQL_ROOT_PASSWORD=root
MYSQL_PASSWORD=tu_contraseña_segura
```

**Nota:** El Makefile copia automáticamente `.env.example` a `.env` si no existe cuando levantas un demo, pero es recomendable revisar y cambiar las contraseñas por defecto.

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

# Comandos específicos por demo
make laravel-down       # Detener Laravel
make laravel-install    # Instalar dependencias de Laravel
make laravel-test       # Ejecutar tests de Laravel

make symfony-down       # Detener Symfony
make symfony-install    # Instalar dependencias de Symfony
make symfony-test       # Ejecutar tests de Symfony

# (Mismo patrón para yii, codeigniter, slim, legacy)

# Comandos genéricos (requieren DEMO=<nombre>)
make up DEMO=laravel      # Levantar un demo
make down DEMO=laravel    # Detener un demo
make install DEMO=laravel # Instalar dependencias
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
cp .env.example .env  # Copia y renombra (quita .example)
# Edita .env para cambiar PORT y contraseñas MySQL si es necesario
docker-compose up -d

# Symfony 8.0
cd symfony
cp .env.example .env  # Copia y renombra (quita .example)
# Edita .env para cambiar PORT y contraseñas MySQL si es necesario
docker-compose up -d

# Yii 2
cd yii
cp .env.example .env  # Copia y renombra (quita .example)
# Edita .env para cambiar PORT y contraseñas MySQL si es necesario
docker-compose up -d

# CodeIgniter 5
cd codeigniter
cp .env.example .env  # Copia y renombra (quita .example)
# Edita .env para cambiar PORT y contraseñas MySQL si es necesario
docker-compose up -d

# Slim 5
cd slim
cp .env.example .env  # Copia y renombra (quita .example)
# Edita .env para cambiar PORT si es necesario
docker-compose up -d

# Legacy (Laravel 12 + PHP 8.5)
cd legacy
cp .env.example .env  # Copia y renombra (quita .example)
# Edita .env para cambiar PORT y contraseñas MySQL si es necesario
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
# Usando Makefile (comandos específicos)
make laravel-down
make symfony-down
make yii-down
make codeigniter-down
make slim-down
make legacy-down

# O usando comandos genéricos
make down DEMO=laravel

# O directamente con docker-compose
cd laravel
docker-compose down

# Detener y eliminar volúmenes
docker-compose down -v
```

### Instalar dependencias

```bash
# Usando Makefile (comandos específicos)
make laravel-install
make symfony-install
make yii-install
make codeigniter-install
make slim-install
make legacy-install

# O usando comandos genéricos
make install DEMO=laravel

# O directamente con docker-compose
cd laravel
docker-compose up -d
docker-compose exec app composer install
```

## Acceso a los Demos

Una vez levantados, los demos estarán disponibles en:

- **Laravel 12**: http://localhost:8001
- **Symfony 8.0**: http://localhost:8001
- **Yii 2**: http://localhost:8001
- **CodeIgniter 4.6**: http://localhost:8001
- **Slim 4.12**: http://localhost:8001
- **Legacy (Laravel 12)**: http://localhost:8001

**Nota:** Todos los demos usan el puerto 8001 por defecto. El Makefile gestiona automáticamente los conflictos de puertos, deteniendo cualquier contenedor que esté usando el puerto antes de levantar un nuevo demo.

## Ejecutar Tests

Cada demo incluye una suite de tests básicos. Para ejecutarlos:

```bash
# Usando Makefile (comandos específicos)
make laravel-test
make symfony-test
make yii-test
make codeigniter-test
make slim-test
make legacy-test

# O usando comandos genéricos
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

**Nota:** Los comandos de test del Makefile automáticamente levantan el contenedor si no está corriendo antes de ejecutar los tests.

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
├── Makefile                # Makefile para gestionar todos los demos
├── laravel/                # Demo Laravel 12 (independiente)
│   ├── docker-compose.yml  # Docker Compose específico de Laravel
│   ├── .env.example        # Variables de entorno estándar de Laravel + PORT
│   ├── Dockerfile
│   ├── composer.json
│   └── public/
├── symfony/                # Demo Symfony 8.0 (independiente)
│   ├── docker-compose.yml  # Docker Compose específico de Symfony
│   ├── .env.example        # Variables de entorno estándar de Symfony + PORT
│   ├── Dockerfile
│   ├── composer.json
│   └── public/
├── yii/                    # Demo Yii 2 (independiente)
│   ├── docker-compose.yml  # Docker Compose específico de Yii
│   ├── .env.example        # Variables de entorno estándar de Yii + PORT
│   ├── Dockerfile
│   ├── composer.json
│   └── web/
├── codeigniter/           # Demo CodeIgniter 4.6 (independiente)
│   ├── docker-compose.yml  # Docker Compose específico de CodeIgniter
│   ├── .env.example        # Variables de entorno estándar de CodeIgniter + PORT
│   ├── Dockerfile
│   ├── composer.json
│   └── public/
├── slim/                   # Demo Slim 4.12 (independiente)
│   ├── docker-compose.yml  # Docker Compose específico de Slim
│   ├── .env.example        # Variables de entorno estándar de Slim + PORT
│   ├── Dockerfile
│   ├── composer.json
│   └── public/
└── legacy/                 # Demo Legacy (Laravel 12 + PHP 8.5) (independiente)
    ├── docker-compose.yml  # Docker Compose específico de Legacy
    ├── .env.example        # Variables de entorno estándar de Laravel + PORT
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
- Todos los demos usan **PHP 8.5** y las **últimas versiones estables** de cada framework
- Todos los demos usan **PHPUnit 11.0** para testing
- Los archivos `.env.example` incluyen las variables de entorno estándar de cada framework más la configuración `PORT=8001` para Docker y las variables de MySQL
- **Importante:** Debes copiar `.env.example` a `.env` (renombrar quitando `.example`) y cambiar las contraseñas de MySQL por defecto antes de usar en producción
- El Makefile gestiona automáticamente los conflictos de puertos, deteniendo contenedores que ocupen el puerto antes de levantar un nuevo demo
- El Makefile copia automáticamente `.env.example` a `.env` si no existe cuando levantas un demo


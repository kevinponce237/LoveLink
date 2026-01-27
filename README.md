# UsPage

UsPage es una plataforma web diseñada para que usuarios creen landing pages conmemorativas personalizadas para parejas. Desarrollada con **Laravel 12, Vue 3 e Inertia.js**, implementa patrones de arquitectura limpia (Repositorios y Servicios) para mantener el código escalable y testeable desde el inicio.

## 🎯 Propósito del Proyecto

Este es un **MVP (Minimum Viable Product)** creado como proyecto de portafolio para demostrar habilidades en desarrollo backend con patrones de arquitectura profesionales, manejo de base de datos relacional, y prácticas de ingeniería de software. Los usuarios pueden crear y personalizar una landing page con contenido conmemorativo, galerías de fotos y temas visuales.

---

## 🛠️ Stack Tecnológico

| Componente | Tecnología | Versión |
|-----------|-----------|---------|
| **Backend** | Laravel | 12 |
| **PHP** | PHP | 8.4+ |
| **Frontend** | Vue 3 (Composition API) | 3 |
| **Meta-Framework** | Inertia.js | 2 |
| **Estilos** | Tailwind CSS | 4 |
| **Base de Datos** | MySQL/MariaDB | 8.0+ |
| **Gestor de Paquetes (Backend)** | Composer | Latest |
| **Gestor de Paquetes (Frontend)** | npm | Latest |
| **Herramientas** | Vite, Laravel Sail | Latest |

---

## 📋 Características Principales

- ✅ **Autenticación:** Registro e inicio de sesión con Laravel Breeze.
- ✅ **Crear Landings:** Un usuario puede crear múltiples landings con slug único.
- ✅ **Personalización Básica:** Editar nombres, fecha de aniversario, bio, colores y fondos del tema.
- ✅ **Galería de Fotos:** Subir imágenes (JPG, PNG, WebP, máx. 5 MB) con URL pública y thumbnails opcionales.
- ✅ **Temas Personalizables:** Catálogo de temas predefinidos con colores y fondos editables.
- ✅ **Invitación San Valentín:** Página especial con mensaje personalizable y botones (futuro: GIFs personalizados).
- ✅ **Visualización Pública:** Acceso a landing via URL amigable: `/p/{slug}`.
- ✅ **Arquitectura Profesional:** Patrón Repository + Service para código mantenible; SystemControl centraliza límites de media.

---

## 🏗️ Arquitectura

El proyecto sigue los principios de **Clean Architecture**:

```
┌─────────────────────────────────────────┐
│   Capa de Presentación (Controllers)    │
│        (Controladores Slim)             │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│   Capa de Servicio (Services)           │
│   (Lógica de Negocio Pura)              │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│   Capa de Acceso a Datos (Repositories) │
│   (Abstracción sobre Eloquent)          │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│   Capa de Datos (Eloquent Models)       │
│   (Mapeo Relacional de Objetos)         │
└─────────────────────────────────────────┘
```

### Estructura de Carpetas

```
uspage/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Controladores (orquestadores)
│   │   ├── Middleware/           # Middlewares
│   │   └── Requests/             # Form Requests (validación)
│   ├── Models/                   # Modelos Eloquent
│   ├── Repositories/             # Capa de Acceso a Datos
│   │   ├── Interfaces/           # Contratos de repositorios
│   │   └── Eloquent/             # Implementaciones con Eloquent
│   ├── Services/                 # Capa de Negocio
│   └── Providers/                # Service Providers
├── resources/
│   ├── js/
│   │   ├── components/           # Componentes Vue reutilizables
│   │   ├── pages/                # Páginas (vistas Inertia)
│   │   ├── layouts/              # Layouts reutilizables
│   │   ├── composables/          # Composables de Vue
│   │   ├── actions/              # Wayfinder (rutas tipadas)
│   │   └── types/                # Tipos TypeScript
│   └── css/
│       └── app.css               # Estilos globales (Tailwind)
├── routes/
│   ├── web.php                   # Rutas web públicas
│   └── console.php               # Comandos Artisan
├── database/
│   ├── migrations/               # Migraciones
│   ├── factories/                # Factories para testing
│   └── seeders/                  # Seeders para datos iniciales
├── tests/
│   ├── Feature/                  # Tests de características
│   └── Unit/                     # Tests unitarios
├── docs/                         # Documentación del proyecto
│   ├── requirements.md           # Requerimientos funcionales
│   ├── domain.md                 # Modelo de dominio
│   └── architecture.md           # Guía de arquitectura
├── bootstrap/
│   ├── app.php                   # Configuración de la app
│   └── providers.php             # Providers registrados
├── config/                       # Archivos de configuración
├── public/                       # Activos públicos
├── storage/                      # Almacenamiento (logs, cache)
├── vendor/                       # Dependencias (Composer)
├── .env.example                  # Variables de entorno (plantilla)
├── composer.json                 # Dependencias de PHP
├── package.json                  # Dependencias de Node.js
├── vite.config.ts                # Configuración de Vite
├── tailwind.config.js            # Configuración de Tailwind CSS
└── phpunit.xml                   # Configuración de PHPUnit
```

---

## 🚀 Instalación y Configuración

### Requisitos Previos

- **PHP 8.4+** con extensiones: `curl`, `json`, `mbstring`, `tokenizer`, `xml`
- **Composer** (gestor de dependencias de PHP)
- **Node.js 18+** y **npm 9+**
- **MySQL 8.0+** o **MariaDB 10.6+**
- **Git** (control de versiones)

### Pasos de Instalación

#### 1. Clonar el Repositorio

```bash
git clone https://github.com/tu-usuario/uspage.git
cd uspage
```

#### 2. Instalar Dependencias PHP

```bash
composer install
```

#### 3. Instalar Dependencias Node.js

```bash
npm install
```

#### 4. Configurar Variables de Entorno

```bash
cp .env.example .env
```

Edita `.env` y configura:
- `APP_NAME`, `APP_URL`, `APP_DEBUG`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `MAIL_*` (si usas correo)
- **Digital Ocean Spaces** (para almacenamiento de media):
  - `AWS_ACCESS_KEY_ID`: Tu Access Key de DO Spaces
  - `AWS_SECRET_ACCESS_KEY`: Tu Secret Key de DO Spaces
  - `MEDIA_STORAGE_DRIVER=s3` (para producción) o `local` (para desarrollo)

#### 5. Generar Clave de Aplicación

```bash
php artisan key:generate
```

#### 6. Ejecutar Migraciones

```bash
php artisan migrate
```

#### 7. (Opcional) Poblar Base de Datos

```bash
php artisan db:seed
```

#### 8. Iniciar el Servidor de Desarrollo

En una terminal:

```bash
php artisan serve
```

En otra terminal (para compilar assets):

```bash
npm run dev
```

La aplicación estará disponible en `http://localhost:8000`.

---

## 🌐 Almacenamiento de Media (Digital Ocean Spaces)

El proyecto está configurado para usar **Digital Ocean Spaces** (Amsterdam) para almacenamiento de imágenes en producción, con fallback local para desarrollo.

### Configuración de Producción

```bash
# En .env para producción:
MEDIA_STORAGE_DRIVER=s3
AWS_ACCESS_KEY_ID=tu_do_spaces_access_key
AWS_SECRET_ACCESS_KEY=tu_do_spaces_secret_key
AWS_BUCKET=uspage-storage
```

### Configuración de Desarrollo

```bash
# En .env para desarrollo:
MEDIA_STORAGE_DRIVER=local
```

### ¿Por qué Amsterdam?

- ✅ **Latencia óptima**: ~20-30ms desde España
- ✅ **Conectividad LATAM**: Excelentes rutas a América Latina
- ✅ **Compliance EU**: Cumple con GDPR
- ✅ **Costo-efectivo**: Mejor precio que AWS S3

### Características

- **Límite por imagen**: 10MB máximo
- **Formatos soportados**: JPG, PNG, WebP, GIF
- **CDN automático**: URLs optimizadas globalmente
- **Backup automático**: Digital Ocean maneja redundancia

---

## 📝 Comandos Útiles

### Laravel (Backend)

```bash
# Crear una migración
php artisan make:migration create_table_name

# Crear un modelo con migración
php artisan make:model ModelName -m

# Crear un controlador
php artisan make:controller ControllerName

# Ejecutar migraciones
php artisan migrate

# Revertir última migración
php artisan migrate:rollback

# Ejecutar tests
php artisan test

# Ejecutar tests de un archivo específico
php artisan test tests/Feature/ExampleTest.php

# Formatear código con Pint
vendor/bin/pint

# Ejecutar Tinker (REPL interactivo)
php artisan tinker

# Crear enlace simbólico para storage público (solo desarrollo)
php artisan storage:link

# Test de conectividad con Digital Ocean Spaces
php artisan tinker
# Dentro de tinker: Storage::disk('media_cloud')->put('test.txt', 'Hello DO!');
```

### Frontend (Node.js)

```bash
# Compilar assets para desarrollo
npm run dev

# Compilar assets para producción
npm run build

# Ver vista previa de producción
npm run preview
```

---

## 🗄️ Base de Datos

El proyecto utiliza **MySQL/MariaDB** siguiendo la **Tercera Forma Normal (3NF)** para garantizar integridad referencial y evitar redundancia de datos.

### Diagrama de Entidades

```
┌─────────────────┐
│     Users       │
├─────────────────┤
│ id (PK)         │
│ email (UNIQUE)  │
│ password        │
│ timestamps      │
└─────────────────┘
         │
         │ 1:1
         │
┌─────────────────────────────┐
│       Landings              │
├─────────────────────────────┤
│ id (PK)                     │
│ user_id (FK)                │
│ theme_id (FK)               │
│ slug (UNIQUE)               │
│ couple_names                │
│ anniversary_date            │
│ bio_text                    │
│ music_url (nullable)        │
│ timestamps                  │
└─────────────────────────────┘
         │
         │ 1:N
         │
┌─────────────────────────────┐
│       Media                 │
├─────────────────────────────┤
│ id (PK)                     │
│ landing_id (FK)             │
│ file_path                   │
│ type (image/video)          │
│ order                       │
│ timestamps                  │
└─────────────────────────────┘

┌─────────────────┐
│     Themes      │
├─────────────────┤
│ id (PK)         │
│ name            │
│ css_class       │
│ config_json     │
│ timestamps      │
└─────────────────┘
```

---

## 🧪 Testing

El proyecto utiliza **PHPUnit** para garantizar la calidad del código.

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar tests con output compacto
php artisan test --compact

# Ejecutar un archivo de test específico
php artisan test tests/Feature/DashboardTest.php

# Ejecutar tests con patrón de nombre
php artisan test --filter=testMethodName
```

---

## 🔒 Seguridad (Básica para MVP)

- ✅ **Autenticación:** Laravel Breeze con sesiones
- ✅ **CSRF Protection:** Tokens en formularios
- ✅ **Validación:** Form Requests en cada controller
- ✅ **Sanitización:** Slugs normalizados, sin caracteres especiales
- ✅ **Hashing:** Contraseñas con bcrypt
- ✅ **Autorización:** Policies para verificar propietario de landing
- ⚠️ **Rate Limiting:** No implementado en MVP (futuro)

---

## � Monitorización en Desarrollo

El proyecto utiliza **Laravel Telescope** para debuguear requests, queries, y eventos en tiempo real:

```bash
php artisan telescope:publish
# Accesible en http://localhost:8000/telescope
```

---

## 📚 Documentación del Proyecto

Consulta la carpeta `docs/` para detalles técnicos:

- **[requirements.md](docs/requirements.md)** - Requerimientos funcionales (RF) y no funcionales (RNF)
- **[domain.md](docs/domain.md)** - Modelo de dominio, entidades, ER, SystemControl
- **[use-cases.md](docs/use-cases.md)** - Casos de uso (incluye invitación San Valentín)
- **[class-diagram.md](docs/class-diagram.md)** - Diagramas UML de clases, services y repositorios

**Nota:** Este es un MVP; la documentación se enfoca en lo esencial.

---

## 🤝 Contribución

1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/amazing-feature`)
3. Commit tus cambios (`git commit -m 'Add amazing feature'`)
4. Push a la rama (`git push origin feature/amazing-feature`)
5. Abre un Pull Request

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Consulta el archivo `LICENSE` para más detalles.

---

## 👨‍💻 Autor

**Kevin** - Desarrollador Backend | Estudiando Ingeniería de Software

Este proyecto es parte de mi portafolio para demostrar conocimientos en:
- Arquitectura de software (Repositories, Services)
- Modelado de datos (3NF)
- Testing unitario y funcional
- Frontend con Vue 3 + Inertia (aprendizaje en progreso)

---

**Última actualización:** Enero 2026

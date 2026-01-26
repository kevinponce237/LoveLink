# Modelo de Dominio - UsPage

Documento que define las entidades, relaciones y conceptos fundamentales del proyecto UsPage.

---

## 📋 Tabla de Contenidos

1. [Descripción del Dominio](#descripción-del-dominio)
2. [Entidades](#entidades)
3. [Diagrama Entidad-Relación (ER)](#diagrama-entidad-relación)
4. [Relaciones](#relaciones)
5. [Reglas de Negocio](#reglas-de-negocio)

---

## Descripción del Dominio

UsPage es una plataforma que permite a usuarios autenticados crear landing pages conmemorativas personalizadas para parejas.

**Conceptos clave:**

- **Usuario:** Registra y autentica; propietario de una landing
- **Landing Page:** Página conmemorativa única por usuario, con slug público
- **Tema:** Estilos visuales personalizables (colores, fondo)
- **Media:** Imágenes asociadas a la landing
- **Slug:** Identificador único y amigable para URL pública

---

## Entidades

### User

Representa un usuario registrado en el sistema.

| Campo | Tipo | Restricción |
|-------|------|------------|
| `id` | INT | PK, AUTO_INCREMENT |
| `email` | VARCHAR(255) | UNIQUE, NOT NULL |
| `password` | VARCHAR(255) | Hashed (bcrypt), NOT NULL |
| `name` | VARCHAR(255) | Nullable |
| `created_at` | TIMESTAMP | Automático |
| `updated_at` | TIMESTAMP | Automático |
| `deleted_at` | TIMESTAMP | Soft delete (nullable) |

**Restricciones:**
- Email único a nivel de BD
- Contraseña mínimo 8 caracteres

---

### Landing

Página conmemorativa asociada a un usuario.

| Campo | Tipo | Restricción |
|-------|------|------------|
| `id` | INT | PK, AUTO_INCREMENT |
| `user_id` | INT | FK → Users (UNIQUE, 1:1) |
| `theme_id` | INT | FK → Themes (NOT NULL) |
| `slug` | VARCHAR(50) | UNIQUE, NOT NULL |
| `couple_names` | VARCHAR(200) | NOT NULL |
| `anniversary_date` | DATE | NOT NULL |
| `bio_text` | LONGTEXT | Nullable |
| `is_published` | BOOLEAN | DEFAULT TRUE |
| `created_at` | TIMESTAMP | Automático |
| `updated_at` | TIMESTAMP | Automático |
| `deleted_at` | TIMESTAMP | Soft delete |

**Restricciones:**
- Slug: 3-50 caracteres, alfanuméricos + guiones, único
- `user_id` UNIQUE → Un usuario = una landing
- Validación de slug: no caracteres especiales

---

### Theme

Catálogo de temas visuales personalizables.

| Campo | Tipo | Restricción |
|-------|------|------------|
| `id` | INT | PK, AUTO_INCREMENT |
| `name` | VARCHAR(100) | NOT NULL |
| `slug` | VARCHAR(100) | UNIQUE |
| `description` | TEXT | Nullable |
| `primary_color` | VARCHAR(7) | Ej: #FF5733 |
| `secondary_color` | VARCHAR(7) | Ej: #FFC300 |
| `bg_color` | VARCHAR(7) | Color de fondo |
| `bg_image_url` | VARCHAR(500) | Nullable |
| `css_class` | VARCHAR(100) | Clase CSS principal |
| `is_active` | BOOLEAN | DEFAULT TRUE |
| `created_at` | TIMESTAMP | Automático |

**Ejemplo de Theme:**

```
id: 1
name: "Elegante Dorado"
slug: "elegante-dorado"
primary_color: "#FFD700"
secondary_color: "#FFF"
bg_color: "#F5F5F5"
css_class: "theme-elegant-gold"
```

---

### Media

Imágenes asociadas a una landing.

| Campo | Tipo | Restricción |
|-------|------|------------|
| `id` | INT | PK, AUTO_INCREMENT |
| `landing_id` | INT | FK → Landings |
| `file_path` | VARCHAR(500) | URL del archivo |
| `type` | ENUM | 'image' (MVP) |
| `mime_type` | VARCHAR(50) | Ej: image/jpeg |
| `file_size` | INT | Bytes |
| `sort_order` | INT | Orden en galería |
| `is_active` | BOOLEAN | DEFAULT TRUE |
| `created_at` | TIMESTAMP | Automático |

**Restricciones:**
- Máximo 50 media por landing
- Tipos: JPG, PNG, WebP
- Tamaño máximo: 5 MB
- Soft delete lógico

---

## Diagrama Entidad-Relación

```
┌────────────────────────────────┐
│         USERS                  │
├────────────────────────────────┤
│ id (PK)                        │
│ email (UNIQUE)                 │
│ password                       │
│ name                           │
│ created_at, updated_at         │
│ deleted_at (soft delete)       │
└────────────────────────────────┘
           │
           │ 1:1 (user_id UNIQUE)
           │ ON DELETE CASCADE
           │
┌────────────────────────────────────────────┐
│          LANDINGS                          │
├────────────────────────────────────────────┤
│ id (PK)                                    │
│ user_id (FK, UNIQUE)                       │
│ theme_id (FK) ─────────────┐               │
│ slug (UNIQUE)              │               │
│ couple_names               │               │
│ anniversary_date           │               │
│ bio_text                   │               │
│ is_published               │               │
│ created_at, updated_at     │               │
│ deleted_at (soft delete)   │               │
└────────────────────────────────────────────┘
           │
           │ 1:N (landing_id)
           │
┌────────────────────────────────┐
│          MEDIA                 │
├────────────────────────────────┤
│ id (PK)                        │
│ landing_id (FK)                │
│ file_path                      │
│ type (image)                   │
│ mime_type                      │
│ file_size                      │
│ sort_order                     │
│ is_active                      │
│ created_at                     │
└────────────────────────────────┘

           M:1 ────────────┐
                           │
┌────────────────────────────────┐
│         THEMES                 │
├────────────────────────────────┤
│ id (PK)                        │
│ name                           │
│ slug (UNIQUE)                  │
│ primary_color                  │
│ secondary_color                │
│ bg_color                       │
│ bg_image_url                   │
│ css_class                      │
│ is_active                      │
│ created_at                     │
└────────────────────────────────┘
```

**Cumplimiento de 3NF:**

✅ **1NF:** Todos los valores son atómicos
✅ **2NF:** Sin dependencias parciales
✅ **3NF:** `Themes` y `Media` separados evitan redundancia

---

## Relaciones

### User ↔ Landing (1:1)

- Un usuario tiene exactamente una landing
- `user_id` en tabla `landings` es UNIQUE
- ON DELETE CASCADE: Al borrar usuario, se borra landing

```php
// User.php
public function landing(): HasOne
{
    return $this->hasOne(Landing::class);
}

// Landing.php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

---

### Landing ↔ Theme (M:1)

- Muchas landings pueden usar el mismo tema
- El usuario puede cambiar de tema sin perder contenido
- ON DELETE RESTRICT: No se puede borrar tema si hay landings usándolo

```php
// Landing.php
public function theme(): BelongsTo
{
    return $this->belongsTo(Theme::class);
}

// Theme.php
public function landings(): HasMany
{
    return $this->hasMany(Landing::class);
}
```

---

### Landing ↔ Media (1:N)

- Una landing tiene múltiples imágenes
- Las imágenes no existen sin landing
- ON DELETE CASCADE: Al borrar landing, se borran imágenes

```php
// Landing.php
public function media(): HasMany
{
    return $this->hasMany(Media::class)
        ->where('is_active', true)
        ->orderBy('sort_order');
}

// Media.php
public function landing(): BelongsTo
{
    return $this->belongsTo(Landing::class);
}
```

---

## Reglas de Negocio

### RN1: Generación de Slug

El slug se genera automáticamente a partir del nombre de pareja.

```
Algoritmo:
1. Convertir a minúsculas
2. Remover acentos (á→a, é→e, ñ→n)
3. Reemplazar espacios por guiones
4. Remover caracteres no alfanuméricos (excepto guiones)
5. Validar patrón: ^[a-z0-9\-]{3,50}$
6. Verificar unicidad en BD

Ejemplo:
- Entrada: "Juan & María López"
- Salida: "juan-maria-lopez"
- Si existe, generar: "juan-maria-lopez-1"
```

---

### RN2: Un Usuario = Una Landing

Cada usuario autenticado puede crear **solo una landing page**.

```
Validación:
- Al crear landing, verificar que user->landing sea null
- Implementar en LandingService::createNewLanding()
- Lanzar UserAlreadyHasLandingException si existe
```

---

### RN3: Personalización de Tema

El usuario selecciona un tema base y personaliza colores/fondo.

```
Campos editables:
- primary_color (color primario)
- secondary_color (color secundario)
- bg_color (color de fondo)
- bg_image_url (imagen de fondo)

Los cambios se guardan en Landing, no en Theme
```

---

### RN4: Publicación de Landing

El usuario controla la visibilidad de su landing.

```
Estados:
- is_published = false → Solo accesible para propietario (draft)
- is_published = true → Accesible públicamente vía /p/{slug}

Ruta pública valida: is_published && exists(slug)
```

---

### RN5: Soft Delete

Landings eliminadas se marcan pero no se borran físicamente.

```
Implementación:
- Modelo Landing usa SoftDeletes trait
- Campo deleted_at NULL = activa, filled = eliminada
- Queries no devuelven landings eliminadas por defecto
- Solo el propietario puede ver su landing eliminada
```

---

### RN6: Límite de Imágenes

Máximo 50 imágenes por landing.

```
Validación en MediaService::uploadImage()
- Contar media activas: Media::where('landing_id', $id)
                              ->where('is_active', true)
                              ->count()
- Si count >= 50, rechazar nueva carga
```

---

## Patrón Repository

La arquitectura separa acceso a datos de lógica de negocio:

```php
// LandingRepositoryInterface
interface LandingRepositoryInterface {
    public function findBySlug(string $slug): ?Landing;
    public function findByUser(User $user): ?Landing;
    public function create(array $data): Landing;
    public function update(int $id, array $data): Landing;
    public function delete(int $id): void;
}

// EloquentLandingRepository
class EloquentLandingRepository implements LandingRepositoryInterface {
    public function __construct(private Landing $model) {}
    
    public function findBySlug(string $slug): ?Landing {
        return $this->model->where('slug', $slug)
            ->where('is_published', true)
            ->first();
    }
    // ... otros métodos
}

// LandingService
class LandingService {
    public function __construct(
        private LandingRepositoryInterface $repo,
        private SlugService $slugService
    ) {}
    
    public function createNewLanding(User $user, array $data): Landing {
        // Generar slug
        $slug = $this->slugService->generate($data['couple_names']);
        
        // Crear via repositorio
        return $this->repo->create([
            'user_id' => $user->id,
            'theme_id' => $data['theme_id'],
            'slug' => $slug,
            'couple_names' => $data['couple_names'],
            'anniversary_date' => $data['anniversary_date'],
        ]);
    }
}
```

---

**Versión:** 1.0  
**Última actualización:** Enero 2026  
**Autor:** Kevin (Equipo de Desarrollo)

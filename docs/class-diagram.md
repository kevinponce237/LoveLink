# Diagrama de Clases - UsPage

Documento con diagramas UML de clases para la arquitectura del proyecto.

---

## 📋 Tabla de Contenidos

1. [Diagrama de Modelos Eloquent](#diagrama-de-modelos-eloquent)
2. [Diagrama de Services](#diagrama-de-services)
3. [Diagrama de Repositories](#diagrama-de-repositories)
4. [Diagrama Completo de Arquitectura](#diagrama-completo-de-arquitectura)

---

## Diagrama de Modelos Eloquent

Entidades principales del dominio.

```
@startuml

package "Models" {
  class User {
    - id: int
    - email: string
    - password: string
    - name: string
    - created_at: timestamp
    - updated_at: timestamp
    - deleted_at: timestamp (nullable)
    --
    + landings(): HasMany
    + isActive(): bool
  }

  class Landing {
    - id: int
    - user_id: int
    - theme_id: int
    - slug: string
    - couple_names: string
    - anniversary_date: date
    - bio_text: string
    - is_published: boolean
    - created_at: timestamp
    - updated_at: timestamp
    - deleted_at: timestamp (nullable)
    --
    + user(): BelongsTo
    + theme(): BelongsTo
    + media(): HasMany
    + getYearsTogetherAttribute(): int
  }

  class Theme {
    - id: int
    - name: string
    - slug: string
    - description: string
    - primary_color: string
    - secondary_color: string
    - bg_color: string
    - bg_image_url: string (nullable)
    - css_class: string
    - is_active: boolean
    - created_at: timestamp
    --
    + landings(): HasMany
  }

  class Media {
    - id: int
    - landing_id: int
    - file_path: string
    - type: enum
    - mime_type: string
    - file_size: int
    - sort_order: int
    - is_active: boolean
    - created_at: timestamp
    --
    + landing(): BelongsTo
  }
}

User "1" --> "*" Landing : has many
Landing "*" --> "1" Theme : belongs to
Landing "1" --> "*" Media : has many

@enduml
```

---

## Diagrama de Services

Capa de lógica de negocio.

```
@startuml

package "Services" {
  class LandingService {
    - landingRepo: LandingRepositoryInterface
    - mediaService: MediaService
    - slugService: SlugService
    --
    + createNewLanding(user, data): Landing
    + updateLanding(id, data): Landing
    + publishLanding(id): void
    + unpublishLanding(id): void
    + getPublicLanding(slug): ?Landing
    + deleteLanding(id): void
  }

  class MediaService {
    - mediaRepo: MediaRepositoryInterface
    - storageService: StorageService
    --
    + uploadImage(landing, file): Media
    + deleteMedia(id): void
    + reorderGallery(landingId, order): void
    + validateFile(file): bool
    + checkLimit(landingId): bool
  }

  class SlugService {
    --
    + generate(text): string
    + validate(slug): bool
    + sanitize(text): string
    + isUnique(slug): bool
  }

  class ThemeService {
    - themeRepo: ThemeRepositoryInterface
    --
    + getActiveThemes(): Collection
    + getThemeById(id): Theme
    + applyThemeToLanding(landing, themeId): void
  }
}

LandingService --> MediaService : uses
LandingService --> SlugService : uses
LandingService --> ThemeService : uses

@enduml
```

---

## Diagrama de Repositories

Capa de acceso a datos con patrón Repository.

```
@startuml

package "Repositories" {
  package "Interfaces" {
    interface LandingRepositoryInterface {
      + findBySlug(slug): ?Landing
      + findById(id): ?Landing
      + findByUser(user): Collection
      + findPublished(slug): ?Landing
      + create(data): Landing
      + update(id, data): Landing
      + delete(id): void
      + count(userId): int
    }

    interface MediaRepositoryInterface {
      + findByLanding(landingId): Collection
      + create(landingId, data): Media
      + update(id, data): Media
      + delete(id): void
      + reorder(landingId, order): void
      + count(landingId): int
    }

    interface ThemeRepositoryInterface {
      + findById(id): ?Theme
      + findActive(): Collection
      + findBySlug(slug): ?Theme
      + create(data): Theme
      + update(id, data): Theme
    }

    interface UserRepositoryInterface {
      + findByEmail(email): ?User
      + findById(id): ?User
      + create(data): User
      + update(id, data): User
      + delete(id): void
    }
  }

  package "Eloquent" {
    class EloquentLandingRepository {
      - model: Landing
      --
      + findBySlug(slug): ?Landing
      + findById(id): ?Landing
      + findByUser(user): Collection
      + findPublished(slug): ?Landing
      + create(data): Landing
      + update(id, data): Landing
      + delete(id): void
      + count(userId): int
    }

    class EloquentMediaRepository {
      - model: Media
      --
      + findByLanding(landingId): Collection
      + create(landingId, data): Media
      + update(id, data): Media
      + delete(id): void
      + reorder(landingId, order): void
      + count(landingId): int
    }

    class EloquentThemeRepository {
      - model: Theme
      --
      + findById(id): ?Theme
      + findActive(): Collection
      + findBySlug(slug): ?Theme
      + create(data): Theme
      + update(id, data): Theme
    }

    class EloquentUserRepository {
      - model: User
      --
      + findByEmail(email): ?User
      + findById(id): ?User
      + create(data): User
      + update(id, data): User
      + delete(id): void
    }
  }

  EloquentLandingRepository ..|> LandingRepositoryInterface
  EloquentMediaRepository ..|> MediaRepositoryInterface
  EloquentThemeRepository ..|> ThemeRepositoryInterface
  EloquentUserRepository ..|> UserRepositoryInterface
}

@enduml
```

---

## Diagrama Completo de Arquitectura

Integración de todas las capas.

```
@startuml

package "Presentación" {
  class LandingController {
    - landingService: LandingService
    --
    + create(): View
    + store(request): Response
    + edit(id): View
    + update(id, request): Response
    + show(slug): View
    + destroy(id): Response
  }

  class MediaController {
    - mediaService: MediaService
    --
    + store(request): Response
    + destroy(id): Response
    + reorder(request): Response
  }

  class AuthController {
    --
    + register(request): Response
    + login(request): Response
    + logout(): Response
  }
}

package "Lógica de Negocio" {
  class LandingService {
    - landingRepo: LandingRepositoryInterface
    - mediaService: MediaService
    - slugService: SlugService
    --
    + createNewLanding(user, data): Landing
    + updateLanding(id, data): Landing
    + publishLanding(id): void
  }

  class MediaService {
    - mediaRepo: MediaRepositoryInterface
    --
    + uploadImage(landing, file): Media
    + deleteMedia(id): void
  }

  class SlugService {
    --
    + generate(text): string
    + validate(slug): bool
  }
}

package "Acceso a Datos" {
  interface LandingRepositoryInterface {
    + findBySlug(slug): ?Landing
    + create(data): Landing
    + update(id, data): Landing
  }

  interface MediaRepositoryInterface {
    + findByLanding(landingId): Collection
    + create(landingId, data): Media
  }

  class EloquentLandingRepository {
    - model: Landing
  }

  class EloquentMediaRepository {
    - model: Media
  }

  EloquentLandingRepository ..|> LandingRepositoryInterface
  EloquentMediaRepository ..|> MediaRepositoryInterface
}

package "Modelos" {
  class User
  class Landing
  class Theme
  class Media
}

LandingController --> LandingService : uses
MediaController --> MediaService : uses

LandingService --> LandingRepositoryInterface : depends on
LandingService --> MediaService : uses
MediaService --> MediaRepositoryInterface : depends on

LandingRepositoryInterface --> Landing : works with
MediaRepositoryInterface --> Media : works with
Landing --> User : belongs to
Landing --> Theme : belongs to
Landing --> Media : has many

@enduml
```

---

## Flujo de Creación de Landing

Diagrama de secuencia simplificado.

```
@startuml

actor User as user
participant LandingController as ctrl
participant LandingService as svc
participant SlugService as slug
participant LandingRepository as repo
participant Landing as model

user -> ctrl: POST /landings (create form)
ctrl -> svc: createNewLanding(user, data)
svc -> slug: generate(couple_names)
slug --> svc: "juan-maria-lopez"
svc -> repo: create(data + slug)
repo -> model: Landing::create()
model --> repo: landing object
repo --> svc: landing
svc --> ctrl: landing
ctrl --> user: redirect /landings/:id/edit

@enduml
```

---

**Versión:** 1.0  
**Última actualización:** Enero 2026  
**Autor:** Kevin (Equipo de Desarrollo)

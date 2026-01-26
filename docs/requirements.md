# Requerimientos - UsPage

Documento que especifica los requerimientos funcionales (RF) y no funcionales (RNF) del proyecto UsPage en su fase MVP.

---

## 📋 Tabla de Contenidos

1. [Requerimientos Funcionales](#requerimientos-funcionales)
2. [Requerimientos No Funcionales](#requerimientos-no-funcionales)
3. [Criterios de Aceptación](#criterios-de-aceptación)

---

## Requerimientos Funcionales

### RF1: Gestión de Usuarios

El sistema permite registro e inicio de sesión con email y contraseña.

- **RF1.1** - Registro: Email único, contraseña hasheada (bcrypt)
- **RF1.2** - Autenticación: Login con email y contraseña
- **RF1.3** - Sesión: Persistencia en aplicación
- **RF1.4** - Logout: Cerrar sesión

---

### RF2: Creación de Landing Page

Un usuario autenticado puede crear **múltiples landing pages**, cada una con su propio slug único.

- **RF2.1** - Un usuario puede tener N landings (relación 1:N)
- **RF2.2** - Slug único generado automáticamente (3-50 caracteres, alfanumérico + guiones)
- **RF2.3** - Campos: nombres de pareja, fecha de aniversario, bio
- **RF2.4** - Selección de tema base al crear
- **RF2.5** - Estado: draft o published

---

### RF3: Personalización de Landing

El propietario personaliza contenido y apariencia.

- **RF3.1** - Editar nombres, fecha, bio
- **RF3.2** - Cambiar tema (sin perder contenido)
- **RF3.3** - Personalizar colores: primario, secundario, fondo
- **RF3.4** - Cambiar imagen de fondo
- **RF3.5** - Vista previa en tiempo real

---

### RF4: Galería Multimedia

El usuario gestiona imágenes en su landing.

- **RF4.1** - Subir imágenes: JPG, PNG, WebP (máx. 5 MB)
- **RF4.2** - Máximo 50 imágenes por landing
- **RF4.3** - Reordenamiento drag & drop
- **RF4.4** - Eliminación lógica
- **RF4.5** - **(OPCIONAL)** Thumbnails automáticos

---

### RF5: Temas Visuales Personalizables

El usuario selecciona y personaliza un tema.

- **RF5.1** - Catálogo de al menos 3 temas
- **RF5.2** - Cada tema: nombre, colores por defecto, config
- **RF5.3** - Editar colores y fondo sin perder datos
- **RF5.4** - Cambios aplican inmediatamente

---

### RF6: Visualización Pública

Visitantes acceden a landings publicadas.

- **RF6.1** - Ruta: `/p/{slug}`
- **RF6.2** - Solo landings publicadas accesibles
- **RF6.3** - Responsive (mobile-first)
- **RF6.4** - Visualiza: nombres, fecha, bio, galería, tema personalizado

---

### RF7: Validación y Manejo de Errores

- **RF7.1** - Slug: unicidad, formato validado
- **RF7.2** - Email: formato correcto
- **RF7.3** - Archivos: tipo, tamaño, MIME type
- **RF7.4** - Mensajes claros al usuario

---

## Requerimientos No Funcionales

### RNF1: Arquitectura Escalable

- **RNF1.1** - Patrón Repository para acceso a datos
- **RNF1.2** - Capa Service para lógica de negocio
- **RNF1.3** - Controladores delgados
- **RNF1.4** - Form Requests para validación centralizada

---

### RNF2: Base de Datos (3NF)

- **RNF2.1** - Cumplimiento de Tercera Forma Normal
- **RNF2.2** - Tablas: Users, Landings, Themes, Media
- **RNF2.3** - Relaciones definidas: 1:N (User-Landing), M:1 (Landing-Theme), 1:N (Landing-Media)
- **RNF2.4** - Índices en: slug, user_id, theme_id
- **RNF2.5** - Soft delete en Users y Landings

---

### RNF3: Seguridad Básica (MVP)

- **RNF3.1** - Autenticación con Laravel Breeze
- **RNF3.2** - CSRF tokens en formularios
- **RNF3.3** - Sanitización de slugs
- **RNF3.4** - Hashing bcrypt en contraseñas
- **RNF3.5** - Validación en Form Requests
- **RNF3.6** - Policies para autorización (solo propietario edita)

---

### RNF4: Testing

- **RNF4.1** - Tests Feature para casos principales
- **RNF4.2** - Tests Unit para Services
- **RNF4.3** - Cobertura mínima: 60%

---

### RNF5: Rendimiento

- **RNF5.1** - Eager loading (evitar N+1)
- **RNF5.2** - Índices en columnas frecuentes
- **RNF5.3** - **(FUTURO)** Caché de landings públicas

---

### RNF6: Monitorización

- **RNF6.1** - Laravel Telescope en desarrollo
- **RNF6.2** - Logs estructurados para errores

---

### RNF7: Frontend

- **RNF7.1** - Componentes reutilizables Vue
- **RNF7.2** - TypeScript para type safety
- **RNF7.3** - Tailwind CSS para estilos
- **RNF7.4** - Validación en cliente

---

## Criterios de Aceptación

Toda funcionalidad debe cumplir:

✅ **Código:**
- Estándar PSR-12 (Pint)
- Type hints en PHP 8
- Sin errores en análisis estático

✅ **Tests:**
- Mínimo 1 test Feature
- Happy path + 1 caso error

✅ **Seguridad:**
- Validación en Form Requests
- Sin SQL injection (Eloquent)
- Autorización verificada (Policies)

✅ **Mobile:**
- Responsive 320px+
- Funcional en navegadores modernos

---

**Versión:** 1.0  
**Última actualización:** Enero 2026  
**Autor:** Kevin (Equipo de Desarrollo)

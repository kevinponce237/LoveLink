# Casos de Uso - UsPage

Documento que especifica los casos de uso principales del sistema.

---

## 📋 Tabla de Contenidos

1. [Actores](#actores)
2. [Casos de Uso Principales](#casos-de-uso-principales)
3. [Diagramas UML](#diagramas-uml)

---

## Actores

### Usuario Autenticado
Propietario de una o más landing pages. Tiene capacidad de crear, editar y eliminar sus landings.

### Visitante Anónimo
Usuario sin autenticación. Solo puede visualizar landings públicas.

### Administrador (Futuro)
Gestiona temas, monitorización y contenido del sistema.

---

## Casos de Uso Principales

### UC1: Registrarse

**Actor Principal:** Visitante Anónimo  
**Precondiciones:** El visitante no tiene cuenta

**Flujo Principal:**
1. Visitante accede a página de registro
2. Ingresa email y contraseña
3. Sistema valida email único y contraseña
4. Cuenta se crea y usuario se autentica automáticamente
5. Se redirige al dashboard

**Postcondiciones:** Usuario autenticado con cuenta activa

---

### UC2: Iniciar Sesión

**Actor Principal:** Usuario sin autenticar  
**Precondiciones:** Usuario tiene cuenta registrada

**Flujo Principal:**
1. Usuario accede a login
2. Ingresa email y contraseña
3. Sistema valida credenciales
4. Sesión se crea
5. Se redirige al dashboard

**Flujos Alternativos:**
- Credenciales incorrectas → Mostrar error
- Email no existe → Mostrar error

**Postcondiciones:** Usuario autenticado y sesión activa

---

### UC3: Crear Nueva Landing Page

**Actor Principal:** Usuario autenticado  
**Precondiciones:** Usuario autenticado

**Flujo Principal:**
1. Usuario accede a "Crear Landing"
2. Completa: nombres de pareja, fecha de aniversario, bio
3. Selecciona tema base
4. Sistema genera slug automáticamente
5. Landing se crea en estado draft
6. Usuario se redirige al editor

**Validaciones:**
- Nombres: máximo 100 caracteres cada uno
- Fecha: válida y no futura
- Slug: único en el sistema

**Postcondiciones:** Landing creada en estado draft, accesible en dashboard

---

### UC4: Editar Landing Page

**Actor Principal:** Usuario propietario de la landing  
**Precondiciones:** Landing existe y usuario autenticado

**Flujo Principal:**
1. Usuario accede a editor de landing
2. Modifica: nombres, fecha, bio
3. Sistema muestra preview en tiempo real
4. Usuario guarda cambios
5. Landing se actualiza

**Validaciones:**
- Solo el propietario puede editar
- Campos respetan límites de caracteres

**Postcondiciones:** Cambios guardados en BD

---

### UC5: Personalizar Tema de Landing

**Actor Principal:** Usuario propietario  
**Precondiciones:** Landing existe

**Flujo Principal:**
1. Usuario accede a sección "Apariencia"
2. Selecciona tema base de catálogo
3. Personaliza colores:
   - Color primario
   - Color secundario
   - Color de fondo
4. Opcionalmente sube imagen de fondo
5. Sistema aplica cambios inmediatamente en preview
6. Usuario guarda cambios

**Postcondiciones:** Personalización guardada, landing renderiza con nuevos estilos

---

### UC6: Subir Imágenes a Galería

**Actor Principal:** Usuario propietario  
**Precondiciones:** Landing existe

**Flujo Principal:**
1. Usuario accede a galería
2. Selecciona una o más imágenes (JPG, PNG, WebP)
3. Sistema valida: tipo, tamaño (máx. 5 MB)
4. Imágenes se suben y se procesan
5. Se agregan a galería con sort_order incremental
6. Usuario puede reordenar con drag & drop

**Validaciones:**
- Máximo 50 imágenes por landing
- Tipos permitidos: JPG, PNG, WebP
- Tamaño máximo: 5 MB

**Postcondiciones:** Imágenes guardadas en galería, visibles en landing pública

---

### UC7: Eliminar Imagen de Galería

**Actor Principal:** Usuario propietario  
**Precondiciones:** Landing existe, imagen presente

**Flujo Principal:**
1. Usuario accede a galería
2. Selecciona imagen a eliminar
3. Sistema marca como inactiva (soft delete)
4. Imagen desaparece de galería

**Postcondiciones:** Imagen marcada como inactiva, no aparece en landing

---

### UC8: Publicar Landing Page

**Actor Principal:** Usuario propietario  
**Precondiciones:** Landing en estado draft, contenido completado

**Flujo Principal:**
1. Usuario accede a dashboard
2. Selecciona landing en draft
3. Clica en "Publicar"
4. Sistema establece is_published = TRUE
5. Landing ahora es accesible públicamente en `/p/{slug}`

**Postcondiciones:** Landing pública, visible para visitantes

---

### UC9: Despublicar Landing Page

**Actor Principal:** Usuario propietario  
**Precondiciones:** Landing publicada

**Flujo Principal:**
1. Usuario accede a landing publicada
2. Clica en "Despublicar"
3. Sistema establece is_published = FALSE
4. Landing no es accesible públicamente

**Postcondiciones:** Landing privada, solo visible para propietario

---

### UC10: Ver Landing Pública

**Actor Principal:** Visitante anónimo  
**Precondiciones:** Landing publicada

**Flujo Principal:**
1. Visitante accede a `/p/{slug}`
2. Sistema valida slug y is_published = TRUE
3. Landing se renderiza con:
   - Nombres de pareja
   - Fecha de aniversario
   - Bio/descripción
   - Galería de imágenes
   - Tema personalizado (colores, fondo)
4. Visitante puede ver landing completa

**Flujos Alternativos:**
- Slug no existe → 404
- Landing no publicada → 404
- Error de carga → Mensaje de error

**Postcondiciones:** Landing renderizada correctamente

---

### UC11: Cerrar Sesión

**Actor Principal:** Usuario autenticado  
**Precondiciones:** Usuario con sesión activa

**Flujo Principal:**
1. Usuario clica en "Cerrar Sesión"
2. Sistema invalida sesión
3. Se redirige a página de inicio

**Postcondiciones:** Sesión terminada, usuario no autenticado

---

## Diagramas UML

### Diagrama de Casos de Uso

```
@startuml

left to right direction

actor "Usuario Autenticado" as UA
actor "Visitante Anónimo" as VA
actor "Administrador" as Admin

rectangle "UsPage System" {
  usecase "UC1: Registrarse" as UC1
  usecase "UC2: Iniciar Sesión" as UC2
  usecase "UC3: Crear Landing" as UC3
  usecase "UC4: Editar Landing" as UC4
  usecase "UC5: Personalizar Tema" as UC5
  usecase "UC6: Subir Imágenes" as UC6
  usecase "UC7: Eliminar Imagen" as UC7
  usecase "UC8: Publicar Landing" as UC8
  usecase "UC9: Despublicar Landing" as UC9
  usecase "UC10: Ver Landing Pública" as UC10
  usecase "UC11: Cerrar Sesión" as UC11
  usecase "Validar Slug" as VAL_SLUG
  usecase "Autenticarse" as AUTH

  VA --> UC1 : registrarse
  VA --> UC2 : iniciar sesión
  VA --> UC10 : ver landing
  
  UA --> AUTH : autenticarse
  UA --> UC3 : crear landing
  UA --> UC4 : editar landing
  UA --> UC5 : personalizar
  UA --> UC6 : subir imágenes
  UA --> UC7 : eliminar imagen
  UA --> UC8 : publicar
  UA --> UC9 : despublicar
  UA --> UC11 : cerrar sesión
  
  UC1 .> AUTH : <<include>>
  UC2 .> AUTH : <<include>>
  UC3 .> VAL_SLUG : <<include>>
  UC8 .> VAL_SLUG : <<include>>
  UC10 .> VAL_SLUG : <<include>>
  
  Admin --> UC3 : gestiona temas (futuro)
}

@enduml
```

---

**Versión:** 1.0  
**Última actualización:** Enero 2026  
**Autor:** Kevin (Equipo de Desarrollo)

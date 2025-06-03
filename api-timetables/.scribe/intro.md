# API de Gestión de Horarios y Actividades

Esta API permite gestionar horarios y actividades de usuarios. Todos los endpoints requieren autenticación mediante token.

## Estructura General

La API está organizada en dos recursos principales:

### Horarios (Timetables)
- Cada usuario puede tener múltiples horarios
- Los horarios contienen información básica (nombre, descripción)
- Solo el propietario puede modificar sus horarios

### Actividades (Activities)
- Las actividades pertenecen a un horario específico
- Cada actividad tiene un día de la semana, hora de inicio y duración
- Las actividades pueden marcarse como disponibles o no disponibles

## Formato de Respuestas

Todas las respuestas siguen este formato:

```json
{
    "success": true|false,
    "message": "Mensaje descriptivo",
    "data": {
        // Datos de la respuesta
    }
}
```

## Códigos de Estado

- `200`: Operación exitosa
- `201`: Recurso creado
- `401`: No autenticado
- `403`: No autorizado
- `404`: Recurso no encontrado
- `422`: Error de validación
- `500`: Error del servidor

## Paginación

Los endpoints que devuelven listas están paginados por defecto (10 elementos por página).
Usar los parámetros `page` y `per_page` para controlar la paginación.

# Introduction



<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>

    This documentation aims to provide all the information you need to work with our API.

    <aside>As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
    You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).</aside>


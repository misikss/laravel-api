# Autenticación

Esta API utiliza [Laravel Sanctum](https://laravel.com/docs/sanctum) para la autenticación mediante tokens. Todos los endpoints protegidos requieren un token de acceso válido.

## Flujo de Autenticación

1. **Registro de Usuario** (`POST /api/auth/register`)
2. **Inicio de Sesión** (`POST /api/auth/login`)
3. **Uso del Token** en solicitudes posteriores
4. **Cierre de Sesión** (`POST /api/auth/logout`) cuando sea necesario

## Registro de Usuario

Para crear una nueva cuenta, envía una solicitud POST a `/api/auth/register`:

```bash
curl -X POST http://localhost/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Tu Nombre",
    "email": "tu@email.com",
    "password": "tu_contraseña"
  }'
```

### Parámetros de Registro
- `name` (requerido): Nombre completo del usuario
- `email` (requerido): Email único del usuario
- `password` (requerido): Contraseña (mínimo 8 caracteres)

## Inicio de Sesión

Para obtener un token, envía una solicitud POST a `/api/auth/login`:

```bash
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "tu@email.com",
    "password": "tu_contraseña"
  }'
```

### Parámetros de Login
- `email` (requerido): Email del usuario registrado
- `password` (requerido): Contraseña del usuario

## Uso del Token

Una vez que tengas el token, inclúyelo en todas las solicitudes protegidas usando el header `Authorization`:

```bash
curl -X GET http://localhost/api/timetables \
  -H "Authorization: Bearer tu-token-aquí" \
  -H "Accept: application/json"
```

### Formato del Header
```
Authorization: Bearer {tu-token}
```

## Cierre de Sesión

Para invalidar el token actual, envía una solicitud POST a `/api/auth/logout`:

```bash
curl -X POST http://localhost/api/auth/logout \
  -H "Authorization: Bearer tu-token-aquí" \
  -H "Accept: application/json"
```

## Códigos de Estado

- `200`: Operación exitosa
- `201`: Recurso creado exitosamente
- `401`: No autenticado o token inválido
- `403`: No autorizado para realizar la acción
- `422`: Error de validación
- `500`: Error interno del servidor

## Notas Importantes

- Los tokens no tienen fecha de expiración por defecto
- Cada usuario puede tener múltiples tokens activos
- Los tokens se invalidan automáticamente al cerrar sesión
- Todas las solicitudes deben incluir el header `Accept: application/json`

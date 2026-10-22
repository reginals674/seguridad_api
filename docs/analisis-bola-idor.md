# Análisis BOLA / IDOR

**BOLA** (*Broken Object Level Authorization*, OWASP API Security Top 10 – API1:2023) o **IDOR** (*Insecure Direct Object Reference*): la API recibe el ID de un objeto (`/api/tareas/7`) y lo entrega o modifica sin comprobar que pertenece a quien lo pide.

JWT (paso 2) resuelve **quién** hace la petición; BOLA trata de **qué objetos** puede tocar.

## Escenario de prueba

| Usuario | ID | Rol | Papel en la prueba |
|---|---|---|---|
| Ana | 1 | user | Víctima: tiene una tarea "Privada de Ana" con un dato sensible |
| Juan | 3 | user | Atacante: usuario legítimo con su propio JWT válido |
| Admin | 6 | admin | Comprueba que el rol administrador conserva el acceso total |

Todas las peticiones llevan una API key válida y el JWT del usuario correspondiente.

## Resultados

| # | Ataque (con el token de Juan) | Antes | Después |
|---|---|---|---|
| A1 | `GET /api/tareas` — listar tareas | **200**, incluye las de Ana | 200, solo las de Juan |
| A2 | `GET /api/tareas/{tarea de Ana}` | **200**, se lee el dato sensible | 404 |
| A3 | `PATCH /api/tareas/{tarea de Ana}` | **200**, título cambiado a "HACKEADO" | 404 |
| A4 | `DELETE /api/tareas/{tarea de Ana}` | **204**, tarea borrada | 404 |
| A5 | `POST /api/tareas` con `"user_id": 1` | **201**, tarea creada a nombre de Ana | 201, pero queda a nombre de Juan |
| A6 | `GET /api/users` — listar usuarios | **200**, todos los emails | 403 |
| A7 | `GET /api/users/1` — perfil de Ana con sus tareas | **200** | 404 |
| A8 | `PATCH /api/users/1` — modificar a Ana | **200**, nombre cambiado (con email/contraseña = robo de cuenta) | 404 |
| A9 | `PATCH /api/users/3` con `"role": "admin"` | n/a (no había roles) | 422, campo `role` prohibido |
| A10 | `POST /api/register` con `"role": "admin"` | n/a (no había roles) | 422, campo `role` prohibido |
| A11 | `GET /api/tareas/999999` (no existe) | 404 con mensaje distinto a A2 | 404 idéntico a A2 |

| # | Uso legítimo (no debe romperse) | Después |
|---|---|---|
| L1–L3 | Ana ve y edita su tarea, ve su perfil | 200 |
| L6 | Juan ve su propio perfil | 200 |
| L7 | Juan intenta borrar su cuenta | 403 (solo admin) |
| R1–R3 | Admin lista usuarios, ve y lista todas las tareas | 200 |
| R4 | Admin crea una tarea para Juan (`user_id: 3`) | 201, a nombre de Juan |

## Causas

1. **Los controladores no comprobaban la propiedad del objeto.** `show`, `update` y `destroy` recibían el modelo por ID y actuaban sobre él directamente.
2. **El cliente decidía el dueño.** `store` aceptaba `user_id` del body, y `user_id` estaba en `Fillable`.
3. **Listados sin filtrar.** `index` devolvía `Tarea::all()` y `User::all()` a cualquiera.
4. **IDs secuenciales.** Con IDs 1, 2, 3… basta con ir sumando 1 para recorrer los datos de todos.

## Corrección aplicada

| Medida | Archivo |
|---|---|
| Columna `role` (`user` / `admin`), por defecto `user` | `database/migrations/2026_10_22_100000_add_role_to_users_table.php` |
| `role` fuera de `Fillable`: no se puede asignar desde una petición | `app/Models/User.php` |
| Regla `'role' => 'prohibited'` en registro y edición: se rechaza con 422 en vez de ignorarse | `AuthController.php`, `UserController.php` |
| `user_id` fuera de `Fillable`: el dueño sale del token | `app/Models/Tarea.php` |
| Policy de tareas: solo el dueño o un admin | `app/Policies/TareaPolicy.php` |
| Policy de usuarios: cada uno su perfil; listar y borrar solo admin | `app/Policies/UserPolicy.php` |
| `Gate::authorize(...)` en cada acción sobre un objeto; `index` filtrado por usuario | `app/Http/Controllers/Api/*Controller.php` |
| "No existe" y "no es tuyo" responden el mismo 404 (anti-enumeración) | `bootstrap/app.php` |
| Asignar roles solo desde la consola del servidor: `php artisan usuario:rol {email} {user\|admin}` | `routes/console.php` |

## Riesgos pendientes

- **IDs secuenciales:** ya no dan acceso, pero revelan cuántos registros hay. Se podrían usar UUID o ULID.
- **`APP_DEBUG=true`:** las respuestas 403 incluyen la traza de la excepción. En producción debe estar en `false`.
- **Fuerza bruta y abuso:** sin límite de peticiones se puede probar contraseñas o IDs sin parar. Se cubre en el paso de Rate Limiter.

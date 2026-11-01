# Prueba 1 — PHP y Servicios Web

Universidad de Playa Ancha · Depto. Ciencia de Datos e Informática
Profesor: Felipe Cárcamo

**Integrante(s):** _Nestor_aliaga_vera_

---

## Requisitos

- PHP 8.0 o superior (XAMPP, Laragon o el servidor integrado de PHP).
- Postman para probar la API.

## Cómo ejecutar

**Opción A — XAMPP:** copiar la carpeta del proyecto dentro de `htdocs/` y abrir:

- Sistema web: `http://localhost/prueba1-php/sistema/`
- API: `http://localhost/prueba1-php/api/...`

**Opción B — servidor integrado de PHP:** desde la raíz del proyecto:

```bash
php -S localhost:8000
```

- Sistema web: `http://localhost:8000/sistema/`
- API: `http://localhost:8000/api/...`

> La carpeta `api/data/` y `sistema/comprobantes/` deben tener permisos de escritura.

## Estructura

```
prueba1-php/
├── api/                        Parte 1: API REST
│   ├── config.php              Funciones comunes (sesión, JSON, validaciones)
│   ├── login.php               POST  Inicio de sesión
│   ├── participantes.php       GET   Listar participantes  (protegido)
│   ├── premios.php             GET   Listar premios        (protegido)
│   ├── registrar.php           POST  Registrar participante (protegido)
│   ├── actualizar.php          POST  Actualizar participante (protegido)
│   ├── eliminar.php            GET   Eliminar participante (protegido)
│   ├── logout.php              GET   Cerrar sesión
│   └── data/
│       ├── participantes.json
│       └── premios.json
├── sistema/                    Parte 2: Sistema web
│   ├── includes.php            Funciones comunes (sesión, protección, plantilla)
│   ├── index.php               Login con CAPTCHA
│   ├── panel.php               Área protegida
│   ├── rifa.php                Reserva de números
│   ├── buscar.php              Búsqueda de participantes (GET)
│   ├── subir_comprobante.php   Carga de archivos
│   ├── archivos.php            Listado de comprobantes
│   ├── salir.php               Cierre de sesión
│   ├── comprobantes/           Archivos subidos
│   └── css/estilos.css
└── postman_coleccion.json      Colección para importar en Postman
```

## Credenciales

| Usuario    | Contraseña |
|------------|------------|
| `userupla` | `upla123`  |

---

## Parte 1 — API REST

Todas las respuestas son JSON con el campo `ok` (true/false) y un `mensaje`.
Los servicios protegidos responden **401** si no hay sesión iniciada.
Postman guarda la cookie de sesión (`PHPSESSID`) automáticamente después del login.

Los POST aceptan `form-data`, `x-www-form-urlencoded` o `raw` JSON.

| # | Servicio | Método | URL | Parámetros |
|---|----------|--------|-----|------------|
| 1 | Iniciar sesión | POST | `/api/login.php` | `usuario`, `clave` |
| 2 | Listar participantes | GET | `/api/participantes.php` | — |
| 3 | Listar premios | GET | `/api/premios.php` | — |
| 4 | Registrar participante | POST | `/api/registrar.php` | `nombre`, `apellido`, `numero_rifa` |
| 5 | Actualizar participante | POST | `/api/actualizar.php` | `id` + campos a modificar |
| 6 | Eliminar participante | GET | `/api/eliminar.php?id=3` | `id` |
| 7 | Cerrar sesión | GET | `/api/logout.php` | — |

Validaciones incluidas: método HTTP correcto (405), campos obligatorios (400),
número de rifa entero positivo y no repetido (409), ID inexistente (404).

### Ejemplo

```http
POST /api/registrar.php
Content-Type: application/json

{ "nombre": "Ana", "apellido": "Pérez", "numero_rifa": 30 }
```

```json
{
    "ok": true,
    "mensaje": "Participante registrado.",
    "participante": { "id": 4, "nombre": "Ana", "apellido": "Pérez", "numero_rifa": 30 }
}
```

---

## Parte 2 — Sistema web

- **index.php:** formulario POST con usuario, contraseña y CAPTCHA (suma de dos
  números aleatorios). El resultado se guarda en `$_SESSION`. Solo se accede si
  las tres condiciones se cumplen; después de cada intento se genera un CAPTCHA nuevo.
- **panel.php:** página principal; sin sesión redirige a `index.php`.
- **rifa.php:** agregar y quitar números (1–100) por POST. Los números se guardan
  en `$_SESSION['numeros']`. Se usa el patrón *Post/Redirect/Get*, por lo que al
  presionar F5 no se reenvía el formulario ni se pierde la información. Muestra
  cantidad, precio ($2.000) y total. La cantidad nunca puede ser negativa.
- **buscar.php:** formulario GET con nombre, apellido, ciudad y categoría.
  4 participantes predefinidos (incluido *Universidad / Playa Ancha / Valparaiso /
  Grupo A*). Deben coincidir las 4 condiciones (sin distinguir mayúsculas).
- **subir_comprobante.php:** sube PDF/JPG/PNG (máx. 5 MB) a `comprobantes/` e informa
  éxito o error.
- **archivos.php:** lista nombre, tamaño y fecha de los comprobantes.
- **salir.php:** vacía `$_SESSION`, borra la cookie de sesión y llama a
  `session_destroy()`; luego redirige a `index.php`.

Todas las páginas excepto `index.php` están protegidas con `protegerPagina()`.

## Video

Enlace al video de demostración: _completar_

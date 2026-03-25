# Generador de Números Aleatorios

Aplicación PHP orientada a objetos que solicita al usuario N elementos y muestra N números aleatorios en una tabla.

## Requisitos

- 
- 

## Ejecución

```bash
php -S localhost:8000 -t public/
```

Luego abre `http://localhost:8000` en tu navegador.

## Uso

1. Ingresa la cantidad de números a generar (n) - requerido (1-1000)
2. Opcionalmente ingresa el rango mínimo y máximo
3. Haz clic en "Generar"

## Comandos disponibles

| Comando | Descripción |
|---------|-------------|
| `composer install` | Instalar dependencias |
| `composer lint` | Análisis de código (CodeSniffer) |
| `composer lint:fix` | Auto-corregir estilo de código |
| `composer stan` | Análisis estático avanzado |
| `composer test` | Ejecutar tests unitarios |
| `composer check` | Ejecutar lint, stan y tests |

## Estructura

- `src/` - Clases de la aplicación
  - `App.php` - Punto de entrada y flujo PRG
  - `Request.php` - Manejo y validación de entrada
  - `RandomGenerator.php` - Generación de números aleatorios
  - `Renderer.php` - Renderizado de vistas
- `public/` - Entry point
  - `index.php` - Front controller
- `tests/` - Tests unitarios

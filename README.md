# Generador de Números Aleatorios

Aplicación PHP orientada a objetos que solicita al usuario N elementos y muestra N números aleatorios en una tabla.

## Requisitos

- Docker
- docker-compose
- Puerto 8082 disponible

## Ejecución

primero levanta en la terminal la pagina 

```bash
live@minios:~/podman/apachePhp$ podman-compose up
```

Luego abre `(http://172.25.0.216:8082/noo/index.php)` en tu navegador.

## Uso

1. Ingresa la cantidad de números a generar (n) - requerido (1-1000)
2. Opcionalmente ingresa el rango mínimo y máximo
3. Haz clic en "Generar"

## Estructura

- `src/` - Clases de la aplicación
  - `App.php` - Punto de entrada y flujo PRG
  - `Request.php` - Manejo y validación de entrada
  - `RandomGenerator.php` - Generación de números aleatorios
  - `Renderer.php` - Renderizado de vistas
- `index.php` - Front controller
- `tests/` - Tests unitarios

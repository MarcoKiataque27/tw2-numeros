## agents.md

### Resumen
Aplicación PHP orientada a objetos que solicita al usuario N elementos y muestra N números aleatorios en una tabla. Implementación separada en archivos (clases y controlador) y sin bucles infinitos, switch o break.

### Estructura de archivos (entregables)
- src/
  - App.php                — Punto de entrada de la aplicación (encapsula flujo PRG).
  - Request.php            — Clase para manejar y validar entrada HTTP.
  - RandomGenerator.php    — Clase para generar la lista de números aleatorios y estadísticas.
  - Renderer.php           — Clase para renderizar vistas (escapado HTML).
- public/
  - index.php              — Front controller que instancia App y lanza la ejecución.
- views/
  - form.php               — HTML del formulario.
  - results.php            — HTML de la tabla de resultados.
- README.md                — Instrucciones breves de requisitos y ejecución.

### Reglas de diseño (obligatorias)
- Programación orientada a objetos: cada responsabilidad en su clase.
- Archivos separados por clase como se listan arriba.
- No usar bucles infinitos, switch ni break.
- Validación servidor: n entero positivo entre 1 y 1000 por defecto.
- Opcional: campos para rango mínimo y máximo (min < max).
- Prevención de reenvío accidental: seguir patrón PRG (Post/Redirect/Get).
- Escapar toda salida HTML para evitar XSS.
- Comentarios breves en secciones clave del código.

### Contratos de las clases

1) src/Request.php
- Propósito: Leer POST/GET, normalizar y validar parámetros.
- Métodos públicos:
  - __construct(array $get, array $post)
  - getInt(string $key, int $default = null) : ?int
  - validateN(int $minAllowed, int $maxAllowed) : array
  - all() : array
- Validaciones:
  - 'n' debe ser entero entre 1 y 1000 (por defecto).
  - 'min' y 'max' (si enviados) deben ser enteros y min < max; si no enviados, usar 1 y 10000.

2) src/RandomGenerator.php
- Propósito: Generar N enteros aleatorios dentro de un rango y calcular estadísticas.
- Constructor: __construct(int $n, int $min = 1, int $max = 10000)
- Métodos públicos:
  - generate() : array   — devuelve el array de números (longitud n).
  - getSum() : int
  - getAverage() : float  — con precisión suficiente (formatear en view).
  - getMin() : int
  - getMax() : int
- Reglas: No usar bucles infinitos. Para iteración normal emplear for/foreach.

3) src/Renderer.php
- Propósito: Renderizar vistas y escapar salidas.
- Métodos públicos:
  - renderForm(array $data = []) : string
  - renderResults(array $numbers, array $stats, array $previousInput = []) : string
- Escapado: usar htmlspecialchars con ENT_QUOTES y UTF-8.

4) src/App.php
- Propósito: Coordinar Request, RandomGenerator y Renderer; implementar PRG.
- Métodos públicos:
  - __construct(Request $req, Renderer $renderer)
  - run() : void
- Flujo (sugerido):
  - Si método POST:
    - Validar entrada con Request::validateN(...)
    - Si errores: almacenar errores en sesión y redirigir (PRG) a GET
    - Si ok: crear RandomGenerator, generar números, almacenar resultados en sesión y redirigir (PRG) a GET
  - Si método GET:
    - Mostrar form.php con datos previos y, si hay resultados en sesión, mostrar results.php
  - Limpiar resultados/errores de sesión tras mostrarlos.

### Recomendaciones de implementación
- Usar session_start() en public/index.php antes de instanciar App para soportar PRG.
- Para generación aleatoria usar random_int($min, $max) en bucles seguros.
- Validaciones con filter_var(..., FILTER_VALIDATE_INT).
- Limitar n para evitar uso excesivo de memoria (máximo 1000).
- Documentar con comentarios breves en cada método y archivo.

### Ejemplo de comportamiento esperado
- Usuario abre /public/index.php → ve formulario con campos: n (requerido), min (opcional), max (opcional) y botón Generar.
- Envía POST con n=10, min=1, max=100 → App valida y redirige (PRG) a GET.
- GET muestra formulario con valores previos y una tabla 10 filas con columnas: Índice, Número aleatorio; fila final con Suma, Promedio (2 decimales), Mínimo y Máximo.

### Notas de seguridad y rendimiento
- Escapar siempre la salida.
- Validar y sanear toda entrada.
- No permitir n > 1000 para evitar consumo excesivo de memoria/CPU.
- No usar switch ni break en ninguna clase ni script.

---

## Comandos de Build, Lint y Test

### Requisitos previos
```bash
# Instalar dependencias PHP
composer install
```

### Comandos disponibles
```bash
# Instalación de dependencias
composer install          # Instalar todas las dependencias
composer update          # Actualizar dependencias

# Ejecución de la aplicación (desarrollo)
php -S localhost:8000 -t public/   # Servidor PHP built-in

# Análisis estático (lint)
composer lint            # Ejecutar PHP_CodeSniffer
composer lint:fix        # Ejecutar PHP_CodeSniffer con auto-fix

# Análisis estático avanzado
composer stan            # Ejecutar PHPStan (análisis profundo)
composer stan:baseline    # Generar baseline de PHPStan

# Tests unitarios
composer test            # Ejecutar PHPUnit (todos los tests)
composer test:unit       # Ejecutar solo tests unitarios
composer test:coverage   # Ejecutar tests con cobertura

# Ejecutar un solo test
./vendor/bin/phpunit --filter=TestMethodName
./vendor/bin/phpunit --filter=RandomGeneratorTest::testGenerate

# Formateo automático de código
composer format          # Formatear con PHP-CS-Fixer

# Todos los checks (lint + stan + test)
composer check           # Ejecutar lint, stan y tests
```

### Estructura de tests
- Tests en directorio `tests/` siguiendo PSR-4
-命名: `Tests\Unit\ClassNameTest.php`
- Usar PHPUnit con annotations o atributos PHP 8+
- Datos de prueba en `tests/fixtures/`

---

## Guía de Estilo de Código

### Convenciones generales
- Estándar: PSR-12 (PHP Standard Recommendations)
- Nivel strict: usar strict_types en todos los archivos PHP
- encoding: UTF-8

### Imports y namespaces
```php
<?php
declare(strict_types=1);

namespace App\Src;

use InvalidArgumentException;
use function array_map;
use function count;
```
- Un use por línea, ordenados alfabéticamente
- Funciones importadas con `use function`
- Constantes con `use const`
- No usar importar con alias a menos que sea necesario

### Formateo de código
- 4 espacios para indentación (no tabs)
- Líneas máximo 120 caracteres
- Una línea en blanco entre secciones (use, constants, properties, methods)
- Llaves de apertura en misma línea (K&R style)
- Operadores con espacios: `$a + $b`, no `$a+$b`
- Arrays cortos en una línea cuando sea posible

### Tipos (Type Hints)
```php
public function __construct(
    private Request $request,
    private Renderer $renderer
) {}

public function run(): void
{
    $numbers = $this->generator->generate();
}

public function getInt(string $key, int $default = null): ?int
```
- Siempre usar type hints en métodos
- Return types obligatorios para métodos públicos
- Propiedades tipadas cuando sea posible
- Usar `?Type` para nullable

### Convenciones de nombres
- Clases: PascalCase (e.g., `RandomGenerator`, `RequestHandler`)
- Métodos: camelCase (e.g., `getInt()`, `generateNumbers()`)
- Propiedades: camelCase (e.g., `$numbers`, `$minValue`)
- Constantes: UPPER_SNAKE_CASE (e.g., `MAX_ITEMS`)
- Archivos: Match class name (e.g., `RandomGenerator.php`)
- Directorios: Plural para namespaces de colección (e.g., `src/`, `tests/`)

### Control de flujo
- NO usar `switch` bajo ninguna circunstancia
- NO usar `break` fuera de loops (solo en casos necesarios)
- NO usar bucles infinitos (`while(true)` sin salida)
- Preferir `foreach` sobre `for` cuando sea posible
- Usar `match` (PHP 8+) en lugar de switch si es necesario

### Manejo de errores
```php
// Validación con excepciones semánticas
public function validateN(int $min, int $max): array
{
    if ($this->n < $min || $this->n > $max) {
        throw new InvalidArgumentException(
            "El valor de n debe estar entre {$min} y {$max}"
        );
    }
    return ['valid' => true];
}

// Captura específica, nunca catch genérico
try {
    $result = $this->generator->generate();
} catch (InvalidArgumentException $e) {
    $errors[] = $e->getMessage();
}
```
- Usar excepciones personalizadas para errores de dominio
- Validar inputs al inicio de métodos públicos
- No suprimir errores con `@`
- Registrar errores significativos

### Seguridad
```php
// Siempre escapar salida
echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

// Validar inputs
$value = filter_var($input, FILTER_VALIDATE_INT);
if ($value === false) {
    throw new InvalidArgumentException('Invalid integer');
}
```
- Escapar toda salida HTML con `htmlspecialchars()`
- Validar y sanear toda entrada del usuario
- Usar `random_int()` para generación de números aleatorios
- No usar `eval()`, `exec()`, o funciones peligrosas
- CSRF: implementar tokens para formularios

### PHPDoc y documentación
```php
/**
 * Generates N random integers within a specified range.
 *
 * @param int $n Number of elements to generate
 * @param int $min Minimum value (inclusive)
 * @param int $max Maximum value (inclusive)
 * @return array<int> Array of random integers
 * @throws InvalidArgumentException If n is out of bounds
 */
public function generate(): array
```
- Documentar métodos públicos con @param y @return
- Usar tipos en PHPDoc para compatibilidad con IDE
- Descripción breve en una línea, detallada si es necesario

### Properties y estado
```php
class RandomGenerator
{
    private int $n;
    private int $min;
    private int $max;
    private ?array $generated = null;

    public function __construct(
        int $n,
        int $min = 1,
        int $max = 10000
    ) {
        $this->n = $n;
        $this->min = $min;
        $this->max = $max;
    }
}
```
- Visibilidad explícita: private/protected/public
- Propiedades inicializadas en constructor
- Usar constructor property promotion (PHP 8+)
- Evitar estado mutable cuando sea posible

### Testing
```php
use PHPUnit\Framework\TestCase;

class RandomGeneratorTest extends TestCase
{
    public function testGenerateReturnsCorrectCount(): void
    {
        $generator = new RandomGenerator(10, 1, 100);
        $result = $generator->generate();

        $this->assertCount(10, $result);
    }

    public function testGenerateRespectsMinMax(): void
    {
        $generator = new RandomGenerator(100, 1, 10);
        $result = $generator->generate();

        foreach ($result as $number) {
            $this->assertGreaterThanOrEqual(1, $number);
            $this->assertLessThanOrEqual(10, $number);
        }
    }
}
```
- Un archivo de test por clase
- Métodos de test con nombre descriptivo: `testMethodDoesExpectedBehavior()`
- Usar assertions específicos ($this->assertSame vs assertEquals)
- Testear casos happy path y edge cases
- Mockear dependencias externas

---

## Configuración de herramientas

### composer.json (configuración sugerida)
```json
{
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "phpstan/phpstan": "^1.10",
        "squizlabs/php_codesniffer": "^3.7",
        "friendsofphp/php-cs-fixer": "^3.16"
    },
    "scripts": {
        "lint": "phpcs --standard=PSR12 src/",
        "lint:fix": "phpcbf --standard=PSR12 src/",
        "stan": "phpstan analyse src/ --level=max",
        "test": "phpunit",
        "format": "php-cs-fixer fix src/"
    }
}
```

### phpunit.xml (configuración sugerida)
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         cacheDirectory=".phpunit.cache">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>src</directory>
        </include>
    </source>
</phpunit>
```

---

## Notas adicionales para agentes

1. Antes de modificar código existente, leer y entender la estructura
2. Mantener separación de responsabilidades (SRP)
3. Ejecutar `composer check` antes de hacer commit
4. No introducir breaking changes sin consenso
5. Actualizar esta guía si se adoptan nuevas convenciones

--- End of agents.md
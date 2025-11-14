# Color Tooltip - Módulo Simple para PrestaShop

## ¿Qué hace este módulo?

Muestra **un tooltip simple con el NOMBRE del atributo de color** cuando pasas el mouse sobre los selectores de color en PrestaShop.

**Ejemplo:** Si tienes un atributo de color llamado "Rojo pálido", cuando pases el mouse sobre ese selector, verás un tooltip que dice "Rojo pálido".

## Lo que NO hace (y está bien así)

- ❌ NO muestra formatos HEX, RGB, HSL (eso ya lo ves en el selector)
- ❌ NO tiene historial de colores (innecesario)
- ❌ NO tiene preview del color (ya lo estás viendo)
- ❌ NO tiene configuración compleja (instalar y listo)

## Instalación

1. Sube la carpeta `colortooltip` a `/modules/`
2. Ve al back office → Módulos
3. Busca "Color Tooltip"
4. Haz clic en **Instalar**
5. ¡Listo! Ya funciona automáticamente

## Detección automática

El módulo detecta automáticamente:
- `input[type="color"]` - Inputs estándar HTML5
- `.input-color` - Clase personalizada
- `.color-picker` - Selectores de PrestaShop
- `input.color` - Variante común
- Cualquier input con clases relacionadas a "color"

## Prioridad del texto mostrado

El tooltip muestra el nombre en este orden de prioridad:

1. `data-color-tooltip` - Atributo específico
2. `data-tooltip` - Atributo genérico
3. `title` - Atributo HTML estándar
4. Texto del `<label>` asociado (por ID o padre)
5. `placeholder` - Texto del placeholder
6. Fallback: "Color"

## Ejemplos de uso

```html
<!-- Opción 1: Usando data-color-tooltip (recomendado) -->
<input type="color" data-color-tooltip="Rojo pálido" value="#ff6b6b">

<!-- Opción 2: Usando title -->
<input type="color" title="Azul marino" value="#001f3f">

<!-- Opción 3: Con label asociado -->
<label for="mi-color">Verde menta</label>
<input type="color" id="mi-color" value="#a8e6cf">
```

## Prueba local (sin PrestaShop)

Abre `test.html` en tu navegador para ver el módulo en acción sin necesidad de instalarlo en PrestaShop.

## Versión

**v2.1.0** - Versión simplificada y limpia

## Changelog

### v2.1.0 (2025-01-14)
- **SIMPLIFICADO DRÁSTICAMENTE** eliminando funcionalidades innecesarias
- Eliminado: formatos HEX/RGB/HSL, historial, preview, tema automático
- Ahora solo muestra el nombre del atributo en un tooltip simple
- Arreglados problemas de encoding (tildes, ñ, etc.)
- CSS reducido de 100+ líneas a solo 20 líneas
- JS reducido de 200+ líneas a ~100 líneas
- PHP simplificado, sin configuración compleja

### v2.0.1 (2025-01-14)
- Corregida detección automática de inputs de color
- Event listeners mejorados

### v2.0.0
- Versión inicial con funcionalidades avanzadas (demasiado compleja)

## Soporte

Si el módulo no funciona:

1. Abre la consola del navegador (F12)
2. Busca: `[ColorTooltip] Se encontraron X inputs de color`
3. Si X = 0, no hay inputs de color en esa página
4. Si X > 0 pero no ves tooltips, revisa que los archivos CSS y JS se estén cargando

## Licencia

Libre para usar y modificar.

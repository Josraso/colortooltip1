document.addEventListener('DOMContentLoaded', () => {

    // Crear UN SOLO tooltip simple
    let tooltip = document.createElement('div');
    tooltip.className = 'color-tooltip-simple';
    document.body.appendChild(tooltip);

    // Obtener el nombre del atributo de color
    const getColorName = (input) => {
        // Prioridad: data-color-tooltip > data-tooltip > title > label asociado > placeholder
        if (input.dataset.colorTooltip) return input.dataset.colorTooltip;
        if (input.dataset.tooltip) return input.dataset.tooltip;
        if (input.title) return input.title;

        // Buscar label asociado por 'for'
        if (input.id) {
            const label = document.querySelector(`label[for="${input.id}"]`);
            if (label && label.textContent.trim()) return label.textContent.trim();
        }

        // Buscar label padre
        const parentLabel = input.closest('label');
        if (parentLabel) {
            const text = parentLabel.textContent.trim();
            if (text) return text;
        }

        // Buscar .sr-only para accesibilidad
        const parent = input.parentNode;
        if (parent) {
            const srOnly = parent.querySelector('.sr-only');
            if (srOnly && srOnly.textContent.trim()) return srOnly.textContent.trim();
        }

        // Buscar texto en contenedor padre
        if (parent && parent.textContent) {
            const text = parent.textContent.trim();
            if (text && text.length < 50) return text;
        }

        return input.placeholder || 'Color';
    };

    const updateTooltipContent = (input) => {
        const colorName = getColorName(input);
        tooltip.textContent = colorName;
    };

    const moveTooltip = (e) => {
        let left = e.pageX + 15;
        let top = e.pageY + 15;

        // Ajustar si se sale de la pantalla
        if (left + tooltip.offsetWidth > window.innerWidth) {
            left = e.pageX - tooltip.offsetWidth - 15;
        }
        if (top + tooltip.offsetHeight > window.innerHeight) {
            top = e.pageY - tooltip.offsetHeight - 15;
        }

        tooltip.style.left = left + 'px';
        tooltip.style.top = top + 'px';
    };

    // Detectar inputs de color con múltiples selectores
    const colorInputSelectors = [
        'input[type="color"]',          // Selector estándar
        '.input-color',                 // Clase personalizada
        '.color-picker',                // Clase común en PrestaShop
        'input.color',                  // Variante común
        '[class*="color-input"]',       // Cualquier clase que contenga "color-input"
    ];

    const colorInputs = document.querySelectorAll(colorInputSelectors.join(', '));

    console.log(`[ColorTooltip] Se encontraron ${colorInputs.length} inputs de color`);

    // Event listeners mejorados - funcionan directamente en el input
    colorInputs.forEach((input) => {

        input.addEventListener('mouseenter', (e) => {
            updateTooltipContent(input);
            tooltip.classList.add('active');
            moveTooltip(e);
        });

        input.addEventListener('mousemove', (e) => {
            if (tooltip.classList.contains('active')) {
                moveTooltip(e);
            }
        });

        input.addEventListener('mouseleave', () => {
            tooltip.classList.remove('active');
        });

        input.addEventListener('focus', (e) => {
            updateTooltipContent(input);
            tooltip.classList.add('active');
            moveTooltip(e);
        });

        input.addEventListener('blur', () => {
            tooltip.classList.remove('active');
        });

    });

});
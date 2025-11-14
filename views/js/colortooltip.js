document.addEventListener('DOMContentLoaded', () => {

    // Crear UN SOLO tooltip para todos
    let tooltip = document.createElement('div');
    tooltip.className = 'color-tooltip-custom';
    tooltip.innerHTML = `
        <div class="color-tooltip-header">
            <div class="color-tooltip-preview"></div>
            <div class="color-tooltip-title"></div>
        </div>
        <div class="color-tooltip-formats"></div>
        <div class="color-tooltip-history">
            <div class="color-tooltip-history-title">Recientes</div>
            <div class="color-tooltip-history-colors"></div>
        </div>
    `;
    document.body.appendChild(tooltip);

    // Cargar historial
    let colorHistory = [];
    try {
        const saved = localStorage.getItem('colorTooltipHistory');
        if (saved) colorHistory = JSON.parse(saved);
    } catch (e) {}

    // Funciones auxiliares
    const hexToRgb = (hex) => {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : { r: 0, g: 0, b: 0 };
    };

    const rgbToHsl = (r, g, b) => {
        r /= 255; g /= 255; b /= 255;
        const max = Math.max(r, g, b), min = Math.min(r, g, b);
        let h, s, l = (max + min) / 2;
        if (max === min) {
            h = s = 0;
        } else {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = ((g - b) / d + (g < b ? 6 : 0)) / 6; break;
                case g: h = ((b - r) / d + 2) / 6; break;
                case b: h = ((r - g) / d + 4) / 6; break;
            }
        }
        return { h: Math.round(h * 360), s: Math.round(s * 100), l: Math.round(l * 100) };
    };

    const getLuminance = (r, g, b) => {
        const a = [r, g, b].map(v => {
            v /= 255;
            return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
        });
        return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
    };

    const getTooltipText = (input) => {
        // Prioridad: data-color-tooltip > data-tooltip > title > .sr-only > valor
        if (input.dataset.colorTooltip) return input.dataset.colorTooltip;
        if (input.dataset.tooltip) return input.dataset.tooltip;
        if (input.title) return input.title;
        
        const parent = input.parentNode;
        if (parent) {
            const srOnly = parent.querySelector('.sr-only');
            if (srOnly && srOnly.innerText.trim()) return srOnly.innerText.trim();
        }
        
        return input.value.toUpperCase();
    };

    const updateTooltipContent = (input) => {
        const color = input.value;
        const text = getTooltipText(input);
        const rgb = hexToRgb(color);
        const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);

        // Actualizar título
        tooltip.querySelector('.color-tooltip-title').textContent = text;

        // Actualizar preview
        tooltip.querySelector('.color-tooltip-preview').style.backgroundColor = color;

        // Actualizar formatos
        tooltip.querySelector('.color-tooltip-formats').innerHTML = `
            <div class="color-tooltip-format-item">
                <span class="color-tooltip-format-label">HEX:</span>
                <span>${color.toUpperCase()}</span>
            </div>
            <div class="color-tooltip-format-item">
                <span class="color-tooltip-format-label">RGB:</span>
                <span>rgb(${rgb.r}, ${rgb.g}, ${rgb.b})</span>
            </div>
            <div class="color-tooltip-format-item">
                <span class="color-tooltip-format-label">HSL:</span>
                <span>hsl(${hsl.h}°, ${hsl.s}%, ${hsl.l}%)</span>
            </div>
        `;

        // Actualizar historial
        if (colorHistory.length > 0) {
            tooltip.querySelector('.color-tooltip-history-colors').innerHTML = colorHistory
                .map(c => `<div class="color-tooltip-history-item" style="background-color: ${c}" data-color="${c}" title="${c.toUpperCase()}"></div>`)
                .join('');
            tooltip.querySelector('.color-tooltip-history').style.display = 'block';
        } else {
            tooltip.querySelector('.color-tooltip-history').style.display = 'none';
        }

        // Tema automático
        const luminance = getLuminance(rgb.r, rgb.g, rgb.b);
        tooltip.classList.remove('theme-light', 'theme-dark');
        tooltip.classList.add(luminance > 0.5 ? 'theme-light' : 'theme-dark');
    };

    const moveTooltip = (e) => {
        let left = e.pageX + 15;
        let top = e.pageY - tooltip.offsetHeight - 15;

        // Ajustar si se sale de la pantalla
        if (left + tooltip.offsetWidth > window.innerWidth) {
            left = e.pageX - tooltip.offsetWidth - 15;
        }
        if (top < 0) {
            top = e.pageY + 15;
        }

        tooltip.style.left = left + 'px';
        tooltip.style.top = top + 'px';
    };

    const addToHistory = (color) => {
        color = color.toLowerCase();
        colorHistory = colorHistory.filter(c => c !== color);
        colorHistory.unshift(color);
        if (colorHistory.length > 5) colorHistory = colorHistory.slice(0, 5);
        try {
            localStorage.setItem('colorTooltipHistory', JSON.stringify(colorHistory));
        } catch (e) {}
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

        input.addEventListener('change', (e) => {
            addToHistory(input.value);
            updateTooltipContent(input);
        });

        input.addEventListener('input', (e) => {
            updateTooltipContent(input);
        });

    });

    // Click en historial para aplicar color
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('color-tooltip-history-item')) {
            const color = e.target.dataset.color;
            const activeInput = document.querySelector('.input-color:hover');
            if (activeInput && color) {
                activeInput.value = color;
                activeInput.dispatchEvent(new Event('change', { bubbles: true }));
                updateTooltipContent(activeInput);
            }
        }
    });

});
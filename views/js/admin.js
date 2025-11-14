/**
 * Admin JS para Color Tooltip
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // Mostrar/ocultar campos según tema automático
    const autoThemeSwitch = document.querySelector('input[name="COLORTOOLTIP_AUTO_THEME"]');
    
    if (autoThemeSwitch) {
        const bgColorField = document.querySelector('input[name="COLORTOOLTIP_BG"]').closest('.form-group');
        const textColorField = document.querySelector('input[name="COLORTOOLTIP_TEXT"]').closest('.form-group');
        
        function toggleColorFields() {
            const isAutoTheme = autoThemeSwitch.checked;
            if (bgColorField && textColorField) {
                bgColorField.style.opacity = isAutoTheme ? '0.5' : '1';
                textColorField.style.opacity = isAutoTheme ? '0.5' : '1';
                
                if (isAutoTheme) {
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-info';
                    alert.innerHTML = '<i class="icon-info-circle"></i> Los colores personalizados se desactivan cuando el tema automático está activado.';
                    
                    if (!bgColorField.querySelector('.alert')) {
                        bgColorField.appendChild(alert);
                    }
                } else {
                    const existingAlert = bgColorField.querySelector('.alert');
                    if (existingAlert) {
                        existingAlert.remove();
                    }
                }
            }
        }
        
        autoThemeSwitch.addEventListener('change', toggleColorFields);
        toggleColorFields();
    }
    
    // Validación de rangos
    const rangeInputs = {
        'COLORTOOLTIP_BORDER_RADIUS': { min: 0, max: 50 },
        'COLORTOOLTIP_OPACITY': { min: 0, max: 100 },
        'COLORTOOLTIP_FONT_SIZE': { min: 10, max: 20 },
        'COLORTOOLTIP_PADDING': { min: 8, max: 20 },
        'COLORTOOLTIP_HISTORY_SIZE': { min: 1, max: 10 },
    };
    
    Object.keys(rangeInputs).forEach(function(fieldName) {
        const input = document.querySelector('input[name="' + fieldName + '"]');
        if (input) {
            input.addEventListener('blur', function() {
                const value = parseInt(this.value);
                const range = rangeInputs[fieldName];
                
                if (value < range.min) {
                    this.value = range.min;
                } else if (value > range.max) {
                    this.value = range.max;
                }
            });
        }
    });
});
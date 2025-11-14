<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Colortooltip extends Module
{
    public function __construct()
    {
        $this->name = 'colortooltip';
        $this->tab = 'front_office_features';
        $this->version = '2.0.0';
        $this->author = 'Tu Nombre';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Color Tooltip Advanced');
        $this->description = $this->l('Sistema avanzado de tooltips para selectores de color con múltiples formatos, historial y tema adaptativo.');
        $this->confirmUninstall = $this->l('¿Estás seguro de que quieres desinstalar este módulo?');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('header')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->installConfiguration();
    }

    public function uninstall()
    {
        return parent::uninstall()
            && $this->uninstallConfiguration();
    }

    /**
     * Instala la configuración por defecto
     */
    private function installConfiguration()
    {
        $config = array(
            // Apariencia
            'COLORTOOLTIP_BG' => '#ffffff',
            'COLORTOOLTIP_TEXT' => '#333333',
            'COLORTOOLTIP_BORDER_RADIUS' => '8',
            'COLORTOOLTIP_OPACITY' => '100',
            'COLORTOOLTIP_FONT_SIZE' => '13',
            'COLORTOOLTIP_PADDING' => '12',
            
            // Funcionalidad
            'COLORTOOLTIP_SHOW_PREVIEW' => '1',
            'COLORTOOLTIP_SHOW_FORMATS' => '1',
            'COLORTOOLTIP_SHOW_HISTORY' => '1',
            'COLORTOOLTIP_HISTORY_SIZE' => '5',
            'COLORTOOLTIP_OFFSET_X' => '15',
            'COLORTOOLTIP_OFFSET_Y' => '15',
            
            // Avanzado
            'COLORTOOLTIP_AUTO_THEME' => '1',
            'COLORTOOLTIP_ZINDEX' => '99999',
            'COLORTOOLTIP_ANIMATION_SPEED' => '200',
            'COLORTOOLTIP_THROTTLE' => '1',
        );

        foreach ($config as $key => $value) {
            if (!Configuration::updateValue($key, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Desinstala toda la configuración
     */
    private function uninstallConfiguration()
    {
        $keys = array(
            'COLORTOOLTIP_BG',
            'COLORTOOLTIP_TEXT',
            'COLORTOOLTIP_BORDER_RADIUS',
            'COLORTOOLTIP_OPACITY',
            'COLORTOOLTIP_FONT_SIZE',
            'COLORTOOLTIP_PADDING',
            'COLORTOOLTIP_SHOW_PREVIEW',
            'COLORTOOLTIP_SHOW_FORMATS',
            'COLORTOOLTIP_SHOW_HISTORY',
            'COLORTOOLTIP_HISTORY_SIZE',
            'COLORTOOLTIP_OFFSET_X',
            'COLORTOOLTIP_OFFSET_Y',
            'COLORTOOLTIP_AUTO_THEME',
            'COLORTOOLTIP_ZINDEX',
            'COLORTOOLTIP_ANIMATION_SPEED',
            'COLORTOOLTIP_THROTTLE',
        );

        foreach ($keys as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }

    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/colortooltip.css');
        $this->context->controller->addJS($this->_path . 'views/js/colortooltip.js');

        // Inyectar configuración dinámica
        return $this->generateDynamicCSS() . $this->generateDynamicJS();
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
            $this->context->controller->addJS($this->_path . 'views/js/admin.js');
        }
    }

    /**
     * Genera CSS dinámico basado en la configuración
     */
    private function generateDynamicCSS()
    {
        $autoTheme = Configuration::get('COLORTOOLTIP_AUTO_THEME');
        
        // Si el tema automático está activado, no aplicamos colores fijos
        if ($autoTheme) {
            $customCss = "
                <style>
                .color-tooltip-custom {
                    border-radius: " . Configuration::get('COLORTOOLTIP_BORDER_RADIUS') . "px;
                    opacity: " . (Configuration::get('COLORTOOLTIP_OPACITY') / 100) . ";
                    font-size: " . Configuration::get('COLORTOOLTIP_FONT_SIZE') . "px;
                    padding: " . Configuration::get('COLORTOOLTIP_PADDING') . "px " . (Configuration::get('COLORTOOLTIP_PADDING') + 4) . "px;
                    z-index: " . Configuration::get('COLORTOOLTIP_ZINDEX') . ";
                    transition: opacity " . (Configuration::get('COLORTOOLTIP_ANIMATION_SPEED') / 1000) . "s ease, transform " . (Configuration::get('COLORTOOLTIP_ANIMATION_SPEED') / 1000) . "s ease;
                }
                </style>
            ";
        } else {
            $bg = Configuration::get('COLORTOOLTIP_BG');
            $text = Configuration::get('COLORTOOLTIP_TEXT');
            
            $customCss = "
                <style>
                .color-tooltip-custom {
                    background: {$bg} !important;
                    color: {$text} !important;
                    border-radius: " . Configuration::get('COLORTOOLTIP_BORDER_RADIUS') . "px;
                    opacity: " . (Configuration::get('COLORTOOLTIP_OPACITY') / 100) . ";
                    font-size: " . Configuration::get('COLORTOOLTIP_FONT_SIZE') . "px;
                    padding: " . Configuration::get('COLORTOOLTIP_PADDING') . "px " . (Configuration::get('COLORTOOLTIP_PADDING') + 4) . "px;
                    z-index: " . Configuration::get('COLORTOOLTIP_ZINDEX') . ";
                    transition: opacity " . (Configuration::get('COLORTOOLTIP_ANIMATION_SPEED') / 1000) . "s ease, transform " . (Configuration::get('COLORTOOLTIP_ANIMATION_SPEED') / 1000) . "s ease;
                }
                .color-tooltip-custom.theme-light,
                .color-tooltip-custom.theme-dark {
                    background: {$bg} !important;
                    color: {$text} !important;
                }
                </style>
            ";
        }

        return $customCss;
    }

    /**
     * Genera JavaScript dinámico con la configuración
     */
    private function generateDynamicJS()
    {
        $config = array(
            'showPreview' => (bool)Configuration::get('COLORTOOLTIP_SHOW_PREVIEW'),
            'showFormats' => (bool)Configuration::get('COLORTOOLTIP_SHOW_FORMATS'),
            'showHistory' => (bool)Configuration::get('COLORTOOLTIP_SHOW_HISTORY'),
            'maxHistorySize' => (int)Configuration::get('COLORTOOLTIP_HISTORY_SIZE'),
            'offsetX' => (int)Configuration::get('COLORTOOLTIP_OFFSET_X'),
            'offsetY' => (int)Configuration::get('COLORTOOLTIP_OFFSET_Y'),
            'autoTheme' => (bool)Configuration::get('COLORTOOLTIP_AUTO_THEME'),
        );

        $configJson = json_encode($config);

        return "
            <script>
            if (typeof window.ColorTooltip !== 'undefined') {
                Object.assign(window.ColorTooltip.config, {$configJson});
            }
            </script>
        ";
    }

    /**
     * Configuración del módulo
     */
    public function getContent()
    {
        $output = '';

        // Procesar formulario
        if (Tools::isSubmit('submitColortooltipModule')) {
            $output .= $this->processConfiguration();
        }

        // Mostrar formulario
        $output .= $this->renderForm();

        return $output;
    }

    /**
     * Procesa y guarda la configuración
     */
    private function processConfiguration()
    {
        $fields = array(
            'COLORTOOLTIP_BG',
            'COLORTOOLTIP_TEXT',
            'COLORTOOLTIP_BORDER_RADIUS',
            'COLORTOOLTIP_OPACITY',
            'COLORTOOLTIP_FONT_SIZE',
            'COLORTOOLTIP_PADDING',
            'COLORTOOLTIP_SHOW_PREVIEW',
            'COLORTOOLTIP_SHOW_FORMATS',
            'COLORTOOLTIP_SHOW_HISTORY',
            'COLORTOOLTIP_HISTORY_SIZE',
            'COLORTOOLTIP_OFFSET_X',
            'COLORTOOLTIP_OFFSET_Y',
            'COLORTOOLTIP_AUTO_THEME',
            'COLORTOOLTIP_ZINDEX',
            'COLORTOOLTIP_ANIMATION_SPEED',
            'COLORTOOLTIP_THROTTLE',
        );

        foreach ($fields as $field) {
            $value = Tools::getValue($field);
            
            // Validaciones específicas
            if ($field == 'COLORTOOLTIP_HISTORY_SIZE') {
                $value = max(1, min(10, (int)$value));
            }
            if ($field == 'COLORTOOLTIP_OPACITY') {
                $value = max(0, min(100, (int)$value));
            }
            if ($field == 'COLORTOOLTIP_BORDER_RADIUS') {
                $value = max(0, min(50, (int)$value));
            }
            
            Configuration::updateValue($field, $value);
        }

        return $this->displayConfirmation($this->l('Configuración actualizada correctamente'));
    }

    /**
     * Renderiza el formulario de configuración
     */
    private function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitColortooltipModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Estructura del formulario
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Configuración de Color Tooltip'),
                    'icon' => 'icon-cogs',
                ),
                'tabs' => array(
                    'appearance' => $this->l('Apariencia'),
                    'functionality' => $this->l('Funcionalidad'),
                    'advanced' => $this->l('Avanzado'),
                    'help' => $this->l('Ayuda'),
                ),
                'input' => array(
                    // ===== PESTAÑA APARIENCIA =====
                    array(
                        'type' => 'color',
                        'label' => $this->l('Color de fondo'),
                        'name' => 'COLORTOOLTIP_BG',
                        'desc' => $this->l('Color de fondo del tooltip (solo si el tema automático está desactivado)'),
                        'tab' => 'appearance',
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Color del texto'),
                        'name' => 'COLORTOOLTIP_TEXT',
                        'desc' => $this->l('Color del texto del tooltip (solo si el tema automático está desactivado)'),
                        'tab' => 'appearance',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Radio del borde'),
                        'name' => 'COLORTOOLTIP_BORDER_RADIUS',
                        'suffix' => 'px',
                        'desc' => $this->l('Radio de las esquinas del tooltip (0-50px)'),
                        'class' => 'fixed-width-sm',
                        'tab' => 'appearance',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Opacidad'),
                        'name' => 'COLORTOOLTIP_OPACITY',
                        'suffix' => '%',
                        'desc' => $this->l('Opacidad del tooltip (0-100%)'),
                        'class' => 'fixed-width-sm',
                        'tab' => 'appearance',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Tamaño de fuente'),
                        'name' => 'COLORTOOLTIP_FONT_SIZE',
                        'suffix' => 'px',
                        'desc' => $this->l('Tamaño de la fuente del texto (10-20px recomendado)'),
                        'class' => 'fixed-width-sm',
                        'tab' => 'appearance',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Padding'),
                        'name' => 'COLORTOOLTIP_PADDING',
                        'suffix' => 'px',
                        'desc' => $this->l('Espaciado interno del tooltip (8-20px recomendado)'),
                        'class' => 'fixed-width-sm',
                        'tab' => 'appearance',
                    ),

                    // ===== PESTAÑA FUNCIONALIDAD =====
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Mostrar preview del color'),
                        'name' => 'COLORTOOLTIP_SHOW_PREVIEW',
                        'desc' => $this->l('Muestra un cuadrado con el color actual en el tooltip'),
                        'is_bool' => true,
                        'values' => array(
                            array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Sí')),
                            array('id' => 'active_off', 'value' => 0, 'label' => $this->l('No'))
                        ),
                        'tab' => 'functionality',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Mostrar formatos de color'),
                        'name' => 'COLORTOOLTIP_SHOW_FORMATS',
                        'desc' => $this->l('Muestra el color en formato HEX, RGB y HSL'),
                        'is_bool' => true,
                        'values' => array(
                            array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Sí')),
                            array('id' => 'active_off', 'value' => 0, 'label' => $this->l('No'))
                        ),
                        'tab' => 'functionality',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Mostrar historial de colores'),
                        'name' => 'COLORTOOLTIP_SHOW_HISTORY',
                        'desc' => $this->l('Muestra los últimos colores utilizados'),
                        'is_bool' => true,
                        'values' => array(
                            array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Sí')),
                            array('id' => 'active_off', 'value' => 0, 'label' => $this->l('No'))
                        ),
                        'tab' => 'functionality',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Tamaño del historial'),
                        'name' => 'COLORTOOLTIP_HISTORY_SIZE',
                        'desc' => $this->l('Número de colores a guardar en el historial (1-10)'),
                        'class' => 'fixed-width-sm',
                        'tab' => 'functionality',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Offset horizontal'),
                        'name' => 'COLORTOOLTIP_OFFSET_X',
                        'suffix' => 'px',
                        'desc' => $this->l('Distancia horizontal del cursor al tooltip'),
                        'class' => 'fixed-width-sm',
                        'tab' => 'functionality',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Offset vertical'),
                        'name' => 'COLORTOOLTIP_OFFSET_Y',
                        'suffix' => 'px',
                        'desc' => $this->l('Distancia vertical del cursor al tooltip'),
                        'class' => 'fixed-width-sm',
                        'tab' => 'functionality',
                    ),

                    // ===== PESTAÑA AVANZADO =====
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Tema automático'),
                        'name' => 'COLORTOOLTIP_AUTO_THEME',
                        'desc' => $this->l('Ajusta automáticamente el color del tooltip según la luminosidad del color seleccionado'),
                        'is_bool' => true,
                        'values' => array(
                            array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Sí')),
                            array('id' => 'active_off', 'value' => 0, 'label' => $this->l('No'))
                        ),
                        'tab' => 'advanced',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Z-index'),
                        'name' => 'COLORTOOLTIP_ZINDEX',
                        'desc' => $this->l('Índice Z del tooltip (debe ser mayor que otros elementos)'),
                        'class' => 'fixed-width-md',
                        'tab' => 'advanced',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Velocidad de animación'),
                        'name' => 'COLORTOOLTIP_ANIMATION_SPEED',
                        'suffix' => 'ms',
                        'desc' => $this->l('Duración de la animación de entrada/salida (100-500ms recomendado)'),
                        'class' => 'fixed-width-sm',
                        'tab' => 'advanced',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Throttling del mouse'),
                        'name' => 'COLORTOOLTIP_THROTTLE',
                        'desc' => $this->l('Optimiza el rendimiento limitando la frecuencia de actualización del tooltip'),
                        'is_bool' => true,
                        'values' => array(
                            array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Sí')),
                            array('id' => 'active_off', 'value' => 0, 'label' => $this->l('No'))
                        ),
                        'tab' => 'advanced',
                    ),

                    // ===== PESTAÑA AYUDA =====
                    array(
                        'type' => 'html',
                        'name' => '',
                        'html_content' => $this->getHelpContent(),
                        'tab' => 'help',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Guardar'),
                    'class' => 'btn btn-default pull-right',
                ),
            ),
        );
    }

    /**
     * Obtiene los valores actuales de configuración
     */
    protected function getConfigFormValues()
    {
        return array(
            'COLORTOOLTIP_BG' => Configuration::get('COLORTOOLTIP_BG'),
            'COLORTOOLTIP_TEXT' => Configuration::get('COLORTOOLTIP_TEXT'),
            'COLORTOOLTIP_BORDER_RADIUS' => Configuration::get('COLORTOOLTIP_BORDER_RADIUS'),
            'COLORTOOLTIP_OPACITY' => Configuration::get('COLORTOOLTIP_OPACITY'),
            'COLORTOOLTIP_FONT_SIZE' => Configuration::get('COLORTOOLTIP_FONT_SIZE'),
            'COLORTOOLTIP_PADDING' => Configuration::get('COLORTOOLTIP_PADDING'),
            'COLORTOOLTIP_SHOW_PREVIEW' => Configuration::get('COLORTOOLTIP_SHOW_PREVIEW'),
            'COLORTOOLTIP_SHOW_FORMATS' => Configuration::get('COLORTOOLTIP_SHOW_FORMATS'),
            'COLORTOOLTIP_SHOW_HISTORY' => Configuration::get('COLORTOOLTIP_SHOW_HISTORY'),
            'COLORTOOLTIP_HISTORY_SIZE' => Configuration::get('COLORTOOLTIP_HISTORY_SIZE'),
            'COLORTOOLTIP_OFFSET_X' => Configuration::get('COLORTOOLTIP_OFFSET_X'),
            'COLORTOOLTIP_OFFSET_Y' => Configuration::get('COLORTOOLTIP_OFFSET_Y'),
            'COLORTOOLTIP_AUTO_THEME' => Configuration::get('COLORTOOLTIP_AUTO_THEME'),
            'COLORTOOLTIP_ZINDEX' => Configuration::get('COLORTOOLTIP_ZINDEX'),
            'COLORTOOLTIP_ANIMATION_SPEED' => Configuration::get('COLORTOOLTIP_ANIMATION_SPEED'),
            'COLORTOOLTIP_THROTTLE' => Configuration::get('COLORTOOLTIP_THROTTLE'),
        );
    }

    /**
     * Contenido de la pestaña de ayuda
     */
    private function getHelpContent()
    {
        return '
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-info-circle"></i> ' . $this->l('Guía de uso') . '
            </div>
            <div class="panel-body">
                <h4>' . $this->l('Configuración básica') . '</h4>
                <p>' . $this->l('El módulo funciona automáticamente en todos los inputs de tipo color con la clase .input-color') . '</p>
                
                <h4>' . $this->l('Personalización por input') . '</h4>
                <p>' . $this->l('Puedes personalizar cada input usando atributos data:') . '</p>
                <pre><code>&lt;input type="color" class="input-color" 
       data-color-tooltip="Descripción personalizada"
       data-show-preview="true"
       data-show-formats="true"
       data-show-history="true" /&gt;</code></pre>
                
                <h4>' . $this->l('Prioridad de contenido') . '</h4>
                <ol>
                    <li><code>data-color-tooltip</code> - ' . $this->l('Atributo específico') . '</li>
                    <li><code>data-tooltip</code> - ' . $this->l('Atributo genérico') . '</li>
                    <li><code>title</code> - ' . $this->l('Atributo title nativo') . '</li>
                    <li><code>.sr-only</code> - ' . $this->l('Texto para screen readers') . '</li>
                    <li>' . $this->l('Valor del color como fallback') . '</li>
                </ol>
                
                <h4>' . $this->l('Tema automático') . '</h4>
                <p>' . $this->l('Cuando está activado, el tooltip cambia automáticamente entre tema claro y oscuro según la luminosidad del color seleccionado.') . '</p>
                
                <h4>' . $this->l('Historial de colores') . '</h4>
                <p>' . $this->l('Los colores se guardan automáticamente en localStorage. Haz click en un color del historial para aplicarlo al input actual.') . '</p>
                
                <h4>' . $this->l('Rendimiento') . '</h4>
                <p>' . $this->l('El módulo usa un único tooltip reutilizable y event delegation para máxima eficiencia. El throttling está activado por defecto.') . '</p>
            </div>
        </div>
        
        <div class="alert alert-info">
            <h4>' . $this->l('Soporte') . '</h4>
            <p>' . $this->l('Si necesitas ayuda, consulta la documentación en GitHub o contacta con soporte técnico.') . '</p>
        </div>
        ';
    }
}
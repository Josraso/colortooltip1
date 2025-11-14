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
        $this->version = '2.1.0';
        $this->author = 'Tu Nombre';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Color Tooltip');
        $this->description = $this->l('Muestra el nombre del atributo de color en un tooltip simple al pasar el mouse sobre los selectores de color.');
        $this->confirmUninstall = $this->l('¿Estás seguro de que quieres desinstalar este módulo?');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('header')
            && $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/colortooltip.css');
        $this->context->controller->addJS($this->_path . 'views/js/colortooltip.js');
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/colortooltip.css');
        $this->context->controller->addJS($this->_path . 'views/js/colortooltip.js');
    }

    public function getContent()
    {
        $output = '<div class="panel">
            <div class="panel-heading">
                <i class="icon-info-circle"></i> ' . $this->l('Cómo usar') . '
            </div>
            <div class="panel-body">
                <h3>' . $this->l('Funcionamiento') . '</h3>
                <p>' . $this->l('Este módulo funciona automáticamente con todos los inputs de color de PrestaShop.') . '</p>
                <p>' . $this->l('Simplemente pasa el mouse sobre cualquier selector de color y verás el nombre del atributo en un tooltip.') . '</p>

                <h3>' . $this->l('Detecta automáticamente') . '</h3>
                <ul>
                    <li><code>input[type="color"]</code> - ' . $this->l('Inputs estándar HTML5') . '</li>
                    <li><code>.input-color</code> - ' . $this->l('Clase personalizada') . '</li>
                    <li><code>.color-picker</code> - ' . $this->l('Selectores de PrestaShop') . '</li>
                </ul>

                <h3>' . $this->l('Personalización') . '</h3>
                <p>' . $this->l('Para personalizar el texto del tooltip en un input específico, usa el atributo data-color-tooltip:') . '</p>
                <pre><code>&lt;input type="color" data-color-tooltip="Rojo pálido" value="#ff6b6b"&gt;</code></pre>

                <h3>' . $this->l('Orden de prioridad para el texto') . '</h3>
                <ol>
                    <li><code>data-color-tooltip</code> - ' . $this->l('Atributo específico del módulo') . '</li>
                    <li><code>data-tooltip</code> - ' . $this->l('Atributo genérico') . '</li>
                    <li><code>title</code> - ' . $this->l('Atributo title HTML') . '</li>
                    <li>' . $this->l('Texto del &lt;label&gt; asociado') . '</li>
                    <li><code>placeholder</code> - ' . $this->l('Texto placeholder del input') . '</li>
                </ol>

                <div class="alert alert-info">
                    <strong>' . $this->l('Sin configuración necesaria') . '</strong><br>
                    ' . $this->l('No hay nada que configurar. El módulo funciona automáticamente después de instalarlo.') . '
                </div>
            </div>
        </div>';

        return $output;
    }
}

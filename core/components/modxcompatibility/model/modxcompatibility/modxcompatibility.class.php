<?php
class MODxCompatibility
{
    public $modx;
    public $namespace = 'modxcompatibility';
    public $options = [];

    public function __construct(modX &$modx, array $options = [])
    {
        $this->modx =& $modx;

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modxcompatibility/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/modxcompatibility/');

        /* loads some default paths for easier management */
        $this->options = array_merge([
            'namespace' => $this->namespace,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'templatesPath' => $corePath . 'templates/',
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'connectorUrl' => $assetsUrl . 'connector.php'
        ], $options);

        $this->modx->addPackage('modxcompatibility', $this->getOption('modelPath'));
        $this->modx->lexicon->load('modxcompatibility:default');
        $this->autoload();
    }

    protected function autoload()
    {
        require_once $this->getOption('corePath') . 'vendor/autoload.php';
    }
}

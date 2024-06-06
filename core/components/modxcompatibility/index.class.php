<?php

/**
 * @package modxcompatibility
 */
abstract class MODxCompatibilityBaseManagerController extends modExtraManagerController
{
    /** @var MODxCompatibility $modxcompatibility */
    public $modxcompatibility;

    public function initialize()
    {
        if (empty($this->modx->version)) {
            $this->modx->getVersionData();
        }
        $version = (int) $this->modx->version['version'];
        if ($version > 2) {
            $this->modxcompatibility = new \ModxCompatibility\v3\Service($this->modx);
        } else {
            $corePath = $this->modx->getOption('modxcompatibility.core_path', null, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modxcompatibility/');
            $this->modxcompatibility = $this->modx->getService(
                'modxcompatibility',
                'MODxCompatibility',
                $corePath . 'model/modxcompatibility/',
                array(
                    'core_path' => $corePath
                )
            );
        }

        $this->addCss($this->modxcompatibility->getOption('cssUrl') . 'mgr.css?v=1');
        $this->addJavascript($this->modxcompatibility->getOption('jsUrl') . 'mgr/modxcompatibility.js');

        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            modxcompatibility.config = ' . $this->modx->toJSON($this->modxcompatibility->options) . ';
            modxcompatibility.config.connector_url = "' . $this->modxcompatibility->getOption('connectorUrl') . '";
            modxcompatibility.config.version = "' . $version . '";
        });
        </script>');

        parent::initialize();
    }

    public function getLanguageTopics()
    {
        return array('modxcompatibility:default');
    }

    public function checkPermissions()
    {
        return $this->modx->hasPermission('workspaces');
    }
}

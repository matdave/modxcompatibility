<?php
require_once dirname(dirname(__FILE__)) . '/index.class.php';

class MODxCompatibilityExtrasManagerController extends MODxCompatibilityManagerControllerCore
{
    public function process(array $scriptProperties = array())
    {

    }

    public function getPageTitle()
    {
        return $this->modx->lexicon('modxcompatibility');
    }

    public function loadCustomCssJs()
    {
        $latestVersion = '1';


        $this->addJavascript($this->modxcompatibility->getOption('jsUrl') . 'mgr/widgets/extras/grid.js?v=' . $latestVersion);
        $this->addJavascript($this->modxcompatibility->getOption('jsUrl') . 'mgr/widgets/extras/panel.js?v=' . $latestVersion);
        $this->addLastJavascript($this->modxcompatibility->getOption('jsUrl') . 'mgr/sections/extras.js?v=' . $latestVersion);

        $this->addHtml("<script>
        Ext.onReady(function() {
            Ext.getCmp('modx-layout').hideLeftbar(true, false);
            MODx.load({ xtype: 'modxcompatibility-page-extras'});
        });
        </script>");

    }

    public function getTemplateFile()
    {
        return $this->modxcompatibility->getOption('templatesPath') . 'extras.tpl';
    }
}

<?php

use ModxCompatibility\v2\CheckExtras;

class modxCompatibilityCheckExtrasProcessor extends modProcessor {
    public $languageTopics = 'modxcompatibility:default';
    public $permission = 'workspaces';

    public function process()
    {
        $checkExtras = new CheckExtras($this->modx->modxcompatibility, $this->getProperties());
        return $checkExtras->run();
    }
}
return 'modxCompatibilityCheckExtrasProcessor';

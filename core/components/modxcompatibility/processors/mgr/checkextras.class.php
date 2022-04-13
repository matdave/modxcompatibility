<?php

use MODxCompatibility\CheckExtras;

class modxCompatibilityCheckExtrasProcessor extends modProcessor {
    public $languageTopics = 'modxcompatibility:default';
    public $permission = 'workspaces';

    public function process()
    {
        $checkExtras = new CheckExtras($this->modx, $this->getProperties());
        return $checkExtras->run();
    }
}

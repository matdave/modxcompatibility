<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('modxcompatibility.core_path', null, $modx->getOption('core_path') . 'components/modxcompatibility/');
require_once $corePath . 'model/modxcompatibility/modxcompatibility.class.php';
$modx->modxcompatibility = new MODxCompatibility($modx);
$modx->lexicon->load('modxcompatibility:default');
/* handle request */
$path = $modx->getOption('processorsPath', $modx->modxcompatibility->options, $corePath . 'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));

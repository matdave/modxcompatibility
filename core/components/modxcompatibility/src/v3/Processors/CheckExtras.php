<?php

namespace ModxCompatibility\v3\Processors;

use MODX\Revolution\Processors\Processor;
use MODX\Revolution\Transport\modTransportPackage;
use MODX\Revolution\Transport\modTransportProvider;

class CheckExtras extends Processor
{
    public function getLanguageTopics() {
        return [
            'modxcompatibility:default',
        ];
    }
    public function process()
    {
        $packs = [];
        $providerCache = [];
        $workspace = $this->getProperty('workspace') ?: 1;
        $total = $this->getProperty('total') ?: 20;
        $start = $this->getProperty('start') ?: 0;
        $search = $this->getProperty('search') ?: '';
        $packages = $this->modx->call(modTransportPackage::class, 'listPackages', [
            &$this->modx,
            $workspace,
            $total,
            $start,
            $search
        ]);
        if(!empty($packages['collection'])){
            foreach($packages['collection'] as $p){
                $update = $this->checkForUpdates($p, $providerCache);
                $info = $this->checkInfo($p, $providerCache);
                $packs[] = [
                    'signature' => $p->get('signature'),
                    'package_name' => $p->get('package_name'),
                    'update' => $update,
                    'info' => $info
                ];
            }
        }
        return json_encode([
            'success' => true,
            'total' => $packages['total'],
            'message' => '',
            'results' => array_values($packs),
        ], true);
    }

    private function formatVersion($version): string
    {
        if($version === '10000000' || $version === '100.0') {
            return $this->modx->lexicon('modxcompatibility.extras.latest');
        }
        if(strpos($version, '.') !== false){
            return $version.'.x';
        } else{
            return $version[0].'.'.($version[1] ?: '0').'.x';
        }
    }

    private function checkForUpdates($package, $providerCache = []): array
    {
        $updates = [];
        if ($package->get('provider') > 0 && $this->modx->getOption('auto_check_pkg_updates',null,false)) {
            $updateCacheKey = 'mgr/providers/updateinfo/'.$package->get('provider').'/'.$package->get('signature');
            $updateCacheOptions = [
                \xPDO::OPT_CACHE_KEY => $this->modx->cacheManager->getOption('cache_packages_key', null, 'packages'),
                \xPDO::OPT_CACHE_HANDLER => $this->modx->cacheManager->getOption('cache_packages_handler', null, $this->modx->cacheManager->getOption(\xPDO::OPT_CACHE_HANDLER)),
            ];
            $updates = $this->modx->cacheManager->get($updateCacheKey, $updateCacheOptions);
            if (empty($updates)) {
                /* cache providers to speed up load time */
                /** @var modTransportProvider $provider */
                if (!empty($providerCache[$package->get('provider')])) {
                    $provider =& $providerCache[$package->get('provider')];
                } else {
                    $provider = $package->getOne('Provider');
                    if ($provider) {
                        $providerCache[$provider->get('id')] = $provider;
                    }
                }
                if ($provider) {
                    $updates = [];
                    $latest = $provider->latest($package->get('signature'));
                    foreach($latest as $l) {
                        $updates[] = [
                            'name' => $l['name'] ?? '',
                            'version' => $l['version'] ?? '',
                            'minimum_supports' => $this->formatVersion($l['minimum_supports'] ?? ''),
                            'minimum_supports_raw' => $l['minimum_supports'] ?? '',
                            'breaks_at' => $this->formatVersion($l['breaks_at'] ?? ''),
                            'breaks_at_raw' => $l['breaks_at'] ?? ''
                        ];
                    }
                    $this->modx->cacheManager->set($updateCacheKey, $updates, 1600, $updateCacheOptions);
                }
            }
        }
        return $updates;
    }

    private function checkInfo($package, $providerCache = []): array
    {
        $info = [];
        if ($package->get('provider') > 0 && $this->modx->getOption('auto_check_pkg_updates',null,false)) {
            $infoCacheKey = 'mgr/providers/info/'.$package->get('provider').'/'.$package->get('signature');
            $infoCacheOptions = [
                \xPDO::OPT_CACHE_KEY => $this->modx->cacheManager->getOption('cache_packages_key', null, 'packages'),
                \xPDO::OPT_CACHE_HANDLER => $this->modx->cacheManager->getOption('cache_packages_handler', null, $this->modx->cacheManager->getOption(\xPDO::OPT_CACHE_HANDLER)),
            ];
            $info = $this->modx->cacheManager->get($infoCacheKey, $infoCacheOptions);
            if (empty($info)) {
                $info = [];
                /* cache providers to speed up load time */
                /** @var modTransportProvider $provider */
                if (!empty($providerCache[$package->get('provider')])) {
                    $provider =& $providerCache[$package->get('provider')];
                } else {
                    $provider = $package->getOne('Provider');
                    if ($provider) {
                        $providerCache[$provider->get('id')] = $provider;
                    }
                }
                if ($provider) {
                    $l = $provider->info($package->get('signature'));
                    $info =  [
                        'name' => $l['name'] ?? '',
                        'version' => $l['version'] ?? '',
                        'minimum_supports' => $this->formatVersion($l['minimum_supports'] ?? ''),
                        'minimum_supports_raw' => $l['minimum_supports'] ?? '',
                        'breaks_at' => $this->formatVersion($l['breaks_at'] ?? ''),
                        'breaks_a_raw' => $l['breaks_at'] ?? ''
                    ];
                    $this->modx->cacheManager->set($infoCacheKey, $info, 1600, $infoCacheOptions);
                }
            }
        }
        return $info;
    }
}

<?php

namespace ModxCompatibility;

use MODX\Revolution\modX;
use MODxCompatibility;

class CheckExtras
{
    /** @var modX $modx */
    public $modx;

    /** @var MODxCompatibility */
    public $modxcompatibility;

    public array $scriptProperties;

    public function __construct(\MODxCompatibility &$modxcompatibility, array $scriptProperties = [])
    {
        $this->modxcompatibility =& $modxcompatibility;
        $this->modx =& $modxcompatibility->modx;
        $this->scriptProperties = $scriptProperties;
    }

    public function run()
    {
        $packs = [];
        $providerCache = [];
        $packages = $this->modx->call('transport.modTransportPackage', 'listPackages', [
            &$this->modx,
            $this->modx->getOption('workspace',$this->scriptProperties,1),
            $this->modx->getOption('total',$this->scriptProperties,20),
            $this->modx->getOption('start',$this->scriptProperties,0),
            $this->modx->getOption('search',$this->scriptProperties,'')
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
        if($version === '10000000') {
            return 'latest';
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
                            'name' => $l['name'],
                            'version' => $l['version'],
                            'minimum_supports' => $this->formatVersion($l['minimum_supports']),
                            'breaks_at' => $this->formatVersion($l['breaks_at'])
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
                        'name' => $l['name'],
                        'version' => $l['version'],
                        'minimum_supports' => $this->formatVersion($l['minimum_supports']),
                        'breaks_at' => $this->formatVersion($l['breaks_at'])
                    ];
                    $this->modx->cacheManager->set($infoCacheKey, $info, 1600, $infoCacheOptions);
                }
            }
        }
        return $info;
    }
}

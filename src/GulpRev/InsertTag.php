<?php

namespace Oneup\Contao\GulpRev;

use Contao\Frontend;

class InsertTag extends Frontend
{
    private $filePaths;
    private $manifestPath;

    public function __construct()
    {
        $this->manifestPath = TL_ASSETS_URL.'assets/website/build/rev-manifest.json';
        parent::__construct();
    }

    public function replace($strTag)
    {
        $arrSplit = explode('::', $strTag);

        if ($arrSplit[0] != 'asset' && $arrSplit[0] != 'cache_asset') {
            return false;
        }

        if (!isset($arrSplit[1])) {
            return false;
        }

        return $this->getAssetVersion($arrSplit[1]);
    }

    private function getAssetVersion($extension)
    {
        if ($GLOBALS['TL_CONFIG']['env'] === 'dev') {
            return 'build.'.$extension;
        }

        if (!file_exists($this->manifestPath)) {
            throw new \Exception(sprintf('Cannot find manifest file: "%s"', $this->manifestPath));
        }
        $this->filePaths = json_decode(file_get_contents($this->manifestPath), true);
        if (!isset($this->filePaths['build.min.'.$extension])) {
            throw new \Exception(sprintf('There is no file "%s" in the version manifest!', 'build.min.'.$extension));
        }

        return $this->filePaths['build.min.'.$extension];
    }
}
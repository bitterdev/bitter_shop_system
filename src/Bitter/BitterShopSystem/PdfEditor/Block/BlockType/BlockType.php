<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\PdfEditor\Block\BlockType;

use Concrete\Core\Application\Application;
use Concrete\Core\Package\PackageService;
use Concrete\Package\BitterShopSystem\Controller;

class BlockType
{
    protected $app;
    protected $packageService;
    /** @var Controller */
    protected $pkg;

    public function __construct(
        Application $app,
        PackageService $packageService
    )
    {
        $this->app = $app;
        $this->packageService = $packageService;
        $this->pkg = $this->packageService->getByHandle("bitter_shop_system")->getController();
    }

    protected function hexToRgb($hex)
    {
        $hex = str_replace('#', '', $hex);
        $length = strlen($hex);
        $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
        return $rgb;
    }
}

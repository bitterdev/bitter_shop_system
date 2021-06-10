<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\API\V1\Middleware;

use Bitter\BitterShopSystem\API\V1\Serializer\SimpleSerializer;
use Concrete\Core\Http\Middleware\FractalNegotiatorMiddleware as CoreFractalNegotiatorMiddleware;

class FractalNegotiatorMiddleware extends CoreFractalNegotiatorMiddleware
{
    public function getSerializer(): SimpleSerializer
    {
        return new SimpleSerializer();
    }
}

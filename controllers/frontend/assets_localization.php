<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Controller\Frontend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Http\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Response;

class AssetsLocalization extends Controller
{

    /**
     * @return Response
     */
    public function getJavascript()
    {
        return $this->createJavascriptResponse(
            sprintf(
                'var ccmi18n_bitter_shop_system = %s;',
                json_encode(
                    [
                        'editTemplateSettings' => t("Edit Template Settings"),
                        'templateSettings' => t("Template Settings"),
                        'addContentToTemplate' => t("Add Content to The Template"),
                        'addContent' => t("Add Content"),
                        'editSettingsDialogTitle' => t("Edit Settings")
                    ]
                )
            )
        );
    }

    /**
     * @param string $content
     *
     * @return Response
     */
    private function createJavascriptResponse($content)
    {
        $rf = $this->app->make(ResponseFactoryInterface::class);

        return $rf->create(
            $content,
            200,
            [
                'Content-Type' => 'application/javascript; charset=' . APP_CHARSET,
                'Content-Length' => strlen($content),
            ]
        );
    }
}

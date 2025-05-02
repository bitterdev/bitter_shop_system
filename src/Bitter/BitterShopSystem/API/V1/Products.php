<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\API\V1;

use Bitter\BitterShopSystem\Product\ProductService;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class Products
{
    protected ProductService $productService;
    protected Request $request;

    public function __construct(
        ProductService $productService,
        Request        $request
    )
    {
        $this->productService = $productService;
        $this->request = $request;
    }

    public function getProducts(): JsonResponse
    {
        $editResponse = new EditResponse();

        if ($this->request->query->has("locale")) {
            $products = $this->productService->getAllByLocale((string)$this->request->query->get("locale"));
        } else {
            $products = $this->productService->getAll();
        }

        $editResponse->setAdditionalDataAttribute("products", $products);

        return new JsonResponse($editResponse);
    }
}
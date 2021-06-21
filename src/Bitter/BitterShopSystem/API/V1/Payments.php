<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\API\V1;

use Bitter\BitterShopSystem\PaymentProvider\PaymentProviderInterface;
use Bitter\BitterShopSystem\PaymentProvider\PaymentProviderService;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Error\ErrorList\ErrorList;
use Symfony\Component\HttpFoundation\JsonResponse;

class Payments
{
    protected $paymentProviderService;

    public function __construct(
        PaymentProviderService $paymentProviderService
    )
    {
        $this->paymentProviderService = $paymentProviderService;
    }

    public function processPayment($paymentProviderHandle): JsonResponse
    {
        $editResponse = new EditResponse();
        $errorList = new ErrorList();

        $paymentProvider = $this->paymentProviderService->getByHandle($paymentProviderHandle);

        if ($paymentProvider instanceof PaymentProviderInterface) {
            $paymentProvider->processPayment();

            $editResponse->setMessage(t("Payment processed successfully."));
        } else {
            $errorList->add(t("The given payment provider handle is invalid."));
        }

        $editResponse->setError($errorList);

        return new JsonResponse($editResponse);
    }
}
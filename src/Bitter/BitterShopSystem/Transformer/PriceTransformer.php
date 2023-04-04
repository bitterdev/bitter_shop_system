<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Transformer;

use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Entity\ShippingCost;
use Bitter\BitterShopSystem\Entity\TaxRate;
use Concrete\Core\Config\Repository\Repository;
use HtmlObject\Element;

class PriceTransformer
{
    protected $config;
    protected $moneyTransformer;

    public function __construct(
        Repository $config,
        MoneyTransformer $moneyTransformer
    )
    {
        $this->config = $config;
        $this->moneyTransformer = $moneyTransformer;
    }

    public function transform(Product $product): string
    {
        $price = new Element("div", "");
        $taxes = null;
        $shipping = null;
        $includeTax = $this->config->get("bitter_shop_system.display_prices_including_tax", false);

        if ($product->getPriceDiscounted() > 0) {
            $price->appendChild(
                new Element(
                    "div",
                    $this->moneyTransformer->transform($product->getPriceRegular($includeTax)),
                    [
                        "class" => "old"
                    ]
                )
            );

            $price->appendChild(
                new Element(
                    "div",
                    $this->moneyTransformer->transform($product->getPriceDiscounted($includeTax)),
                    [
                        "class" => "new"
                    ]
                )
            );
        } else {
            $price->appendChild(
                new Element(
                    "div",
                    $this->moneyTransformer->transform($product->getPriceRegular($includeTax)),
                    [
                        "class" => "regular"
                    ]
                )
            );
        }

        if ($product->getTaxRate() instanceof TaxRate && $product->getTaxRate()->getRate() > 0) {
            $taxRate = $product->getTaxRate()->getRate();

            if ($includeTax) {
                $taxes = new Element(
                    "div",
                    t("incl. %s%% %s", number_format($taxRate, 2), $product->getTaxRate()->getName()),
                    [
                        "class" => "text-muted"
                    ]
                );
            } else {
                $taxes = new Element(
                    "div",
                    t("excl. %s%% %s", number_format($taxRate, 2), $product->getTaxRate()->getName()),
                    [
                        "class" => "text-muted"
                    ]
                );
            }
        }

        if ($product->getShippingCost() instanceof ShippingCost && $product->getShippingCost()->getPrice() > 0) {
            $shippingCost = $product->getShippingCost()->getPrice($includeTax);

            $shipping = new Element(
                "div",
                t(" excl. %s shipping", $this->moneyTransformer->transform($shippingCost)),
                [
                    "class" => "text-muted"
                ]
            );
        }

        return new Element(
            "div",
            [
                $price,
                $taxes,
                $shipping
            ],
            [
                "class" => "price"
            ]
        );

    }
}
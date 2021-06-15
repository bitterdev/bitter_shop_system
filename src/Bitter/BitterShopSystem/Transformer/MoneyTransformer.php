<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Transformer;

use Concrete\Core\Config\Repository\Repository;

class MoneyTransformer
{
    protected $config;

    public function __construct(
        Repository $config
    )
    {
        $this->config = $config;
    }

    public function transform(float $amount): string
    {
        $valueFormatted = number_format(
            $amount,
            (int)$this->config->get("bitter_shop_system.money_formatting.decimals", 2),
            (string)$this->config->get("bitter_shop_system.money_formatting.decimal_point", "."),
            (string)$this->config->get("bitter_shop_system.money_formatting.thousands_separator", ",")
        );

        $currencySymbol = (string)$this->config->get("bitter_shop_system.money_formatting.currency_symbol", "$");
        $space = str_repeat(" ", (int)$this->config->get("bitter_shop_system.money_formatting.currency_symbol_spaces", 1));

        if ((string)$this->config->get("bitter_shop_system.money_formatting.currency_symbol_position", "left")) {
            if ($amount < 0) {
                $valueFormatted = number_format(
                    $amount * -1,
                    (int)$this->config->get("bitter_shop_system.money_formatting.decimals", 2),
                    (string)$this->config->get("bitter_shop_system.money_formatting.decimal_point", "."),
                    (string)$this->config->get("bitter_shop_system.money_formatting.thousands_separator", ",")
                );

                return "-" . $currencySymbol . $space . $valueFormatted;
            } else {
                return $currencySymbol . $space . $valueFormatted;
            }
        } else {
            return $valueFormatted . $space . $currencySymbol;
        }

    }
}
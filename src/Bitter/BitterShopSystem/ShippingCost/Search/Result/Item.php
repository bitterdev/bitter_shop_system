<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\ShippingCost\Search\Result;

use Bitter\BitterShopSystem\Entity\ShippingCost;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Search\Result\Item as SearchResultItem;
use Concrete\Core\Search\Result\Result as SearchResult;

class Item extends SearchResultItem
{
    public $id;
    
    public function __construct(SearchResult $result, Set $columns, $item)
    {
        parent::__construct($result, $columns, $item);
        $this->populateDetails($item);
    }
    
    /**
    * @param ShippingCost $item
    */
    protected function populateDetails($item)
    {
        $this->id = $item->getId();
    }
}

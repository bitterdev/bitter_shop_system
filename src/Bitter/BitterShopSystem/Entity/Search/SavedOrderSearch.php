<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */
/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
/** @noinspection PhpFullyQualifiedNameUsageInspection */

namespace Bitter\BitterShopSystem\Entity\Search;

use Concrete\Core\Entity\Search\SavedSearch;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="`SavedOrderSearchQueries`")
 */
class SavedOrderSearch extends SavedSearch
{
    /**
    * @var integer
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\Column(name="`id`", type="integer", nullable=true)
    */
    protected $id;
    
}

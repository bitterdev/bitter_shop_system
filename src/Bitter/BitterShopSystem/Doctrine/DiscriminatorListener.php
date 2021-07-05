<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Doctrine;

use Bitter\BitterShopSystem\Entity\Attribute\Key\CustomerKey;
use Bitter\BitterShopSystem\Entity\Attribute\Key\ProductKey;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use ReflectionClass;

class DiscriminatorListener implements EventSubscriber
{
    protected $driver;

    public function getSubscribedEvents(): array
    {
        return [Events::loadClassMetadata];
    }

    public function __construct(EntityManager $db)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->driver = $db->getConfiguration()->getMetadataDriverImpl();
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        $class = $event->getClassMetadata()->name;

        /** @noinspection PhpUnhandledExceptionInspection */
        $reflection = new ReflectionClass($class);

        if ($reflection->isSubclassOf("Concrete\\Core\\Entity\\Attribute\\Key\\Key")) {
            if ($class === "Bitter\\BitterShopSystem\\Entity\\Attribute\\Key\\ProductKey") {
                $event->getClassMetadata()->discriminatorMap["productkey"] = ProductKey::class;
                $event->getClassMetadata()->discriminatorValue = 'productkey';
            } else if ($class === "Bitter\\BitterShopSystem\\Entity\\Attribute\\Key\\CustomerKey") {
                $event->getClassMetadata()->discriminatorMap["customerkey"] = CustomerKey::class;
                $event->getClassMetadata()->discriminatorValue = 'customerkey';
            }
        }
    }
}
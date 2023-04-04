<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Backup\ContentImporter\Importer\Routine;

use Bitter\BitterShopSystem\Attribute\Key\CustomerKey;
use Bitter\BitterShopSystem\Customer\CustomerService;
use Bitter\BitterShopSystem\Entity\Customer;
use Concrete\Core\Backup\ContentImporter\Importer\Routine\AbstractRoutine;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\UserInfoRepository;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;

class ImportCustomersRoutine extends AbstractRoutine
{
    public function getHandle(): string
    {
        return 'customers';
    }

    public function import(SimpleXMLElement $element)
    {
        $app = Application::getFacadeApplication();
        /** @var CustomerService $customerService */
        $customerService = $app->make(CustomerService::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @var UserInfoRepository $userInfoRepository */
        $userInfoRepository = $app->make(UserInfoRepository::class);

        if (isset($element->customers)) {
            foreach ($element->customers->customer as $item) {
                $pkg = static::getPackageObject($item['package']);
                $user = $userInfoRepository->getByName((string)$item["user"]);
                $customerEntry = $customerService->getByMailAddress((string)$item["email"]);

                if (!$customerEntry instanceof Customer) {
                    $customerEntry = new Customer();
                    $customerEntry->setPackage($pkg);
                }

                if ($user instanceof User) {
                    $customerEntry->setUser($user);
                }

                $customerEntry->setEmail((string)$item["email"]);

                $entityManager->persist($customerEntry);
                $entityManager->flush();

                if (isset($item->attributes)) {
                    foreach ($item->attributes->children() as $attr) {
                        $handle = (string)$attr['handle'];
                        $ak = CustomerKey::getByHandle($handle);
                        if (is_object($ak)) {
                            $value = $ak->getController()->importValue($attr);
                            $customerEntry->setAttribute($handle, $value);
                        }
                    }
                }
            }
        }
    }

}

<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

namespace Concrete\Package\BitterShopSystem\Controller\Dialog\TaxRates\Bulk;

use Bitter\BitterShopSystem\Entity\TaxRate;
use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse as UserEditResponse;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Url;
use Doctrine\ORM\EntityManagerInterface;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/tax_rates/bulk/delete';
    protected $taxRates = [];
    protected $canEdit = false;
    protected $excluded = false;

    public function view()
    {
        $this->populateTaxRates();

        $this->set('taxRates', $this->taxRates);
        $this->set('excluded', $this->excluded);
    }

    private function populateTaxRates()
    {
        $taxRateIds = $this->request("item");

        if (is_array($taxRateIds) && count($taxRateIds) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            foreach($taxRateIds as $taxRateId) {
                $this->taxRates[] = $entityManager->getRepository(TaxRate::class)->findOneBy(["id" => (int)$taxRateId]);
            }
        }
    }

    public function submit()
    {
        $r = new UserEditResponse();

        $this->populateTaxRates();

        if (!$this->validateAction()) {
            $r->setError(new \Exception(t('Invalid Token')));
            $r->outputJSON();
            $this->app->shutdown();
        }

        $count = 0;

        if (count($this->taxRates) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            $db = $entityManager->getConnection();

            $db->executeQuery("SET foreign_key_checks = 0");

            foreach ($this->taxRates as $taxRate) {
                $entityManager->remove($taxRate);
                $entityManager->flush();
            }

            $db->executeQuery("SET foreign_key_checks = 1");
        }

        $r->setMessage(t2('%s tax rate deleted', '%s tax rates deleted', $count));
        $r->setTitle(t('Tax Rates Deleted'));
        $r->setRedirectURL(Url::to('/dashboard/bitter_shop_system/tax_rates'));
        $r->outputJSON();
    }

    protected function canAccess()
    {
        $user = new User();
        return $user->isSuperUser();
    }
}

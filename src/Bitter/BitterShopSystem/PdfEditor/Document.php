<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\PdfEditor;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\File;
use Concrete\Core\Support\Facade\Application;
use setasign\Fpdi\Fpdi;

class Document extends Fpdi
{
    protected $_tplIdx;

    public function Header()
    {
        $app = Application::getFacadeApplication();
        /** @var Repository $config */
        $config = $app->make(Repository::class);

        if (is_null($this->_tplIdx)) {
            $letterhead = File::getByID((int)$config->get("bitter_shop_system.pdf_editor.letterhead.first_page_id"));
        } else {
            $letterhead = File::getByID((int)$config->get("bitter_shop_system.pdf_editor.letterhead.following_page_id"));
        }

        if ($letterhead instanceof \Concrete\Core\Entity\File\File) {
            $letterheadApprovedVersion = $letterhead->getApprovedVersion();

            if ($letterheadApprovedVersion instanceof Version) {
                /** @noinspection PhpUnhandledExceptionInspection */
                /** @noinspection PhpParamsInspection */
                $this->setSourceFile(DIR_BASE . $letterheadApprovedVersion->getRelativePath());

                /** @noinspection PhpUnhandledExceptionInspection */
                $this->_tplIdx = $this->importPage(1);

                $this->useTemplate($this->_tplIdx);
            }
        }
    }
}

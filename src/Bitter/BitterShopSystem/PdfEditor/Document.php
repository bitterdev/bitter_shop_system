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

    public function GetMultiCellHeight($w, $h, $txt, $border=null, $align='J') {
        // Calculate MultiCell with automatic or explicit line breaks height
        // $border is un-used, but I kept it in the parameters to keep the call
        //   to this function consistent with MultiCell()
        $cw = &$this->CurrentFont['cw'];
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
        $s = str_replace("\r",'',$txt);
        $nb = strlen($s);
        if($nb>0 && $s[$nb-1]=="\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $ns = 0;
        $height = 0;
        while($i<$nb)
        {
            // Get next character
            $c = $s[$i];
            if($c=="\n")
            {
                // Explicit line break
                if($this->ws>0)
                {
                    $this->ws = 0;
                    $this->_out('0 Tw');
                }
                //Increase Height
                $height += $h;
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                continue;
            }
            if($c==' ')
            {
                $sep = $i;
                $ls = $l;
                $ns++;
            }
            $l += $cw[$c];
            if($l>$wmax)
            {
                // Automatic line break
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                    if($this->ws>0)
                    {
                        $this->ws = 0;
                        $this->_out('0 Tw');
                    }
                    //Increase Height
                    $height += $h;
                }
                else
                {
                    if($align=='J')
                    {
                        $this->ws = ($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
                        $this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
                    }
                    //Increase Height
                    $height += $h;
                    $i = $sep+1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
            }
            else
                $i++;
        }
        // Last chunk
        if($this->ws>0)
        {
            $this->ws = 0;
            $this->_out('0 Tw');
        }
        //Increase Height
        $height += $h;

        return $height;
    }

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

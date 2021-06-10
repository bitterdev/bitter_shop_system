<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Attribute\MultipleFiles;

use Bitter\BitterShopSystem\Entity\Attribute\Key\Settings\MultipleFilesSettings;
use Bitter\BitterShopSystem\Entity\Attribute\Value\Value\MultipleFilesSelectedFiles;
use Bitter\BitterShopSystem\Entity\Attribute\Value\Value\MultipleFilesValue;
use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Backup\ContentImporter\ValueInspector\ValueInspector;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\Error\ErrorList\Error\FieldNotPresentError;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\ErrorList\Field\AttributeField;
use Concrete\Core\File\File;
use Concrete\Core\Form\Service\Validation;
use HtmlObject\Element;
use SimpleXMLElement;

class Controller extends AttributeTypeController
{
    /** @var int|null */
    protected $akMaxFilesCount;

    public function getIconFormatter(): FontAwesomeIconFormatter
    {
        return new FontAwesomeIconFormatter('files-o');
    }

    public function getSearchIndexValue(): bool
    {
        return false;
    }

    public function getAttributeKeySettingsClass(): string
    {
        return MultipleFilesSettings::class;
    }

    public function getAttributeValueClass(): string
    {
        return MultipleFilesValue::class;
    }

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();

        if (isset($data['value'])) {
            return $this->createAttributeValue($data['value']);
        }

        return $this->createAttributeValue();
    }

    protected function load(): void
    {
        $attributeKey = $this->getAttributeKey();

        if (!is_object($this->attributeKey)) {
            return;
        }

        $this->akMaxFilesCount = $attributeKey->getAttributeKeySettings()->getMaxFilesCount();

        $this->set('attributeKey', $attributeKey);
        $this->set('akMaxFilesCount', $this->akMaxFilesCount);
    }

    public function type_form()
    {
        $this->load();
    }

    /**
     * @param array $data
     * @return MultipleFilesSettings
     */
    public function saveKey($data): MultipleFilesSettings
    {
        /** @var MultipleFilesSettings $type */
        $type = $this->getAttributeKeySettings();
        $type->setMaxFilesCount((int)$data['akMaxFilesCount']);
        return $type;
    }

    public function importValue(SimpleXMLElement $item)
    {
        $files = [];

        foreach ($item->children() as $fileItem) {
            $fIDVal = (string)$fileItem;
            /** @var ValueInspector $valueInspector */
            $inspector = $this->app->make('import/value_inspector');
            $result = $inspector->inspect($fIDVal);
            $files[] = $result->getReplacedValue();
        }

        return $this->createAttributeValue($files);
    }

    public function importKey(SimpleXMLElement $element): MultipleFilesSettings
    {
        /** @var MultipleFilesSettings $type */
        $type = $this->getAttributeKeySettings();

        if (isset($element->type)) {
            $type->setMaxFilesCount((int)$element->type['maxfiles']);
        }

        return $type;
    }

    public function validateKey($data = []): ErrorList
    {
        /** @var Validation $formValidator */
        $formValidator = $this->app->make(Validation::class);
        $formValidator->setData($data);
        $formValidator->addRequired("akMaxFilesCount", "You need to have at least one file field.");
        $formValidator->test();
        return $formValidator->getError();
    }

    public function validateForm($data)
    {
        if (!isset($this->akMaxFilesCount)) {
            $this->load();
        }

        if (!is_array($data['value'])) {
            return new FieldNotPresentError(new AttributeField($this->getAttributeKey()));
        } else if ($this->akMaxFilesCount > 0 && count($data['value']) > $this->akMaxFilesCount) {
            return new Error(t('Limit of files is exceeded for %s', $this->getAttributeKey()->getAttributeKeyDisplayName()),
                new AttributeField($this->getAttributeKey())
            );
        }

        return true;
    }

    public function createAttributeValue($mixed = null): MultipleFilesValue
    {
        $attributeValue = new MultipleFilesValue();

        if (is_array($mixed) && count($mixed) > 0) {
            foreach ($mixed as $fileId) {
                $file = File::getByID($fileId);

                if ($file instanceof FileEntity) {
                    $attributeValueFile = new MultipleFilesSelectedFiles();
                    $attributeValueFile->setFile($file);
                    $attributeValueFile->setAttributeValue($attributeValue);
                    $attributeValue->getSelectedFiles()->add($attributeValueFile);
                }
            }
        }

        return $attributeValue;
    }

    public function createAttributeKeySettings(): MultipleFilesSettings
    {
        return new MultipleFilesSettings();
    }

    public function getDisplayValue(): string
    {
        $ul = new Element("ul");

        $currentFilesValue = $this->attributeValue->getValue();

        if (is_object($currentFilesValue)) {
            /** @var FileEntity[] $currentFiles */
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            $currentFiles = $currentFilesValue->getFileObjects();

            if (count($currentFiles) > 0) {
                foreach ($currentFiles as $fileEntity) {
                    $approvedFileVersion = $fileEntity->getApprovedVersion();

                    if ($approvedFileVersion instanceof Version) {
                        $ul->appendChild(
                            new Element(
                                "li",
                                new Element(
                                    "a",
                                    $approvedFileVersion->getFileName(),
                                    [
                                        "href" => $approvedFileVersion->getDownloadURL(),
                                        "target" => "_blank"
                                    ]
                                )
                            )
                        );
                    }
                }
            }
        }
        if (count($ul->getChildren()) > 0) {
            return (string)$ul->render();
        } else {
            return t("No files selected.");
        }
    }

    public function form()
    {
        $this->requireAsset('core/file-manager');

        if (is_object($this->attributeValue)) {
            $currentFilesValue = $this->attributeValue->getValue();

            if ($currentFilesValue) {
                /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                $this->set('currentFiles', $currentFilesValue->getFileObjects());
            }
        }

        $this->load();
    }

    public function exportValue(SimpleXMLElement $akn)
    {
        $currentFilesValue = $this->getAttributeValue()->getValue();

        if (is_object($currentFilesValue)) {
            /** @var FileEntity[] $currentFiles */
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            $currentFiles = $currentFilesValue->getFileObjects();

            if (count($currentFiles) > 0) {
                foreach ($currentFiles as $fileEntity) {
                    $akn->addChild('file', ContentExporter::replaceFileWithPlaceHolder($fileEntity->getFileID()));
                }
            }
        }
    }
}

<?php

namespace Newance\Training\Controller\Adminhtml\Brand;

use Newance\Training\Model\Brand;

/**
 * Training brand save controller
 */
class Save extends \Newance\Training\Controller\Adminhtml\Brand
{
    /**
     * Before model save
     * @param  \Newance\Training\Model\Brand $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _beforeSave($model, $request)
    {
        $imageFields = [
            'banner_img',
            'brand_img'
        ];

        foreach ($imageFields as $imageField) {
            $this->saveImage($model, $imageField);
        }
    }

    /**
     * Save image
     *
     * @param  \Newance\Training\Model\Brand $model
     * @param  string $imageField
     * @return void
     */
    private function saveImage($model, $imageField)
    {
        $fileSystem = $this->_objectManager->create('Magento\Framework\Filesystem');
        $mediaDirectory = $fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

        if (isset($data[$imageField]) && isset($data[$imageField]['value'])) {
            if (isset($data[$imageField]['delete'])) {
                unlink($mediaDirectory->getAbsolutePath() . $data[$imageField]['value']);
                $model->setData($imageField, '');
            } else {
                $model->unsetData($imageField);
            }
        }

        try {
            $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\UploaderFactory');
            $uploader = $uploader->create(['fileId' => 'brand[' . $imageField . ']']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $uploader->setAllowCreateFolders(true);
            $result = $uploader->save(
                $mediaDirectory->getAbsolutePath(Brand::BASE_MEDIA_PATH)
            );
            $model->setData($imageField, Brand::BASE_MEDIA_PATH . $result['file']);
        } catch (\Exception $e) {
            if ($e->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY) {
                throw new \Exception($e->getMessage());
            }
        }
    }
}

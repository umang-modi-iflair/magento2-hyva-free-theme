<?php
namespace Newance\Training\Controller\Adminhtml\Post;

use Newance\Training\Model\Post;

/**
 * Training post save controller
 */
class Save extends \Newance\Training\Controller\Adminhtml\Post
{
    /**
     * Before model save
     * @param  \Newance\Training\Model\Post $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _beforeSave($model, $request)
    {
        /* prepare publish date */
        $dateFilter = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\Filter\Date');
        $data = $model->getData();

        $inputFilter = new \Magento\Framework\Filter\FilterInput(
            ['publish_time' => $dateFilter],
            [],
            $data
        );
        $data = $inputFilter->getUnescaped();
        $model->setData($data);

        /* prepare relative links */
        if ($links = $request->getPost('links')) {
            $jsHelper = $this->_objectManager->create('Magento\Backend\Helper\Js');

            $links = is_array($links) ? $links : [];
            $linkTypes = ['relatedposts', 'relatedproducts'];
            foreach ($linkTypes as $type) {
                if (isset($links[$type])) {
                    $links[$type] = $jsHelper->decodeGridSerializedInput($links[$type]);

                    $model->setData($type . '_links', $links[$type]);
                }
            }
        }

        /* prepare featured image */
        $imageField = 'featured_img';
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
            $uploader = $uploader->create(['fileId' => 'post[' . $imageField . ']']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $uploader->setAllowCreateFolders(true);
            $result = $uploader->save(
                $mediaDirectory->getAbsolutePath(Post::BASE_MEDIA_PATH)
            );
            $model->setData($imageField, Post::BASE_MEDIA_PATH . $result['file']);
        } catch (\Exception $e) {
            if ($e->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY) {
                throw new \Exception($e->getMessage());
            }
        }

        /* prepare home image */
        $homeImageField = 'home_img';

        if (isset($data[$homeImageField]) && isset($data[$homeImageField]['value'])) {
            if (isset($data[$homeImageField]['delete'])) {
                unlink($mediaDirectory->getAbsolutePath() . $data[$homeImageField]['value']);
                $model->setData($homeImageField, '');
            } else {
                $model->unsetData($homeImageField);
            }
        }
        try {
            $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\UploaderFactory');
            $uploader = $uploader->create(['fileId' => 'post[' . $homeImageField . ']']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $uploader->setAllowCreateFolders(true);
            $result = $uploader->save(
                $mediaDirectory->getAbsolutePath(Post::BASE_MEDIA_PATH)
            );
            $model->setData($homeImageField, Post::BASE_MEDIA_PATH . $result['file']);
        } catch (\Exception $e) {
            if ($e->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY) {
                throw new \Exception($e->getMessage());
            }
        }

    }
}

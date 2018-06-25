<?php

namespace Uploader;

use Psr\Http\Message\UploadedFileInterface;
use Uploader\Adapter\AdapterInterface;
use Uploader\Model\UploaderTable;
use Uploader\Result\Download;

class Uploader
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var UploaderTable
     */
    private $uploaderTable;

    /**
     * @var array
     */
    protected $allowedMediaTypes;

    public function __construct(AdapterInterface $adapter, UploaderTable $uploaderTable)
    {
        $this->adapter = $adapter;
        $this->uploaderTable = $uploaderTable;
    }

    /**
     * @param string $mediaType
     *
     * @return Uploader
     */
    public function addAllowedMediaType(string $mediaType): Uploader
    {
        $this->allowedMediaTypes[] = $mediaType;
        return $this;
    }

    /**
     * @param array $mediaTypes
     *
     * @return Uploader
     */
    public function initializeAllowedMediaTypes(array $mediaTypes): Uploader
    {
        $this->allowedMediaTypes = $mediaTypes;
        return $this;
    }

    /**
     * @param string $mediaType
     *
     * @return Uploader
     */
    public function removeAllowedMediaType(string $mediaType): Uploader
    {
        $key = array_search($mediaType, $this->allowedMediaTypes);
        if (false !== $key) {
            unset($this->allowedMediaTypes[$key]);
        }
        return $this;
    }

    /**
     * @return Uploader
     */
    public function unsetAllowedMediaTypes(): Uploader
    {
        $this->allowedMediaTypes = [];
        return $this;
    }

    /**
     * @param UploadedFileInterface $uploadedFile
     *
     * @return int|null
     */
    public function upload(UploadedFileInterface $uploadedFile, $currentUserId): ?int
    {
        if (!in_array($uploadedFile->getClientMediaType(), $this->allowedMediaTypes)) {
            return null;
        }
        $originalName = $uploadedFile->getClientFilename();
        $fileName = $this->applyRandomToFilename($originalName);
        $this->adapter->upload($uploadedFile, $fileName);
        return $this->uploaderTable->insert($fileName, $originalName, $uploadedFile->getClientMediaType(), $uploadedFile->getSize(), $currentUserId);
    }

    /**
     *
     * @param string $fileName
     */
    public function download(string $fileName):Download
    {
        return $this->adapter->download($fileName);
    }

    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        /**
         * @var \Uploader\Model\Uploader $uploader
         */
        $uploader = $this->uploaderTable->fetchById($id)->current();
        if ($uploader) {
            $this->adapter->unlink($uploader->getPath());
            $this->uploaderTable->delete($id);
        }
    }

    public function get(array $ids)
    {
        /**
         * @var \Uploader\Model\Uploader $uploader
         */
        $uploader = $this->uploaderTable->fetchByIds($ids)->toArray();
        return $uploader;
    }

    public function getPath(int $id)
    {
        /**
         * @var \Uploader\Model\Uploader $uploader
         */
        $uploader = $this->uploaderTable->fetchById($id)->current();
        return $uploader->getPath();
    }

    public function getFilePath(int $id)
    {
        return $this->adapter->filePath($this->getPath($id));
    }

    public function getUploadedBy(int $id)
    {
        $uploadedRow = $this->uploaderTable->fetchById($id)->current();
        return $uploadedRow->getUploadedBy() ?? 0;
    }

    public function byKey(string $stringKey)
    {
        $arrayKey = @unserialize(base64_decode($stringKey));
        $id = $arrayKey[0] ?? 0;
        $path = $arrayKey[1] ?? '';
        $size = $arrayKey[2] ?? 0;

        /**
         * @var \Uploader\Model\Uploader $uploader
         */
        if ($id > 0 && ! empty($path) && $size > 0) {
            $uploader = $this->uploaderTable->fetchByIdPathSize($id, $path, $size);
            if ($uploader) {
                return [
                    'uploader' => $uploader,
                    'filePath' => $this->adapter->filePath($uploader->getPath())
                ];
            }
        }
        return null;
    }

    /**
     * @param $fileName
     *
     * @return string
     */
    protected function applyRandomToFilename($fileName)
    {
        return DIRECTORY_SEPARATOR .
            uniqid(sha1($fileName), false) .
            time() .
            strtolower(strrchr($fileName, '.'));
    }
}

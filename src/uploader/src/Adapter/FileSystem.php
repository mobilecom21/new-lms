<?php

namespace Uploader\Adapter;

use Psr\Http\Message\UploadedFileInterface;
use Uploader\Result\Download;

class FileSystem implements AdapterInterface
{
    /**
     * @var string
     */
    protected $targetPath = 'data/media';

    /**
     * @inheritDoc
     */
    public function upload(UploadedFileInterface $uploadedFile, string $fileName): void
    {
        $uploadedFile->moveTo($this->targetPath . $fileName);
    }

    /**
     * @inheritDoc
     */
    public function download(string $fileName): Download
    {
        return new Download(file_exists($this->targetPath . $fileName) ? file_get_contents($this->targetPath . $fileName) : '');
    }

    /**
     * @inheritDoc
     */
    public function unlink(string $fileName): void
    {
        unlink($this->targetPath . $fileName);
    }

    /**
     * @inheritDoc
     */
    public function filePath(string $fileName): string
    {
        return $this->targetPath . $fileName;
    }
}

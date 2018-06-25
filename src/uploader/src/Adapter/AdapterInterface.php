<?php

namespace Uploader\Adapter;

use Psr\Http\Message\UploadedFileInterface;
use Uploader\Result\Download;

interface AdapterInterface
{
    /**
     * @param UploadedFileInterface $uploadedFile
     * @param string $fileName
     */
    public function upload(UploadedFileInterface $uploadedFile, string $fileName): void;

    /**
     * @param string $fileName
     */
    public function download(string $fileName):Download;

    /**
     * @param string $fileName
     */
    public function unlink(string $fileName): void;

    /**
     * @param string $fileName
     * @return string
     */
    public function filePath(string $fileName): string;
}

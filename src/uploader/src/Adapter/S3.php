<?php

namespace Uploader\Adapter;

use Psr\Http\Message\UploadedFileInterface;
use Aws\S3\S3Client;
use Uploader\Result\Download;
use Options\Model\OptionsTable;

class S3 implements AdapterInterface
{
    /**
     * @var S3Client
     */
    private $s3Client;

    /**
     * @var OptionsTable
     */
    private $optionsTable;

    /**
     * @var string
     */
    protected $targetPath = 'data/media';

    /**
     * @var string
     */
    protected $bucket;

    public function __construct(S3Client $s3Client, OptionsTable $optionsTable)
    {
        $this->s3Client = $s3Client;
        $this->optionsTable = $optionsTable;
        $this->bucket = $this->optionsTable->fetchByName('amazon_bucket')['value'] ?? '';
    }

    /**
     * @inheritDoc
     */
    public function upload(UploadedFileInterface $uploadedFile, string $fileName): void
    {
        /*$iterator = $this->s3Client->getIterator('ListObjects', array(
            'Bucket' => $this->bucket
        ));
        foreach ($iterator as $object) {
            echo $object['Key'] . "\n";
            $this->unlink(str_replace('data/media', '', $object['Key']));
        }
        die();*/

        $uploadedFile->moveTo($this->targetPath . $fileName);
        if ('application/zip' != $uploadedFile->getClientMediaType()) {
            try{
                $this->s3Client->putObject(array(
                    'Bucket'     => $this->bucket,
                    'Key'        => $this->targetPath . $fileName,
                    'SourceFile' => $this->targetPath . $fileName,
                ));
            } catch (S3Exception $e) {
                echo $e->getMessage() . "\n";
            }
            //unlink($this->targetPath . $fileName);  //activate this line after some time if all works ok with s3
        }
    }

    /**
     * @inheritDoc
     */
    public function download(string $fileName): Download
    {
        try{
            $s3file = @$this->s3Client->getObject(array(
                'Bucket' => $this->bucket,
                'Key'    => $this->targetPath . $fileName
            ));
        } catch (S3Exception $e) {
            //echo $e->getMessage() . "\n";
        }
        return new Download(! empty($s3file['Body']) ? $s3file['Body'] : '');
    }

    /**
     * @inheritDoc
     */
    public function unlink(string $fileName): void
    {
        try{
            $this->s3Client->deleteObject(array(
                'Bucket' => $this->bucket,
                'Key'    => $this->targetPath . $fileName
            ));
        } catch (S3Exception $e) {
            //echo $e->getMessage() . "\n";
        }
    }

    /**
     * @inheritDoc
     */
    public function filePath(string $fileName): string
    {
        return $this->targetPath . $fileName;
    }
}

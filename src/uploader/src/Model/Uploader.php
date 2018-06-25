<?php

namespace Uploader\Model;

class Uploader
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $size;

    /**
     * @var int
     */
    protected $uploadedBy;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Uploader
     */
    public function setId(int $id): Uploader
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return Uploader
     */
    public function setPath(string $path): Uploader
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Uploader
     */
    public function setName(string $name): Uploader
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Uploader
     */
    public function setType(string $type): Uploader
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @param string $size
     *
     * @return Uploader
     */
    public function setSize(string $size): Uploader
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getUploadedBy(): ?int
    {
        return $this->uploadedBy;
    }

    /**
     * @param int $uploadedBy
     *
     * @return Uploader
     */
    public function setUploadedBy(?int $uploadedBy): Uploader
    {
        $this->uploadedBy = $uploadedBy;
        return $this;
    }

    public function getUniqueKey()
    {
        return $this->makeUniqueKey($this->getId(), $this->getPath(), $this->getSize());
    }

    public function makeUniqueKey(int $id, string $path, int $size)
    {
        return base64_encode(serialize([$id, $path, $size]));
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}

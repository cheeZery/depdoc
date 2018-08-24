<?php
declare(strict_types=1);

namespace DepDoc\Configuration;

class ConfigurationFileDefinition
{
    /** @var string */
    protected $filename;
    /** @var string */
    protected $format;

    /**
     * @param string $filename
     * @param string $format
     */
    public function __construct(string $filename, string $format)
    {
        $this->filename = $filename;
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return $this
     */
    public function setFilename(string $filename): ConfigurationFileDefinition
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     * @return $this
     */
    public function setFormat(string $format): ConfigurationFileDefinition
    {
        $this->format = $format;

        return $this;
    }
}

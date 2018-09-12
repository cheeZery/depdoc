<?php
declare(strict_types=1);

namespace DepDoc\Writer;

/**
 * @codeCoverageIgnore
 */
class WriterConfiguration
{
    /** @var string */
    protected $newline = PHP_EOL;
    /** @var boolean */
    protected $exportExternalLink = true;

    /**
     * @param null|string $newline
     * @param bool|null $exportExternalLink
     */
    public function __construct(?string $newline = null, ?bool $exportExternalLink = null)
    {
        if ($newline !== null) {
            $this->newline = $newline;
        }
        if ($exportExternalLink !== null) {
            $this->exportExternalLink = $exportExternalLink;
        }
    }

    /**
     * @return string
     */
    public function getNewline(): string
    {
        return $this->newline;
    }

    /**
     * @param string $newline
     * @return $this
     */
    public function setNewline(string $newline): WriterConfiguration
    {
        $this->newline = $newline;

        return $this;
    }

    /**
     * @return bool
     */
    public function isExportExternalLink(): bool
    {
        return $this->exportExternalLink;
    }

    /**
     * @param bool $exportExternalLink
     * @return $this
     */
    public function setExportExternalLink(bool $exportExternalLink): WriterConfiguration
    {
        $this->exportExternalLink = $exportExternalLink;

        return $this;
    }
}

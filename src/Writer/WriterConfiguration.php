<?php
declare(strict_types=1);

namespace DepDoc\Writer;

class WriterConfiguration
{
    /** @var string */
    protected $newline;
    /** @var boolean */
    protected $exportExternalLink;

    /**
     * @param string $newline
     * @param bool $exportExternalLink
     */
    public function __construct(string $newline, bool $exportExternalLink)
    {
        $this->newline = $newline;
        $this->exportExternalLink = $exportExternalLink;
    }

    /**
     * @return string
     */
    public function getNewline(): string
    {
        return $this->newline;
    }

    /**
     * @return bool
     */
    public function isExportExternalLink(): bool
    {
        return $this->exportExternalLink;
    }
}

<?php
declare(strict_types=1);

namespace DepDoc\Configuration;

class ApplicationConfiguration
{
    /** @var string */
    protected $newlineCharacter;
    /** @var string */
    protected $lockSymbol;
    /** @var boolean */
    protected $exportExternalLink;

    /**
     * @return string
     */
    public function getNewlineCharacter(): string
    {
        return $this->newlineCharacter;
    }

    /**
     * @param string $newlineCharacter
     * @return $this
     */
    public function setNewlineCharacter(string $newlineCharacter): ApplicationConfiguration
    {
        $this->newlineCharacter = $newlineCharacter;

        return $this;
    }

    /**
     * @return string
     */
    public function getLockSymbol(): string
    {
        return $this->lockSymbol;
    }

    /**
     * @param string $lockSymbol
     * @return $this
     */
    public function setLockSymbol(string $lockSymbol): ApplicationConfiguration
    {
        $this->lockSymbol = $lockSymbol;

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
    public function setExportExternalLink(bool $exportExternalLink): ApplicationConfiguration
    {
        $this->exportExternalLink = $exportExternalLink;

        return $this;
    }
}

<?php
declare(strict_types=1);

namespace DepDoc\Configuration;

/**
 * @codeCoverageIgnore
 */
class ApplicationConfiguration
{
    /** @var string[] */
    public const ALLOWED_LOCK_SYMBOLS = [
        '🔒',
        '🛇',
        '⚠',
        '✋',
    ];

    /** @var string */
    protected $newlineCharacter = PHP_EOL;
    /** @var string */
    protected $lockSymbol = '⚠';
    /** @var boolean */
    protected $exportExternalLink = true;
    /** @var boolean */
    protected $composer = true;
    /** @var boolean */
    protected $npm = true;

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

    /**
     * @return bool
     */
    public function isComposer(): bool
    {
        return $this->composer;
    }

    /**
     * @param bool $composer
     *
     * @return self
     */
    public function setComposer(bool $composer): self
    {
        $this->composer = $composer;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNpm(): bool
    {
        return $this->npm;
    }

    /**
     * @param bool $npm
     *
     * @return self
     */
    public function setNpm(bool $npm): self
    {
        $this->npm = $npm;

        return $this;
    }
}

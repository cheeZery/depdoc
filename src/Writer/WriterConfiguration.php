<?php
declare(strict_types=1);

namespace DepDoc\Writer;

class WriterConfiguration
{
    /** @var string */
    protected $newline;

    /**
     * @param string $newline
     */
    public function __construct(string $newline)
    {
        $this->newline = $newline;
    }

    /**
     * @return string
     */
    public function getNewline(): string
    {
        return $this->newline;
    }
}

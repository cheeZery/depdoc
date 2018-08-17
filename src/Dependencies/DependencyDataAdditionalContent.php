<?php

namespace DepDoc\Dependencies;

class DependencyDataAdditionalContent
{
    /** @var string[] */
    protected $lines = [];

    /**
     * @param string[] $lines
     */
    public function __construct(array $lines = [])
    {
        $this->lines = $lines;
    }


    public function getAll(): array
    {
        return $this->lines;
    }

    /**
     * @param string $line
     * @return DependencyDataAdditionalContent
     */
    public function add(string $line): DependencyDataAdditionalContent
    {
        $this->lines[] = $line;

        return $this;
    }

    /**
     * @param int $index
     * @return DependencyDataAdditionalContent
     */
    public function removeIndex(int $index): DependencyDataAdditionalContent
    {
        unset($this->lines[$index]);

        return $this;
    }

    /**
     * @return DependencyDataAdditionalContent
     */
    public function removeLasEmptyLine(): DependencyDataAdditionalContent
    {
        if (end($this->lines) === '') {
            array_pop($this->lines);
        }

        return $this;
    }
}

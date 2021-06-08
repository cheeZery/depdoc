<?php
declare(strict_types=1);

namespace DepDoc\Dependencies;

/**
 * @codeCoverageIgnore
 */
class DependencyDataAdditionalContent
{
    /** @var string[] */
    protected array $lines = [];

    /**
     * @param string[] $lines
     */
    public function __construct(array $lines = [])
    {
        $this->lines = $lines;
    }


    /**
     * @return string[]
     */
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
     * @param int $index
     *
     * @return string|null
     */
    public function getPreviousLine(int $index): ?string
    {
        $lines = $this->lines;
        while (key($lines) !== $index) {
            next($lines);
        }

        $previousValue = prev($lines);

        return $previousValue === false ? null : $previousValue;
    }

    /**
     * @return DependencyDataAdditionalContent
     */
    public function removeLastEmptyLine(): DependencyDataAdditionalContent
    {
        if (end($this->lines) === '') {
            array_pop($this->lines);
        }

        return $this;
    }
}

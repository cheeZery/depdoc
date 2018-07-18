<?php
declare(strict_types=1);

namespace DepDoc;

class Runner
{
    private const ACTIONS = ['validate', 'update'];

    /** @var Application|null */
    private $application;

    public function run(array $arguments = []): int
    {
        if (count($arguments) && ($arguments[0] === '-h' || $arguments[0] === '--help')) {
            echo static::getHelp();

            return 0;
        }

        $action = $arguments[0] ?? self::ACTIONS[0];

        if (!in_array($action, self::ACTIONS)) {
            echo "Unrecognized action '$action'!" . PHP_EOL . PHP_EOL;
            echo static::getHelp();

            return 1;
        }

        $actionName = "{$action}Action";

        if (!$this->getApplication()->$actionName()) {
            return 1;
        }

        return 0;
    }

    public function getHelp(): string
    {
        return <<<HELP
Usage:
  depdoc [command] [options]

Commands:
  validate      Validate a already generated DEPENDENCIES.md
  update        Update or create a DEPENDENCIES.md

Options:
  --help        Output this help 

HELP;

    }

    /**
     * @return Application|null
     */
    public function getApplication(): ?Application
    {
        if (!$this->application) {
            $this->application = new Application();
        }

        return $this->application;
    }

    /**
     * @param Application $application
     * @return $this
     */
    public function setApplication(?Application $application): Runner
    {
        $this->application = $application;

        return $this;
    }
}

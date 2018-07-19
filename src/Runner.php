<?php
declare(strict_types=1);

namespace DepDoc;

class Runner
{
    private const ACTIONS = ['validate', 'update'];

    /** @var Application */
    protected $application;

    /**
     * @param Application|null $application
     */
    public function __construct(?Application $application = null)
    {
        $this->application = $application ?? new Application();
    }


    public function run(array $arguments = []): int
    {
        if (count($arguments) && (in_array('-h', $arguments) || in_array('--help', $arguments))) {
            echo static::getHelp();

            return 0;
        }

        list($action, $options) = $this->parseArguments($arguments);
        if (!$action) {
            echo static::getHelp();

            return 0;
        }

        if (!in_array($action, self::ACTIONS)) {
            echo "Unrecognized action '$action'!" . PHP_EOL . PHP_EOL;
            echo static::getHelp();

            return 1;
        }

        $actionName = "{$action}Action";

        if (!$this->getApplication()->$actionName($options)) {
            return 1;
        }

        return 0;
    }

    protected function parseArguments(array $arguments): array
    {
        $parsedAction = null;
        $parsedOptions = [
            'targetDirectory' => getcwd(),
        ];

        while (count($arguments)) {
            $argument = trim(array_shift($arguments));
            if (strlen($argument) === 0) {
                continue;
            }

            if ($argument === '-d' && count($arguments)) {
                $parsedOptions['targetDirectory'] = array_shift($arguments);
                continue;
            }
            if ($argument[0] !== '-') {
                $parsedAction = $argument;
                continue;
            }
        }

        return [$parsedAction, $parsedOptions];
    }

    protected function getHelp(): string
    {
        return <<<HELP
Usage:
  depdoc [command] [options]

Commands:
  validate        Validate a already generated DEPENDENCIES.md
  update          Update or create a DEPENDENCIES.md

Options:
  -h|--help       Output this help 
  -d|--directory  Execution directory to look for the DEPENDENCIES.md file

HELP;

    }

    /**
     * @return Application
     */
    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * @param Application $application
     * @return $this
     */
    public function setApplication(Application $application): Runner
    {
        $this->application = $application;

        return $this;
    }
}

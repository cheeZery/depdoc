<?php
declare(strict_types=1);

namespace DepDoc\Application;

use DepDoc\Command\UpdateCommand;
use DepDoc\Command\ValidateCommand;
use Symfony\Component\Console\Application;

class DepDocApplication extends Application
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct('DepDoc', '1.0');
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultCommands(): array
    {
        return array_merge(parent::getDefaultCommands(), [
            new ValidateCommand(),
            new UpdateCommand(),
        ]);
    }
}

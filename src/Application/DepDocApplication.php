<?php
declare(strict_types=1);

namespace DepDoc\Application;

use Symfony\Component\Console\Application;

class DepDocApplication extends Application
{
    public function __construct()
    {
        parent::__construct('DepDoc', '1.0');
    }

    protected function getDefaultCommands(): array
    {
        return array_merge(parent::getDefaultCommands(), [

        ]);
    }
}

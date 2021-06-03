<?php
declare(strict_types=1);

namespace DepDoc;

class Runner
{
    private const ACTIONS = ['validate', 'update'];

    public static function run(array $arguments = [])
    {
        $action = $arguments[0] ?? self::ACTIONS[0];

        if (!in_array($action, self::ACTIONS)) {
            echo "Unrecognized action '$action'!";
            exit(1);
        }

        $app = new Application();

        $actionName = "{$action}Action";

        $app->$actionName();
    }
}

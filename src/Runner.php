<?php

namespace DepDoc;

// TODO: Add PHP 7.1 as requirement in composer.json

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
<?php
declare(strict_types=1);

namespace DepDoc\Helper;

use JsonException;

class CliCommandHelper {

    public function runAndGetOutputAsJson(string $command, string $contextName): array {

        $fullCommand = sprintf('%s 2> /dev/null', escapeshellcmd($command));
        exec($fullCommand, $output);

        if (count($output) === 0) {
            return [];
        }

        // Skip until first valid json line
        if ($output[0] !== '{') {
            do {
                array_shift($output);
            } while (count($output) > 0 && trim($output[0]) !== '{');
        }

        if (count($output) === 0) {
            return [];
        }

        try {
            $jsonOutput = json_decode(implode("", $output), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            echo sprintf(
                'Error occurred while trying to read "%s" dependencies: %s (%s)' . PHP_EOL,
                $contextName,
                $exception->getMessage(),
                $exception->getCode()
            );
            exit(1);
        }

        return $jsonOutput;
    }

}

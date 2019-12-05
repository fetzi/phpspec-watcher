<?php

namespace Fetzi\PhpspecWatcher;

use Fetzi\PhpspecWatcher\Exceptions\InvalidConfigurationException;
use Symfony\Component\Yaml\Yaml;

class Configuration
{
    private const CONFIGURATION_FILE = '.phpspec-watcher.yml';

    public static function load(): array
    {
        $config = [];

        if (self::exists()) {
            $config = Yaml::parse(file_get_contents(self::getConfigPath()));
        }

        $config = self::mergeDefaults($config);
        $config = self::makeAbsolutePaths($config);

        self::validate($config);

        return $config;
    }

    public static function exists(): bool
    {
        return file_exists(self::getConfigPath());
    }

    public static function initialize()
    {
        file_put_contents(self::getConfigPath(), Yaml::dump(self::mergeDefaults([])));
    }

    public static function getConfigPath(): string
    {
        return getcwd() . '/' . self::CONFIGURATION_FILE;
    }

    private static function makeAbsolutePaths($config): array
    {
        $directories = array_map(
            function ($item) {
                return getcwd() . '/' . $item;
            },
            $config['directories']
        );

        $config['directories'] = array_filter($directories, function ($item) {
            return file_exists($item);
        });

        return $config;
    }

    private static function mergeDefaults($config): array
    {
        $defaults = [
            'fileMask'      => '*.php',
            'checkInterval' => 1,
            'directories'   => [
                'app',
                'src',
                'spec',
            ],
            'phpspec' => [
                'binary'    => 'vendor/bin/phpspec',
                'arguments' => [
                    'format=dot',
                ],
            ],
            'notifications' => [
                'onError'   => true,
                'onSuccess' => true,
            ],
        ];

        return $config + $defaults;
    }

    private static function validate($config)
    {
        if (!file_exists($config['phpspec']['binary'])) {
            throw new InvalidConfigurationException(
                sprintf(
                    'phpspec binary cannot be found in %s',
                    $config['phpspec']['binary']
                )
            );
        }

        if (empty($config['directories'])) {
            throw new InvalidConfigurationException(
                'No watch directory available'
            );
        }
    }
}

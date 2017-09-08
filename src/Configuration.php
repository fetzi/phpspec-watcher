<?php

namespace Fetzi\PhpspecWatcher;

use Fetzi\PhpspecWatcher\Exceptions\InvalidConfigurationException;
use Symfony\Component\Yaml\Yaml;

class Configuration
{
    const CONFIGURATION_FILE = '.phpspec-watcher.yml';

    public static function load()
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

    public static function exists()
    {
        return file_exists(self::getConfigPath());
    }

    public static function initialize()
    {
        file_put_contents(self::getConfigPath(), Yaml::dump(self::mergeDefaults([])));
    }

    public static function getConfigPath()
    {
        return getcwd().'/'.self::CONFIGURATION_FILE;
    }

    private static function makeAbsolutePaths($config)
    {
        $directories = array_map(
            function ($item) {
                return getcwd().'/'.$item;
            },
            $config['directories']
        );

        $config['directories'] = array_filter($directories, function ($item) {
            return file_exists($item);
        });

        return $config;
    }

    private static function mergeDefaults($config)
    {
        $defaults = [
            'fileMask'      => '*.php',
            'checkInterval' => 1.0,
            'directories'   => [
                'app',
                'src',
                'tests',
            ],
            'phpspecBinary' => 'vendor/bin/phpspec',
            'notifications' => [
                'onError'   => true,
                'onSuccess' => false,
            ],
        ];

        return $config + $defaults;
    }

    private static function validate($config)
    {
        if (!file_exists($config['phpspecBinary'])) {
            throw new InvalidConfigurationException(
                sprintf(
                    'phpspec binary cannot be found in %s',
                    $config['phpspecBinary']
                )
            );
        }
    }
}

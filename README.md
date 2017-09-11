# phpspec Watcher

[![StyleCI](https://styleci.io/repos/102859380/shield?branch=master)](https://styleci.io/repos/102859380)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/fe3f8dba-15da-4527-a333-1a392d10673d.svg?style=flat-square)](https://insight.sensiolabs.com/projects/fe3f8dba-15da-4527-a333-1a392d10673d)
[![Code Climate](https://img.shields.io/codeclimate/github/fetzi/phpspec-watcher.svg?style=flat-square)](https://codeclimate.com/github/fetzi/phpspec-watcher)


## Introduction
phpspec-watcher is a tool for automating phpspec test execution when the source code of a project changes. On file save the watcher automatically triggers the phpspec test suite and informs the developer about the test results.

## Installation
This tool can be installed globally with

```
composer global require fetzi/phpspec-watcher
```

or just for one package as dev-dependency

```
composer require fetzi/phpspec-watcher --dev
```

## Usage
After a global installation you can invoke the watcher by calling

```
phpspec-watcher watch
```

If you have installed the watcher as a dev-dependency for one project you can find the binary within the vendor bin directory.

```
vendor/bin/phpspec-watcher watch
```

## Configuration
The watcher can be configured with a configuration file `.phpspec-watcher.yml` stored in the project root directory.

The following listing shows the file structure and the option meanings:

```yml
fileMask: '*.php'                   # file pattern that should be watched

checkInterval: 1                    # a float value indicating the resource check interval

directories:                        # a list of directories that should be watched
    - app
    - src
    - spec
    
phpspec:
    binary: vendor/bin/phpspec      # path to the phpspec binary
    arguments: [format=dot]         # additional phpspec arguments

notifications:                      # flags for notfications on success and on error
    onError: true
    onSuccess: true

```

To speed up the configuration thing there is an initialization command to bootstrap the config file with default values:

```
phpspec-watcher init
```

## Notifications
The watcher triggers operating system notifications after executing the test suite.

To display the notification icons on Mac you need to install another notifier because AppleScript cannot display custom icons

```
brew install terminal-notifier
```

## Credits
The idea for creating this package was born after discovering the awesome [phpunit-watcher](https://github.com/spatie/phpunit-watcher) package by [Spatie](https://spatie.be).

## License

The MIT License (MIT). Please see the [License File](LICENSE) for more information.

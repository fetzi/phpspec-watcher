<?php

namespace Fetzi\PhpspecWatcher;

use Symfony\Component\Finder\Finder;

class FileWatcher
{
    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var array
     */
    private $oldState = [];

    /**
     * @var array
     */
    private $newState = [];

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;

        $this->computeState();
        $this->oldState = $this->newState;
    }

    /**
     * checks if the given finder instance has file changes
     *
     * @return bool
     */
    public function hasChanges(): bool
    {
        $this->computeState();

        return !empty(array_diff($this->oldState, $this->newState)) ||
            !empty(array_diff($this->newState, $this->oldState));
    }

    /**
     * compute the checksum for all existing files and store the relative
     * pathname and the checksum in the new state
     */
    private function computeState(): void
    {
        $state = [];
        foreach ($this->finder->files() as $file) {
            $state[$file->getRelativePathname()] = md5_file($file->getPathname());
        }

        $this->oldState = $this->newState;
        $this->newState = $state;
    }
}

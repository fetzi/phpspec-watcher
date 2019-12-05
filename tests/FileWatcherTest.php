<?php

namespace Fetzi\PhpspecWatcher\Tests;

use Fetzi\PhpspecWatcher\FileWatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

class FileWatcherTest extends TestCase
{
    const STUBS_DIR = __DIR__ . '/stubs';

    /**
     * @var FileWatcher
     */
    private $fileWatcher;

    public function setUp(): void
    {
        $this->cleanUp();

        file_put_contents(static::STUBS_DIR . '/foo.txt', 'abc');

        $finder = new Finder();
        $finder
            ->files()
            ->in(static::STUBS_DIR)
            ->name('*');

        $this->fileWatcher = new FileWatcher($finder);
    }

    public function testFilesHaveNoChanges()
    {
        $this->assertFalse($this->fileWatcher->hasChanges());
    }

    public function testFileContentHasChanged()
    {
        file_put_contents(static::STUBS_DIR . '/foo.txt', 'abcdefghi');
        $this->assertTrue($this->fileWatcher->hasChanges());
    }

    public function testNewFileWasAdded()
    {
        file_put_contents(static::STUBS_DIR . '/foo2.txt', 'test');
        $this->assertTrue($this->fileWatcher->hasChanges());
    }

    public function testDeletedFile()
    {
        file_put_contents(static::STUBS_DIR . '/foo2.txt', 'test');
        $this->fileWatcher->hasChanges();

        unlink(static::STUBS_DIR . '/foo.txt');
        $this->assertTrue($this->fileWatcher->hasChanges());
    }

    private function cleanUp()
    {
        $files = glob(static::STUBS_DIR . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}

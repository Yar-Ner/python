<?php


namespace App\Application\Commands;


use Symfony\Component\Console\Exception\LogicException;

trait FileLockableTrait
{
    private $lockDir = __DIR__."/../../../logs/";
    private $lock = null;
    private $deleter;

    private function lock(): bool
    {
        $lockFile = str_replace('\\', '_', sprintf('%s.lock',static::class));
        $lockFilePatch = sprintf('%s%s', $this->lockDir, $lockFile);

        if (null !== $this->lock) {
            throw new LogicException('A lock is already in place.');
        }

        if (file_exists($lockFilePatch)) {
            return false;
        }

        if (touch($lockFilePatch)) {
            $this->lock = $lockFilePatch;

            //todo: Create terminatorService and use if it will be needed
            $this->deleter = static function () use ($lockFilePatch) {
                if (file_exists($lockFilePatch)) {
                    unlink($lockFilePatch);
                }
            };

            register_shutdown_function($this->deleter);

            return true;
        }

        return false;
    }

    private function release(): void
    {
        if ($this->lock) {
            unlink($this->lock);
            $this->lock = null;
        }
    }

    public function __destruct()
    {
        if (is_callable($this->deleter)) {
            ($this->deleter)();
        }
    }
}

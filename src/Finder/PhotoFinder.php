<?php

declare(strict_types=1);

namespace App\Finder;

use App\Transformer\FileTransformer;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class PhotoFinder
{
    /**
     * photoDir
     *
     * @var string
     */
    private $photoDir;

    /**
     * fileTransformer
     *
     * @var FileTransformer
     */
    private $fileTransformer;

    /**
     * __construct
     *
     * @param string $photoDir
     * @param FileTransformer $fileTransformer
     */
    public function __construct($photoDir, FileTransformer $fileTransformer)
    {
        $this->photoDir = $photoDir;
        $this->fileTransformer = $fileTransformer;
    }

    /**
     * find
     *
     * @return array
     */
    public function find(?OutputInterface $output): array
    {
        $absolutePath = $this->photoDir;

        $finder = new Finder();
        $finder
            ->in($absolutePath)
            ->sortByType()
        ;

        if ($output) {
            $progress = new ProgressBar($output, count($finder));
            $progress->start();
        }

        $files = [];
        foreach ($finder as $file) {
            $files[] = $this->treatFile($file);
            if ($output) {
                $progress->advance();
            }
        }
        if ($output) {
            $progress->finish();
        }

        return $files;
    }

    /**
     * treatFile
     *
     * @param File $file
     * @param bool $recursive
     *
     * @return File
     */
    private function treatFile(SplFileInfo $file)
    {
        if ($file->isDir()) {
            return $this->fileTransformer->transformToDirectory($file);
        } elseif ($file->isFile()) {
            return $this->fileTransformer->transformToFile($file);
        }
    }
}

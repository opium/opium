<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Directory;
use App\Entity\File;
use App\Entity\Photo;
use App\Finder\PhotoFinder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateCommand extends Command
{
    private $entityManager;

    private $photoFinder;

    public function __construct(EntityManagerInterface $entityManager, PhotoFinder $photoFinder)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->photoFinder = $photoFinder;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('opium:populate')
            ->addOption('truncate', null, InputOption::VALUE_NONE, 'Truncate table before re-inserting photos')
            ->setDescription('Populate database from files')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileRepo = $this->entityManager->getRepository(File::class);
        $dirRepo = $this->entityManager->getRepository(Directory::class);
        $photoRepo = $this->entityManager->getRepository(Photo::class);

        if ($input->getOption('truncate')) {
            $this->truncateTables('file');
            $output->writeln('table truncated');
        }

        $root = new Directory();
        $root->setPathname('')
            ->setName('');
        if (!$fileRepo->findOneByPathname('')) {
            $this->entityManager->persist($root);
        }

        $fileList = $this->photoFinder->find($output);

        foreach ($fileList as $file) {
            $entity = $fileRepo->findOneByPathname($file->getPathname());
            if ($entity) {
                if ($file instanceof Directory && $file->getDirectoryThumbnail()) {
                    $entity->setDirectoryThumbnail($file->getDirectoryThumbnail());
                }
            } else {
                $entity = $file;
                $this->entityManager->persist($entity);
            }
        }

        $this->entityManager->flush();

        $output->writeln('update parent');
        $fileList = $fileRepo->findAll();

        // update parent
        foreach ($fileList as $file) {
            $parentPath = $this->getParentPath($file);
            $dir = $dirRepo->findOneByPathname($parentPath);
            if ($dir && $dir != $file) {
                $file->setParent($dir);
            }
        }
        $this->entityManager->flush();

        $output->writeln('=== update cover ===');

        // update photo
        $dirList = $dirRepo->findAll();
        foreach ($dirList as $dir) {
            $photo = $photoRepo->findOneBy(['parent' => $dir, 'displayable' => true]);
            if ($photo) {
                $output->writeln($dir->getSlug());
                $dir->setDirectoryThumbnail($photo);

                $parent = $dir->getParent();

                while ($parent) {
                    $parent->setDirectoryThumbnail($photo);
                    $parent = $parent->getParent();
                }
            }
        }

        $this->entityManager->flush();
    }

    /**
     * truncateTables
     */
    private function truncateTables($tableNames = [], $cascade = false)
    {
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
        if (is_string($tableNames)) {
            $tableNames = [$tableNames];
        }
        foreach ($tableNames as $name) {
            $connection->executeUpdate($platform->getTruncateTableSQL($name, $cascade));
        }
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
    }

    /**
     * getParentPath
     *
     * @param File $file
     *
     * @return string parent path
     */
    private function getParentPath(File $file): string
    {
        $path = $file->getPathname();
        if (strlen($path) < 2) {
            return '';
        }

        $lastSlashPos = strrpos($path, '/', -2);

        if (false === $lastSlashPos) {
            return '';
        }

        return substr($path, 0, $lastSlashPos);
    }
}
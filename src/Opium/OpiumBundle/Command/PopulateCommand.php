<?php

namespace Opium\OpiumBundle\Command;

use Opium\OpiumBundle\Entity\Directory;
use Opium\OpiumBundle\Entity\File;
use Opium\OpiumBundle\Entity\Photo;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateCommand extends ContainerAwareCommand
{
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
        $em = $this->getContainer()->get('doctrine.orm.opium_entity_manager');
        $repo = $this->getContainer()->get('opium.repository.file');
        $dirRepo = $this->getContainer()->get('opium.repository.directory');
        $photoRepo = $this->getContainer()->get('opium.repository.photo');

        if ($input->getOption('truncate')) {
            $this->truncateTables('file');
            $output->writeln('table truncated');
        }

        $root = new Directory();
        $root->setPathname('')
            ->setName('');
        if (!$repo->findOneByPathname('')) {
            $em->persist($root);
        }

        $fileList = $this->getContainer()->get('opium.finder.photo')->find($output);

        foreach ($fileList as $file) {
            $entity = $repo->findOneByPathname($file->getPathname());
            if ($entity) {
                if ($file instanceof Directory && $file->getDirectoryThumbnail()) {
                    $entity->setDirectoryThumbnail($file->getDirectoryThumbnail());
                }

            } else {
                $entity = $file;
                $em->persist($entity);
            }
        }

        $em->flush();

        $output->writeln('update parent');
        $fileList = $repo->findAll();

        // update parent
        foreach ($fileList as $file) {
            $parentPath = $this->getParentPath($file);
            $dir = $dirRepo->findOneByPathname($parentPath);
            if ($dir && $dir != $file) {
                $file->setParent($dir);
            }
        }
        $em->flush();

        $output->writeln('update cover');

        // update photo
        $dirList = $dirRepo->findAll();
        foreach ($dirList as $dir) {
            $photo = $photoRepo->findOneBy(['parent' => $dir, 'displayable' => true]);
            if ($photo) {
                $dir->setDirectoryThumbnail($photo);
            }
        }

        $em->flush();

    }

    /**
     * getParentPath
     *
     * @param File $file
     * @access private
     * @return string parent path
     */
    private function getParentPath(File $file)
    {
        $path = $file->getPathname();
        if (strlen($path) < 2) {
            return '';
        }

        $parentPath = substr($path, 0, strrpos($path, '/', -2));

        return $parentPath;
    }

    /**
     * truncateTables
     *
     * @param bool $tableNames
     * @param bool $cascade
     * @access public
     * @return void
     */
    public function truncateTables($tableNames = array(), $cascade = false)
    {
        $em = $this->getContainer()->get('doctrine.orm.opium_entity_manager');
        $connection = $em->getConnection();
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
}

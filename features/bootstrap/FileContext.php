<?php

declare(strict_types=1);

use App\Entity\Directory;
use App\Entity\Photo;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
class FileContext implements Context
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Then there should be :nb photo(s)
     */
    public function thereShouldBeFiles($nb): void
    {
        $nbPhotos = $this->countFile(Photo::class);

        if ($nbPhotos !== $nb) {
            throw new \RuntimeException(sprintf('There should be %d photos, %d found.', $nb, $nbPhotos));
        }
    }

    /**
     * @Then there shoud be :nb directories
     * @Then there shoud be :nb directory
     */
    public function thereShoudBeDirectories($nb): void
    {
        $nbDirs = $this->countFile(Directory::class);

        if ($nbDirs !== $nb) {
            throw new \RuntimeException(sprintf('There should be %d directories, %d found.', $nb, $nbDirs));
        }
    }

    private function countFile(string $entityName): int
    {
        return (int) $this->entityManager
            ->getRepository($entityName)
            ->createQueryBuilder('f')
            ->select('COUNT(f)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}

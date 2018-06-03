<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Directory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DirectoryController
{
    /**
     * getMeAction
     *
     * @Route("/directories")
     */
    public function getDirectoriesAction(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $repo = $entityManager->getRepository(Directory::class);
        $rootDir = $repo->findOneBySlug('');

        return new Response($serializer->serialize($rootDir, 'json'));
    }
}

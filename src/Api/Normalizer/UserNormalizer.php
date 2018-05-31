<?php

namespace App\Api\Normalizer;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface
{
    public function  normalize($object, $format = null, array $context = [])
    {
        return [
            'type' => 'user',
            'username' => $object->getUsername(),
            'roles' => $object->getRoles(),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof UserInterface;
    }
}

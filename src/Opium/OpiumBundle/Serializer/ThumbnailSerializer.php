<?php

namespace Opium\OpiumBundle\Serializer;

use JMS\Serializer\EventDispatcher\ObjectEvent;
use Symfony\Component\Routing\RouterInterface;

class ThumbnailSerializer
{
    /**
     * router
     *
     * @var RouterInterface
     * @access private
     */
    private $router;

    /**
     * __construct
     *
     * @param RouterInterface $router
     * @access public
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onSerializerPostSerialize(ObjectEvent $event)
    {
        //ldd($event->getVisitor(), $event->getVisitor()->getNavigator());
        $photo = $event->getObject();

        $thumbnails = [
            'square-200x200' => $this->router->generate(
                'image_crop',
                [ 'slug' => $photo->getSlug(), 'width' => 200, 'height' => 200 ],
                true
            ),

            'banner-1170x400' => $this->router->generate(
                'image_crop',
                [ 'slug' => $photo->getSlug(), 'width' => 1170, 'height' => 400 ],
                true
            ),
        ];

        $event->getVisitor()->addData('thumbnails', $thumbnails);
    }
}

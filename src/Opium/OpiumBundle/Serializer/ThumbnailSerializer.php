<?php

namespace Opium\OpiumBundle\Serializer;

use JMS\Serializer\EventDispatcher\ObjectEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

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
     * requestStack
     *
     * @var RequestStack
     * @access private
     */
    private $requestStack;

    /**
     * __construct
     *
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     * @access public
     */
    public function __construct(RouterInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    public function onSerializerPostSerialize(ObjectEvent $event)
    {
        $photo = $event->getObject();

        $width = $this->requestStack->getMasterRequest()->headers->get('X-Device-Width', 1170);
        $height = 200;

        $thumbnails = [
            'square-200x200' => $this->router->generate(
                'image_crop',
                [ 'slug' => $photo->getSlug(), 'width' => 200, 'height' => 200 ],
                true
            ),

            'banner' => $this->router->generate(
                'image_crop',
                [ 'slug' => $photo->getSlug(), 'width' => $width, 'height' => $height ],
                true
            ),
        ];

        $event->getVisitor()->addData('thumbnails', $thumbnails);
    }
}

<?php

namespace Opium\OpiumBundle\Serializer;

use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Opium\Component\Layout\LineLayout;
use Opium\OpiumBundle\Entity\Directory;
use Opium\OpiumBundle\Entity\Photo;

class ThumbnailSerializer implements EventSubscriberInterface
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
     * lineLayout
     *
     * @var LineLayout
     * @access private
     */
    private $lineLayout;

    /**
     * __construct
     *
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     * @access public
     */
    public function __construct(RouterInterface $router, RequestStack $requestStack, LineLayout $lineLayout)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->lineLayout = $lineLayout;
    }

    /**
     * onSerializerPostSerialize
     *
     * @param ObjectEvent $event
     * @access public
     * @return void
     */
    public function onSerializerPostSerialize(ObjectEvent $event)
    {
        $object = $event->getObject();

        switch (true) {
            case $object instanceof Photo:
                $this->onPostSerializePhoto($event);
                break;

            case $object instanceof Directory:
                $this->onPostSerializeDirectory($event);
                break;

            default:
                break;

        }
    }

    /**
     * onPostSerializeDirectory
     *
     * @param ObjectEvent $event
     * @access private
     * @return void
     */
    private function onPostSerializeDirectory(ObjectEvent $event)
    {
        $directory = $event->getObject();
        $request = $this->requestStack->getMasterRequest();

        $width = $request->headers->get('X-Device-Width', 1170);
        $gutter = $request->query->get('gutter', 0);
        $width = 600;

        $lines = $this->lineLayout->computeRectangleList($directory->getChildren(), $width, 200, $gutter);
        $sizes = [];
        if ($event->getContext()->getDepth() == 0) {
            foreach ($lines as $item) {
                if ($item instanceof Directory) {
                    $slug = $item->getDirectoryThumbnail() ? $item->getDirectoryThumbnail()->getSlug() : null;
                } else {
                    $slug = $item->getSlug();
                }

                if ($slug) {
                    $sizes[$item->getId()] = $this->router->generate(
                        'image_crop',
                        [
                            'slug' => $slug,
                                'cropWidth' => $lines[$item]->getWidth(),
                                'cropHeight' => $lines[$item]->getHeight()
                        ],
                        true
                    );
                }
            }

            $event->getVisitor()->addData('images_link', $sizes);
        }
    }

    /**
     * onPostSerializePhoto
     *
     * @param Photo $photo
     * @access private
     * @return void
     */
    private function onPostSerializePhoto(ObjectEvent $event)
    {
        $photo = $event->getObject();
        $width = $this->requestStack->getMasterRequest()->headers->get('X-Device-Width', 1170);
        $height = 200;

        $thumbnails = [
            'square-200x200' => $this->router->generate(
                'image_crop',
                [ 'slug' => $photo->getSlug(), 'cropWidth' => 200, 'cropHeight' => 200 ],
                true
            ),

            'banner' => $this->router->generate(
                'image_crop',
                [ 'slug' => $photo->getSlug(), 'cropWidth' => $width, 'cropHeight' => $height ],
                true
            ),
        ];

        $event->getVisitor()->addData('thumbnails', $thumbnails);
    }

     /**
      * getSubscribedEvents
      *
      * @static
      * @access public
      * @return void
      */
     public static function getSubscribedEvents()
     {
         return array(
             array('event' => 'serializer.post_serialize', 'method' => 'onSerializerPostSerialize'),
         );
     }
}

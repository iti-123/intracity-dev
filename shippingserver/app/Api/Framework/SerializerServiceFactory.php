<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/8/17
 * Time: 1:04 AM
 */

namespace Api\Framework;

use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

/**
 * Class SerializerServiceFactory
 */
class SerializerServiceFactory
{
    /**
     * @var Serializer
     */
    static $serializer;

    public static function create()
    {
        if (static::$serializer === null) {

            AnnotationRegistry::registerAutoloadNamespace(
                'JMS\Serializer\Annotation', env('BASE_DIR') . '/vendor/jms/serializer/src'
            );
            static::$serializer = SerializerBuilder::create()
                ->setCacheDir(env('BASE_DIR') . '/storage/serializer')
                ->setDebug(env('API_DEBUG'))
                ->build();
        }

        return static::$serializer;
    }
}
<?php


namespace App\Service;


use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SerializerService
{
    public function serialize ($data): string
    {
        $encoder = new JsonEncoder();

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return [
                    'title' => $object->getId()
                ];
            },
        ];

        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        $serializer = new Serializer([$normalizer], [$encoder]);
        return $serializer->serialize($data, 'json');
    }
}

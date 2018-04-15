<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 13/04/2018
 * Time: 14:51
 */

namespace App\Service\Serializer;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Serializer\Serializer;
use Hateoas\Serializer\JsonSerializerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Context as JMSContext;
use JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface;
use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;
use JMS\Serializer\DeserializationContext;
use App\Entity\Phone;

class CustomSerializer extends SerializerBuilder implements JsonSerializerInterface, Serializer
{
    /**
     * @internal
     */
    const SERIALIZATION = 0;

    /**
     * @internal
     */
    const DESERIALIZATION = 1;
    const PHONE_DESC = "Full description by clicking on the link self";

    private $serializer;
    private $serializationContextFactory;
    private $deserializationContextFactory;

    public function __construct(
        SerializerInterface $serializer,
        SerializationContextFactoryInterface $serializationContextFactory = null,
        DeserializationContextFactoryInterface $deserializationContextFactory = null
    ) {
        parent::__construct();
        $this->serializer = $serializer;
        $this->serializationContextFactory = $serializationContextFactory;
        $this->deserializationContextFactory = $deserializationContextFactory;
    }

    public function serializeLinks(array $links, JsonSerializationVisitor $visitor, SerializationContext $context)
    {
        $serializedLinks = array();
        foreach ($links as $link) {
            $serializedLink = array_merge(array(
                'href' => $link->getHref(),
            ), $link->getAttributes());

            if (!isset($serializedLinks[$link->getRel()]) && 'curies' !== $link->getRel()) {
                $serializedLinks[$link->getRel()] = $serializedLink;
            } elseif (isset($serializedLinks[$link->getRel()]['href'])) {
                $serializedLinks[$link->getRel()] = array(
                    $serializedLinks[$link->getRel()],
                    $serializedLink
                );
            } else {
                $serializedLinks[$link->getRel()][] = $serializedLink;
            }
        }

        $visitor->addData('_links', $serializedLinks);
    }

    public function serializeEmbeddeds(array $embeddeds, JsonSerializationVisitor $visitor, SerializationContext $context)
    {
        $serializedEmbeddeds = array();
        $multiple = array();

        foreach ($embeddeds as $embedded) {
            $items = $embedded->getData();
            
            if ($items[0] instanceof Phone) {
                foreach ($items as $item) {
                    $item->setDescription(self::PHONE_DESC);
                }
            }

            $context->pushPropertyMetadata($embedded->getMetadata());

            if (!isset($serializedEmbeddeds[$embedded->getRel()])) {
                $serializedEmbeddeds[$embedded->getRel()] = $context->accept($embedded->getData());
            } elseif (!isset($multiple[$embedded->getRel()])) {
                $multiple[$embedded->getRel()] = true;

                $serializedEmbeddeds[$embedded->getRel()] = array(
                    $serializedEmbeddeds[$embedded->getRel()],
                    $context->accept($embedded->getData()),
                );
            } else {
                $serializedEmbeddeds[$embedded->getRel()][] = $context->accept($embedded->getData());
            }

            $context->popPropertyMetadata();
        }

        $visitor->addData('_embedded', $serializedEmbeddeds);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($data, $format, Context $context)
    {
        $context = $this->convertContext($context, self::SERIALIZATION);

        return $this->serializer->serialize($data, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize($data, $type, $format, Context $context)
    {
        $context = $this->convertContext($context, self::DESERIALIZATION);

        return $this->serializer->deserialize($data, $type, $format, $context);
    }

    /**
     * @param Context $context
     * @param int     $direction {@see self} constants
     *
     * @return JMSContext
     */
    private function convertContext(Context $context, $direction)
    {
        if (self::SERIALIZATION === $direction) {
            $jmsContext = $this->serializationContextFactory
                ? $this->serializationContextFactory->createSerializationContext()
                : SerializationContext::create();
        } else {
            $jmsContext = $this->deserializationContextFactory
                ? $this->deserializationContextFactory->createDeserializationContext()
                : DeserializationContext::create();
            $maxDepth = $context->getMaxDepth(false);
            if (null !== $maxDepth) {
                for ($i = 0; $i < $maxDepth; ++$i) {
                    $jmsContext->increaseDepth();
                }
            }
        }

        foreach ($context->getAttributes() as $key => $value) {
            $jmsContext->attributes->set($key, $value);
        }

        if (null !== $context->getVersion()) {
            $jmsContext->setVersion($context->getVersion());
        }
        if (null !== $context->getGroups()) {
            $jmsContext->setGroups($context->getGroups());
        }
        if (null !== $context->getMaxDepth(false) || null !== $context->isMaxDepthEnabled()) {
            $jmsContext->enableMaxDepthChecks();
        }
        if (null !== $context->getSerializeNull()) {
            $jmsContext->setSerializeNull($context->getSerializeNull());
        }

        foreach ($context->getExclusionStrategies() as $strategy) {
            $jmsContext->addExclusionStrategy($strategy);
        }

        return $jmsContext;
    }
}

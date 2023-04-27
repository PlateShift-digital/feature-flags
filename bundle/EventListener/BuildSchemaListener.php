<?php

namespace PlateShift\FeatureFlagBundle\EventListener;

use Ibexa\Contracts\DoctrineSchema\Event\SchemaBuilderEvent;
use Ibexa\Contracts\DoctrineSchema\SchemaBuilderEvents;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BuildSchemaListener implements EventSubscriberInterface
{
    private string $schemaPath;

    public function __construct(string $schemaPath)
    {
        $this->schemaPath = $schemaPath;
    }

    #[ArrayShape([SchemaBuilderEvents::BUILD_SCHEMA => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            SchemaBuilderEvents::BUILD_SCHEMA => 'onBuildSchema',
        ];
    }

    public function onBuildSchema(SchemaBuilderEvent $event): void
    {
        $event
            ->getSchemaBuilder()
            ->importSchemaFromFile($this->schemaPath);
    }
}

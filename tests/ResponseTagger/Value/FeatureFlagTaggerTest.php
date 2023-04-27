<?php

namespace Test\PlateShift\FeatureFlagBundle\ResponseTagger\Value;

use FOS\HttpCache\ResponseTagger;
use PlateShift\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;
use PlateShift\FeatureFlagBundle\ResponseTagger\Value\FeatureFlagTagger;
use PHPUnit\Framework\TestCase;

class FeatureFlagTaggerTest extends TestCase
{
    public function test_ignores_non_feature_tags(): void
    {
        $responseTagger = $this->createMock(ResponseTagger::class);
        $responseTagger->expects($this->never())->method('addTags');

        $tagger = new FeatureFlagTagger($responseTagger);

        $tagger->tag(null);
    }

    public function test_adds_feature_tags_to_response_tagger(): void
    {
        $feature = new FeatureFlag([
            'identifier' => 'foo',
            'checkedScopes' => [
                'global',
                'test',
                'default',
            ],
        ]);

        $responseTagger = $this->createMock(ResponseTagger::class);
        $responseTagger->expects($this->once())->method('addTags')->with([
            'ps-ff-global-foo',
            'ps-ff-test-foo',
            'ps-ff-default-foo',
        ]);

        $tagger = new FeatureFlagTagger($responseTagger);

        $tagger->tag($feature);
    }
}

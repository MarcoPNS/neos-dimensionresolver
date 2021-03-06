<?php
namespace Flowpack\Neos\DimensionResolver\Tests\Unit\Http\ContentDimensionDetection;

/*
 * This file is part of the Neos.Neos package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */
use Neos\Flow\Tests\UnitTestCase;
use Flowpack\Neos\DimensionResolver\Http\ContentDimensionDetection;
use Flowpack\Neos\DimensionResolver\Http\ContentDimensionResolutionMode;
use Flowpack\Neos\DimensionResolver\Tests\Unit\Http\ContentDimensionDetection\Fixtures\InvalidDummyDimensionPresetDetector;
use Flowpack\Neos\DimensionResolver\Tests\Unit\Http\ContentDimensionDetection\Fixtures\ValidDummyDimensionPresetDetector;

/**
 * Test case for the DetectContentSubgraphMiddleware
 */
class DimensionPresetDetectorResolverTest extends UnitTestCase
{
    /**
     * @test
     */
    public function resolveDimensionPresetDetectorReturnsSubdomainDetectorForMatchingResolutionMode()
    {
        $resolver = new ContentDimensionDetection\DimensionPresetDetectorResolver();

        $detector = $resolver->resolveDimensionPresetDetector('dimensionName', [
            'resolution' => ['mode' => ContentDimensionResolutionMode::RESOLUTION_MODE_SUBDOMAIN],
        ]);

        $this->assertSame(ContentDimensionDetection\SubdomainDimensionPresetDetector::class, get_class($detector));
    }

    /**
     * @test
     */
    public function resolveDimensionPresetDetectorReturnsTopLevelDomainDetectorForMatchingResolutionMode()
    {
        $resolver = new ContentDimensionDetection\DimensionPresetDetectorResolver();

        $detector = $resolver->resolveDimensionPresetDetector('dimensionName', [
            'resolution' => ['mode' => ContentDimensionResolutionMode::RESOLUTION_MODE_TOPLEVELDOMAIN],
        ]);

        $this->assertSame(ContentDimensionDetection\TopLevelDomainDimensionPresetDetector::class, get_class($detector));
    }

    /**
     * @test
     */
    public function resolveDimensionPresetDetectorReturnsUriPathSegmentDetectorForMatchingResolutionMode()
    {
        $resolver = new ContentDimensionDetection\DimensionPresetDetectorResolver();

        $detector = $resolver->resolveDimensionPresetDetector('dimensionName', [
            'resolution' => ['mode' => ContentDimensionResolutionMode::RESOLUTION_MODE_URIPATHSEGMENT],
        ]);

        $this->assertSame(ContentDimensionDetection\UriPathSegmentDimensionPresetDetector::class, get_class($detector));
    }

    /**
     * @test
     */
    public function resolveDimensionPresetDetectorReturnsUriPathSegmentDetectorIfNothingWasConfigured()
    {
        $resolver = new ContentDimensionDetection\DimensionPresetDetectorResolver();

        $detector = $resolver->resolveDimensionPresetDetector('dimensionName', []);

        $this->assertSame(ContentDimensionDetection\UriPathSegmentDimensionPresetDetector::class, get_class($detector));
    }

    /**
     * @test
     */
    public function resolveDimensionPresetDetectorReturnsConfiguredDetectorIfImplementationClassExistsAndImplementsTheDetectorInterface()
    {
        $resolver = new ContentDimensionDetection\DimensionPresetDetectorResolver();

        $detector = $resolver->resolveDimensionPresetDetector('dimensionName', [
            'resolution' => ['mode' => ContentDimensionResolutionMode::RESOLUTION_MODE_SUBDOMAIN],
            'detectionComponent' => [
                'implementationClassName' => ValidDummyDimensionPresetDetector::class
            ]
        ]);

        $this->assertSame(ValidDummyDimensionPresetDetector::class, get_class($detector));
    }

    /**
     * @test
     * @expectedException \Flowpack\Neos\DimensionResolver\Http\Exception\InvalidDimensionPresetDetectorException
     */
    public function resolveDimensionPresetDetectorThrowsExceptionWithNotExistingDetectorImplementationClassConfigured()
    {
        $resolver = new ContentDimensionDetection\DimensionPresetDetectorResolver();

        $resolver->resolveDimensionPresetDetector('dimensionName', [
            'detectionComponent' => [
                'implementationClassName' => 'Flowpack\Neos\DimensionResolver\Http\ContentDimensionDetection\NonExistingImplementation'
            ]
        ]);
    }

    /**
     * @test
     * @expectedException \Flowpack\Neos\DimensionResolver\Http\Exception\InvalidDimensionPresetDetectorException
     */
    public function resolveDimensionPresetDetectorThrowsExceptionWithImplementationClassNotImplementingTheDetectorInterfaceConfigured()
    {
        $resolver = new ContentDimensionDetection\DimensionPresetDetectorResolver();

        $resolver->resolveDimensionPresetDetector('dimensionName', [
            'detectionComponent' => [
                'implementationClassName' => InvalidDummyDimensionPresetDetector::class
            ]
        ]);
    }
}

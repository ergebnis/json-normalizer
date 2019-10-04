<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Test\Unit\JsonSchema\Uri\Retrievers;

use JsonSchema\Exception;
use JsonSchema\Uri;
use Localheinz\Json\Normalizer\Exception\UriRetrieverRequiredException;
use Localheinz\Json\Normalizer\JsonSchema\Uri\Retrievers\ChainUriRetriever;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;
use Prophecy\Argument;

/**
 * @internal
 * @coversNothing
 */
final class ChainUriRetrieverTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsUriRetrieverInterface(): void
    {
        $this->assertClassImplementsInterface(Uri\Retrievers\UriRetrieverInterface::class, ChainUriRetriever::class);
    }

    public function testConstructorRejectsEmptyRetrievers(): void
    {
        $this->expectException(UriRetrieverRequiredException::class);

        new ChainUriRetriever();
    }

    public function testDefaults(): void
    {
        $retriever = $this->prophesize(Uri\Retrievers\UriRetrieverInterface::class)->reveal();

        $uriRetriever = new ChainUriRetriever($retriever);

        self::assertSame('', $uriRetriever->getContentType());
    }

    public function testRetrieveThrowsResourceNotFoundExceptionWhenNoneOfTheRetrieversWhereAbleToRetrieveUri(): void
    {
        $uri = $this->faker()->url;

        $retrievers = \array_map(function () use ($uri): Uri\Retrievers\UriRetrieverInterface {
            $retriever = $this->prophesize(Uri\Retrievers\UriRetrieverInterface::class);

            $retriever
                ->retrieve(Argument::is($uri))
                ->shouldBeCalled()
                ->willThrow(new Exception\ResourceNotFoundException());

            return $retriever->reveal();
        }, \range(0, 2));

        $uriRetriever = new ChainUriRetriever(...$retrievers);

        $this->expectException(Exception\ResourceNotFoundException::class);

        $uriRetriever->retrieve($uri);
    }

    public function testRetrieveReturnsSchemaFromFirstRetrieverThatWasAbleToRetrieve(): void
    {
        $faker = $this->faker();

        $uri = $faker->url;
        $schema = \json_encode($faker->words);
        $contentType = $faker->mimeType;

        $failedRetriever = $this->prophesize(Uri\Retrievers\UriRetrieverInterface::class);

        $failedRetriever
            ->retrieve(Argument::is($uri))
            ->shouldBeCalled()
            ->willThrow(new Exception\ResourceNotFoundException());

        $successfulRetriever = $this->prophesize(Uri\Retrievers\UriRetrieverInterface::class);

        $successfulRetriever
            ->retrieve(Argument::is($uri))
            ->shouldBeCalled()
            ->willReturn($schema);

        $successfulRetriever
            ->getContentType()
            ->shouldBeCalled()
            ->willReturn($contentType);

        $untouchedRetriever = $this->prophesize(Uri\Retrievers\UriRetrieverInterface::class);

        $uriRetriever = new ChainUriRetriever(
            $failedRetriever->reveal(),
            $successfulRetriever->reveal(),
            $untouchedRetriever->reveal()
        );

        self::assertSame($schema, $uriRetriever->retrieve($uri));
        self::assertSame($contentType, $uriRetriever->getContentType());
    }
}

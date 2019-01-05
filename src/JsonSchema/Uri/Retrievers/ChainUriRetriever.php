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

namespace Localheinz\Json\Normalizer\JsonSchema\Uri\Retrievers;

use JsonSchema\Exception\ResourceNotFoundException;
use JsonSchema\Uri;
use Localheinz\Json\Normalizer\Exception;

final class ChainUriRetriever implements Uri\Retrievers\UriRetrieverInterface
{
    /**
     * @var Uri\Retrievers\UriRetrieverInterface[]
     */
    private $retrievers;

    /**
     * @var null|string
     */
    private $contentType;

    /**
     * @param Uri\Retrievers\UriRetrieverInterface ...$retrievers
     *
     * @throws Exception\UriRetrieverRequiredException
     */
    public function __construct(Uri\Retrievers\UriRetrieverInterface ...$retrievers)
    {
        if (0 === \count($retrievers)) {
            throw Exception\UriRetrieverRequiredException::create();
        }

        $this->retrievers = $retrievers;
    }

    public function retrieve($uri)
    {
        foreach ($this->retrievers as $retriever) {
            try {
                $contents = $retriever->retrieve($uri);
            } catch (ResourceNotFoundException $exception) {
                continue;
            }

            $this->contentType = $retriever->getContentType();

            return $contents;
        }

        throw new ResourceNotFoundException(\sprintf(
            'JSON schema not found at %s',
            $uri
        ));
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }
}

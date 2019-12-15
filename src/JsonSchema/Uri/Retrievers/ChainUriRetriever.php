<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\JsonSchema\Uri\Retrievers;

use Ergebnis\Json\Normalizer\Exception;
use JsonSchema\Exception\ResourceNotFoundException;
use JsonSchema\Uri;

final class ChainUriRetriever implements Uri\Retrievers\UriRetrieverInterface
{
    /**
     * @var Uri\Retrievers\UriRetrieverInterface[]
     */
    private $retrievers;

    /**
     * @var string
     */
    private $contentType = '';

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

    public function getContentType(): string
    {
        return $this->contentType;
    }
}

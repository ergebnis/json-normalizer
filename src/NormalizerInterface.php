<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer;

interface NormalizerInterface
{
    /**
     * @throws Exception\SchemaUriCouldNotBeResolvedException
     * @throws Exception\SchemaUriCouldNotBeReadException
     * @throws Exception\SchemaUriReferencesDocumentWithInvalidMediaTypeException
     * @throws Exception\SchemaUriReferencesInvalidJsonDocumentException
     * @throws Exception\OriginalInvalidAccordingToSchemaException
     * @throws Exception\NormalizedInvalidAccordingToSchemaException
     */
    public function normalize(Json $json): Json;
}

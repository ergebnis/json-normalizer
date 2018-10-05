<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer;

interface NormalizerInterface
{
    /**
     * @param Json $json
     *
     * @throws Exception\SchemaUriCouldNotBeResolvedException
     * @throws Exception\SchemaUriCouldNotBeReadException
     * @throws Exception\SchemaUriReferencesDocumentWithInvalidMediaTypeException
     * @throws Exception\SchemaUriReferencesInvalidJsonDocumentException
     * @throws Exception\OriginalInvalidAccordingToSchemaException
     * @throws Exception\NormalizedInvalidAccordingToSchemaException
     *
     * @return Json
     */
    public function normalize(Json $json): Json;
}

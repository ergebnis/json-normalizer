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

use Localheinz\Json\Format\FormatInterface;
use Localheinz\Json\JsonInterface;

final class FixedFormatNormalizer implements NormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var FormatInterface
     */
    private $format;

    /**
     * @var Format\FormatterInterface
     */
    private $formatter;

    public function __construct(
        NormalizerInterface $normalizer,
        FormatInterface $format,
        Format\FormatterInterface $formatter = null
    ) {
        $this->normalizer = $normalizer;
        $this->format = $format;
        $this->formatter = $formatter ?: new Format\Formatter();
    }

    public function normalize(JsonInterface $json): JsonInterface
    {
        return $this->formatter->format(
            $this->normalizer->normalize($json),
            $this->format
        );
    }
}

<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Double\Rule;

use Ergebnis\Json\Normalizer\Context;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Normalizer\Schema;
use Ergebnis\Json\Parser;
use Ergebnis\Json\Pointer;

final class SchemaRecordingRule implements Rule
{
    /**
     * @var array<string, null|Schema>
     */
    private array $schemas = [];

    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function name(): Rule\Name
    {
        return Rule\Name::fromString('record-schema');
    }

    public function target(): Rule\Target
    {
        return Rule\Target::create(
            Pointer\Specification::always(),
            Parser\Node\Node::class,
        );
    }

    public function definition(): Rule\Definition
    {
        return Rule\Definition::create(
            'Test double.',
            Rule\Example::create(
                '{}',
                '{}',
            ),
        );
    }

    public function apply(
        Parser\Node\Node $node,
        Context $context
    ): Rule\Action {
        $this->schemas[$context->path()->toJsonPointer()->toJsonString()] = $context->schema();

        return Rule\Action::keep();
    }

    /**
     * @return array<string, null|Schema>
     */
    public function schemas(): array
    {
        return $this->schemas;
    }
}

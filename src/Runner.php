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

namespace Ergebnis\Json\Normalizer;

use Ergebnis\Json\Parser;

/**
 * @internal
 */
final class Runner
{
    public const MAXIMUM_PASSES = 10;
    private Configuration $configuration;
    private Parser\Parser $parser;
    private Parser\Printer $printer;

    private function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->parser = new Parser\Parser();
        $this->printer = new Parser\Printer();
    }

    public static function create(Configuration $configuration): self
    {
        return new self($configuration);
    }

    /**
     * @throws Exception\RulesDidNotSettle
     */
    public function normalize(Parser\Raw $raw): Result
    {
        $node = $this->parser->parse(
            $raw,
            Parser\MaximumDepth::default(),
        );

        $changes = [];
        $changesOfPass = [];

        for ($pass = 1; self::MAXIMUM_PASSES >= $pass; ++$pass) {
            $visitor = RuleVisitor::create($this->configuration);

            $traverser = new Parser\Traverser\Traverser($visitor);

            $node = $traverser->traverse($node);

            $changesOfPass = $visitor->changes();

            if ([] === $changesOfPass) {
                return $this->result(
                    $raw,
                    $node,
                    $changes,
                );
            }

            foreach ($changesOfPass as $change) {
                $changes[] = $change;
            }
        }

        throw Exception\RulesDidNotSettle::after(
            self::MAXIMUM_PASSES,
            ...$changesOfPass,
        );
    }

    /**
     * @param list<Change> $changes
     */
    private function result(
        Parser\Raw $raw,
        Parser\Node\Node $node,
        array $changes
    ): Result {
        $printed = $this->printer->print(
            $node,
            $this->configuration->format(Parser\Format::fromRaw($raw)),
        );

        return Result::create(
            $raw,
            Parser\Raw::fromString($printed),
            ...$changes,
        );
    }
}

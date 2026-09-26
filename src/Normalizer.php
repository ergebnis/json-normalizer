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

final class Normalizer
{
    public const MAXIMUM_PASSES = 10;
    private Configuration $configuration;
    private Parser\Parser $parser;
    private Parser\Printer $printer;
    private SchemaLoader $schemaLoader;
    private SchemaResolver $schemaResolver;

    private function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->parser = new Parser\Parser();
        $this->printer = new Parser\Printer();
        $this->schemaLoader = SchemaLoader::create();
        $this->schemaResolver = SchemaResolver::create($this->schemaLoader);
    }

    public static function create(Configuration $configuration): self
    {
        return new self($configuration);
    }

    /**
     * @throws Exception\InputInvalidAccordingToSchema
     * @throws Exception\OutputInvalidAccordingToSchema
     * @throws Exception\RulesDidNotSettle
     */
    public function normalize(Parser\Raw $raw): Result
    {
        $schemaUri = $this->configuration->schemaUri();

        $schema = null;

        if (null !== $schemaUri) {
            $schema = $this->schemaLoader->load($schemaUri);
        }

        $node = $this->parser->parse(
            $raw,
            Parser\MaximumDepth::default(),
        );

        $inputErrors = $this->errors(
            $schema,
            $node,
        );

        if ([] !== $inputErrors) {
            throw Exception\InputInvalidAccordingToSchema::withErrors(
                (string) $schemaUri,
                ...$inputErrors,
            );
        }

        $changes = [];
        $changesOfPass = [];

        for ($pass = 1; self::MAXIMUM_PASSES >= $pass; ++$pass) {
            $visitor = RuleVisitor::create(
                $this->configuration,
                $schema,
                $this->schemaResolver,
            );

            $traverser = new Parser\Traverser\Traverser($visitor);

            $node = $traverser->traverse($node);

            $changesOfPass = $visitor->changes();

            if ([] === $changesOfPass) {
                $outputErrors = $this->errors(
                    $schema,
                    $node,
                );

                if ([] !== $outputErrors) {
                    throw Exception\OutputInvalidAccordingToSchema::withErrors(
                        (string) $schemaUri,
                        ...$outputErrors,
                    );
                }

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
     * @return list<string>
     */
    private function errors(
        ?Schema $schema,
        Parser\Node\Node $node
    ): array {
        if (!$schema instanceof Schema) {
            return [];
        }

        return $this->schemaLoader->errors(
            $schema,
            $node,
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

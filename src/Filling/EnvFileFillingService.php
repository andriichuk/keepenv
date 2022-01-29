<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Filling;

use Andriichuk\KeepEnv\Environment\Reader\EnvReaderInterface;
use Andriichuk\KeepEnv\Environment\Writer\EnvWriterInterface;
use Andriichuk\KeepEnv\Specification\Reader\SpecificationReaderInterface;
use Andriichuk\KeepEnv\Validation\VariableValidationInterface;
use RuntimeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnvFileFillingService
{
    private SpecificationReaderInterface $specificationReader;
    private EnvReaderInterface $envReader;
    private EnvWriterInterface $envWriter;
    private VariableValidationInterface $variableVerification;

    public function __construct(
        SpecificationReaderInterface $specificationReader,
        EnvReaderInterface $envReader,
        EnvWriterInterface $envWriter,
        VariableValidationInterface $variableVerification
    ) {
        $this->specificationReader = $specificationReader;
        $this->envReader = $envReader;
        $this->envWriter = $envWriter;
        $this->variableVerification = $variableVerification;
    }

    public function fill(
        string $envName,
        string $envPath,
        string $specPath,
        callable $valueProvider,
        callable $successHandler
    ): void {
        $specification = $this->specificationReader->read($specPath);
        $envSpec = $specification->get($envName);

        $emptyVariables = array_filter(
            $this->envReader->read($envPath),
            /**
             * @param mixed $value
             */
            static fn ($value): bool => $value === '',
        );

        $variablesToFill = $envSpec->onlyWithKeys(array_keys($emptyVariables));

        foreach ($variablesToFill as $variable) {
            if (!empty($variable->rules['equals'])) {
                $value = (string) $variable->rules['equals'];
            } else {
                $value = (string) $valueProvider($variable, function (string $value) use ($variable): string {
                    $report = $this->variableVerification->validate($variable, $value);

                    if ($report !== []) {
                        throw new RuntimeException(reset($report)->message);
                    }

                    return $value;
                });
            }

            $this->envWriter->save($variable->name, $value);
            $successHandler("Added $variable->name=$value");
        }
    }
}

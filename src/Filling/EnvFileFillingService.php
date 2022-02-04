<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Filling;

use Andriichuk\KeepEnv\Environment\Reader\EnvReaderInterface;
use Andriichuk\KeepEnv\Environment\Writer\EnvWriterInterface;
use Andriichuk\KeepEnv\Specification\Reader\SpecReaderInterface;
use Andriichuk\KeepEnv\Validation\Exceptions\ValidationReportException;
use Andriichuk\KeepEnv\Validation\VariableValidationInterface;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnvFileFillingService
{
    private SpecReaderInterface $specificationReader;
    private EnvReaderInterface $envReader;
    private EnvWriterInterface $envWriter;
    private VariableValidationInterface $variableValidation;

    public function __construct(
        SpecReaderInterface $specificationReader,
        EnvReaderInterface $envReader,
        EnvWriterInterface $envWriter,
        VariableValidationInterface $variableValidation
    ) {
        $this->specificationReader = $specificationReader;
        $this->envReader = $envReader;
        $this->envWriter = $envWriter;
        $this->variableValidation = $variableValidation;
    }

    /**
     * @return int Count of filled variables.
     */
    public function fill(
        string $envName,
        string $envPath,
        string $specPath,
        callable $valueProvider,
        callable $successHandler
    ): int {
        $specification = $this->specificationReader->read($specPath);
        $envSpec = $specification->get($envName);

        $variablesFromFile = $this->envReader->read($envPath);
        $variables = 0;

        foreach ($envSpec->all() as $variable) {
            if ($variable->system || !empty($variablesFromFile[$variable->name])) {
                continue;
            }

            if (!empty($variable->rules['equals'])) {
                $value = (string) $variable->rules['equals'];
            } else {
                $value = (string) $valueProvider($variable, function (string $value) use ($variable): string {
                    $report = $this->variableValidation->validate($variable, $value);

                    if ($report !== []) {
                        throw ValidationReportException::variableIsNotValid(reset($report)->message);
                    }

                    return $value;
                });
            }

            $this->envWriter->save($variable->name, $value);
            $successHandler("Added $variable->name=$value");
            $variables++;
        }

        return $variables;
    }
}

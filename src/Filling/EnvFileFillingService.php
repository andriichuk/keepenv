<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Filling;

use Andriichuk\Enviro\Environment\Loader\EnvFileLoaderInterface;
use Andriichuk\Enviro\Environment\Writer\EnvFileWriter;
use Andriichuk\Enviro\Specification\Reader\SpecificationReaderInterface;
use Andriichuk\Enviro\Verification\VariableVerification;
use RuntimeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnvFileFillingService
{
    private SpecificationReaderInterface $specificationReader;
    private EnvFileLoaderInterface $envFileLoader;
    private EnvFileWriter $envFileWriter;
    private VariableVerification $variableVerification;

    public function __construct(
        SpecificationReaderInterface $specificationReader,
        EnvFileLoaderInterface $envFileLoader,
        EnvFileWriter $envFileWriter,
        VariableVerification $variableVerification
    ) {
        $this->specificationReader = $specificationReader;
        $this->envFileLoader = $envFileLoader;
        $this->envFileWriter = $envFileWriter;
        $this->variableVerification = $variableVerification;
    }

    public function fill(
        string $environmentName,
        array $envPaths,
        string $specPath,
        callable $valueProvider,
        callable $successHandler
    ): void {
        $specification = $this->specificationReader->read($specPath);
        $envSpec = $specification->get($environmentName);

        $emptyVariables = array_filter($this->envFileLoader->load($envPaths), static function ($value): bool {
            return $value === '';
        });

        $variablesToFill = $envSpec->onlyWithKeys($emptyVariables);

        foreach ($variablesToFill as $variable) {
            if (!empty($variable->rules['equals'])) {
                $value = $variable->rules['equals'];
            } else {
                $value = $valueProvider($variable, function ($value) use ($variable): string {
                    $report = $this->variableVerification->validate($variable, $value);

                    if ($report !== []) {
                        throw new RuntimeException(reset($report)->message);
                    }

                    return $value;
                });
            }

            $this->envFileWriter->save($variable->name, (string) $value);
            $successHandler("Added $variable->name=$value");
        }
    }
}

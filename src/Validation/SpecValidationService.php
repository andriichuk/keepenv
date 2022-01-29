<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation;

use Andriichuk\KeepEnv\Environment\Loader\EnvLoaderInterface;
use Andriichuk\KeepEnv\Specification\Reader\SpecificationReaderInterface;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecValidationService
{
    private EnvLoaderInterface $envFileLoader;
    private SpecificationReaderInterface $specificationReader;
    private VariableValidation $variableVerification;

    public function __construct(
        SpecificationReaderInterface $specificationReader,
        EnvLoaderInterface $envFileLoader,
        VariableValidation $variableVerification
    ) {
        $this->envFileLoader = $envFileLoader;
        $this->specificationReader = $specificationReader;
        $this->variableVerification = $variableVerification;
    }

    public function validate(
        string $envName,
        array  $envPaths,
        string $specPath,
        bool   $overrideExistingVariables
    ): ValidationReport {
        $envVariables = $this->specificationReader->read($specPath)->get($envName);
        $variableValues = $this->envFileLoader->load($envPaths, $overrideExistingVariables);

        $verificationReport = new ValidationReport();
        $verificationReport->setVariablesCount($envVariables->count());

        foreach ($envVariables->all() as $variable) {
            $report = $this->variableVerification->validate($variable, $variableValues[$variable->name] ?? null);

            foreach ($report as $reportItem) {
                $verificationReport->add($reportItem);
            }
        }

        return $verificationReport;
    }
}

<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Verification;

use Andriichuk\KeepEnv\Environment\Loader\EnvLoaderInterface;
use Andriichuk\KeepEnv\Specification\Reader\SpecificationReaderInterface;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecVerificationService
{
    private EnvLoaderInterface $envFileLoader;
    private SpecificationReaderInterface $specificationReader;
    private VariableVerification $variableVerification;

    public function __construct(
        SpecificationReaderInterface $specificationReader,
        EnvLoaderInterface $envFileLoader,
        VariableVerification $variableVerification
    ) {
        $this->envFileLoader = $envFileLoader;
        $this->specificationReader = $specificationReader;
        $this->variableVerification = $variableVerification;
    }

    public function verify(
        string $envName,
        array  $envPaths,
        string $specPath,
        bool   $overrideExistingVariables
    ): VerificationReport {
        $envVariables = $this->specificationReader->read($specPath)->get($envName);
        $variableValues = $this->envFileLoader->load($envPaths, $overrideExistingVariables);

        $verificationReport = new VerificationReport();
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

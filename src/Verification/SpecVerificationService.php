<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Verification;

use Andriichuk\Enviro\Environment\Loader\EnvFileLoaderInterface;
use Andriichuk\Enviro\Specification\Reader\SpecificationReaderInterface;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecVerificationService
{
    private EnvFileLoaderInterface $envFileLoader;
    private SpecificationReaderInterface $specificationReader;
    private VariableVerification $variableVerification;

    public function __construct(
        SpecificationReaderInterface $specificationReader,
        EnvFileLoaderInterface $envFileLoader,
        VariableVerification $variableVerification
    ) {
        $this->envFileLoader = $envFileLoader;
        $this->specificationReader = $specificationReader;
        $this->variableVerification = $variableVerification;
    }

    public function verify(string $environmentName, array $envPaths, string $specPath): VerificationReport
    {
        $specification = $this->specificationReader->read($specPath)->get($environmentName);
        $verificationReport = new VerificationReport();
        $variableValues = $this->envFileLoader->load($envPaths);

        foreach ($specification->all() as $variable) {
            $report = $this->variableVerification->validate($variable, $variableValues[$variable->name] ?? null);

            foreach ($report as $reportItem) {
                $verificationReport->add($reportItem);
            }
        }

        return $verificationReport;
    }
}

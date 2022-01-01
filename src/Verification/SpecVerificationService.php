<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Verification;

use Andriichuk\Enviro\Reader\Specification\SpecificationReaderInterface;
use Andriichuk\Enviro\Validation\ValidatorRegistryInterface;
use Andriichuk\Enviro\Writer\Env\EnvFileWriter;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecVerificationService
{
    private EnvFileWriter $envFileWriter;
    private SpecificationReaderInterface $specificationReader;
    private ValidatorRegistryInterface $validatorRegistry;

    public function __construct(
        EnvFileWriter $envFileWriter,
        SpecificationReaderInterface $specificationReader,
        ValidatorRegistryInterface $validatorRegistry
    ) {
        $this->envFileWriter = $envFileWriter;
        $this->specificationReader = $specificationReader;
        $this->validatorRegistry = $validatorRegistry;
    }

    public function verify(string $source, string $envName): VerificationReport
    {
        $specification = $this->specificationReader->read($source)->get($envName);
        $verificationReport = new VerificationReport();

        foreach ($specification->all() as $variable) {
            if ($variable->rules === null) {
                continue;
            }

            foreach ($variable->rules as $ruleName => $options) {
                $ruleName = is_string($ruleName) ? $ruleName : $options;
                $validator = $this->validatorRegistry->get($ruleName);

                $value = $this->envFileWriter->get($variable->name);
                $isValid = $validator->validate($value, is_array($options) ? $options : [$options]);

                if (!$isValid) {
                    $verificationReport->add(
                        new VariableReport(
                            $variable->name,
                            $validator->message([
                                'name' => $variable->name,
                                'value' => $value,
                                'cases' => $options,
                                'equals' => $variable->rules['equals'] ?? '',
                            ])
                        )
                    );
                }
            }
        }

        return $verificationReport;
    }
}

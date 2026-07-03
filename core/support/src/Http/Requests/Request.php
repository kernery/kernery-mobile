<?php

namespace Kernery\Support\Http\Requests;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Create the default validator instance.
     */
    protected function createDefaultValidator(ValidationFactory $factory): Validator
    {
        $rules = method_exists($this, RULES) ? $this->container->call([$this, RULES]) : [];

        $rules = apply_filters(APP_REQUEST_RULES, $rules, $this);

        $messages = apply_filters(APP_REQUEST_MESSAGES, $this->messages(), $this);

        $attributes = apply_filters(APP_REQUEST_ATTRIBUTES, $this->attributes(), $this);

        $validationData = apply_filters(APP_REQUEST_VALIDATION_DATA, $this->validationData(), $this);

        $validator = $factory->make(
            $validationData,
            $rules,
            $messages,
            $attributes
        )->stopOnFirstFailure($this->stopOnFirstFailure);

        if ($this->isPrecognitive()) {
            $validator->setRules(
                $this->filterPrecognitiveRules($validator->getRulesWithoutPlaceholders())
            );
        }

        return $validator;
    }
}

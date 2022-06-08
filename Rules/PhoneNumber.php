<?php

namespace Alyka\Modularizer\Rules;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;
use Illuminate\Contracts\Validation\Rule;

class PhoneNumber implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $phoneProto = $phoneUtil->parse($value);
        } catch (NumberParseException $e) {
            return false;
        }
        return $phoneUtil->isValidNumber($phoneProto);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('core::validation.invalid_phone_number');
    }
}

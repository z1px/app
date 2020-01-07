<?php

namespace Z1px\App\Rules;


use Illuminate\Contracts\Validation\Rule;
use Z1px\Tool\Verify;

class NotEmailRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        return Verify::email($value) ? false : true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
//        return trans('validation.mobile');
        return ':attribute 不能为邮箱号';
    }
}

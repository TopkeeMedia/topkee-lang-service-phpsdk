<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'        => 'accepted',
    'active_url'      => 'active_url',
    'after'           => 'after::date',
    'after_or_equal'  => 'after_or_equal::date',
    'alpha'           => 'alpha',
    'alpha_dash'      => 'alpha_dash',
    'alpha_num'       => 'alpha_num',
    'array'           => 'array',
    'before'          => 'before::date',
    'before_or_equal' => 'before_or_equal::date',
    'between'         => [
        'numeric' => 'between_numeric::min,:max',
        'file'    => 'between_file::min,:max',
        'string'  => 'between_string::min,:max',
        'array'   => 'between_array::min,:max',
    ],
    'boolean'        => 'boolean',
    'confirmed'      => 'confirmed',
    'date'           => 'date',
    'date_equals'    => 'date_equals::date',
    'date_format'    => 'date_format::format',
    'different'      => 'different::other',
    'digits'         => 'digits::digits',
    'digits_between' => 'digits_between::min,:max',
    'dimensions'     => 'dimensions',
    'distinct'       => 'distinct',
    'email'          => 'email',
    'ends_with'      => 'ends_with::values',
    'exists'         => 'exists',
    'file'           => 'file',
    'filled'         => 'filled',
    'gt'             => [
        'numeric' => 'gt_numeric:value',
        'file'    => 'gt_file:value',
        'string'  => 'gt_string:value',
        'array'   => 'gt_array:value',
    ],
    'gte' => [
        'numeric' => 'gte_numeric:value',
        'file'    => 'gte_file:value',
        'string'  => 'gte_string:value',
        'array'   => 'gte_array:value',
    ],
    'image'    => 'image',
    'in'       => 'in',
    'in_array' => 'in_array::other',
    'integer'  => 'integer',
    'ip'       => 'ip',
    'ipv4'     => 'ipv4',
    'ipv6'     => 'ipv6',
    'json'     => 'json',
    'lt'       => [
        'numeric' => 'lt_numeric::value',
        'file'    => 'lt_file::value',
        'string'  => 'lt_string::value',
        'array'   => 'lt_array::value',
    ],
    'lte' => [
        'numeric' => 'lte_numeric::value',
        'file'    => 'lte_file::value',
        'string'  => 'lte_string::value',
        'array'   => 'lte_array::value',
    ],
    'max' => [
        'numeric' => 'lte_numeric::max',
        'file'    => 'lte_file::max',
        'string'  => 'lte_string::max',
        'array'   => 'lte_array::max',
    ],
    'mimes'     => 'mimes::values',
    'mimetypes' => 'mimetypes::values',
    'min'       => [
        'numeric' => 'min_numeric::min',
        'file'    => 'min_file::min',
        'string'  => 'min_string::min',
        'array'   => 'min_array::min',
    ],
    'multiple_of'          => 'multiple_of::value.',
    'not_in'               => 'not_in',
    'not_regex'            => 'not_regex',
    'numeric'              => 'numeric',
    'password'             => 'password',
    'present'              => 'present',
    'regex'                => 'regex',
    'required'             => 'required',
    'required_if'          => 'required_if::other,:value',
    'required_unless'      => 'required_unless::other,:values',
    'required_with'        => 'required_with::values',
    'required_with_all'    => 'required_with_all::values',
    'required_without'     => 'required_without::values',
    'required_without_all' => 'required_without_all::values',
    'prohibited'           => 'prohibited',
    'prohibited_if'        => 'prohibited_if',
    'prohibited_unless'    => 'prohibited_unless',
    'same'                 => 'same::other',
    'size'                 => [
        'numeric' => 'size_numeric::size',
        'file'    => 'size_file::size',
        'string'  => 'size_string::size',
        'array'   => 'size_array::size',
    ],
    'starts_with' => 'starts_with::values',
    'string'      => 'string',
    'timezone'    => 'timezone',
    'unique'      => 'unique',
    'uploaded'    => 'uploaded',
    'url'         => 'url',
    'uuid'        => 'uuid',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],
];

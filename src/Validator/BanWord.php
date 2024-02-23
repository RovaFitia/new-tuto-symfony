<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class BanWord extends Constraint
{
    public function __construct(
        public string $message = 'This contains a banned word {{ banword }}' ,
        public array $banWords = ['spam', 'viagra'] ,
        ?array $groups = null ,
        mixed $payload = null
    )
    {
        Parent::__construct(null, $groups, $payload)  ;
    }
}

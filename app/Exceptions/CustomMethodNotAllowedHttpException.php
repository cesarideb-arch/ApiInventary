<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class CustomMethodNotAllowedHttpException extends MethodNotAllowedHttpException
{
    public function __construct($message = 'El método GET no está permitido para esta ruta. Por favor, usa el método POST.', $code = 405)
    {
        parent::__construct([], $message, null, $code);
    }
}

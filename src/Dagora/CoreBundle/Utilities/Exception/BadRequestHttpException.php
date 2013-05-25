<?php

namespace TE\CoreBundle\Utilities\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * BadRequestHttpException.
 *
 * @author Javier F. Escribano <javier@touristeye.com>
 */
class BadRequestHttpException extends HttpException
{
    /**
     * Constructor.
     *
     * @param string     $message  The internal exception message
     * @param \Exception $previous The previous exception
     * @param integer    $code     The internal exception code
     */
    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(400, $message, $previous, array(), $code);
    }
}

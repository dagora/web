<?php

namespace TE\CoreBundle\Utilities\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * DuplicatedHttpException.
 *
 * @author Javier F. Escribano <javier@touristeye.com>
 */
class DuplicatedHttpException extends HttpException
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
        parent::__construct(409, $message, $previous, array(), $code);
    }
}

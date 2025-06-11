<?php

namespace Me\Weather\Trait;

/**
 * class ResponseCode
 *
 * @author  Vyacheslav Lipatov
 * @package Me\Weather\Trait
 */
trait ResponseCodeTrait
{
    protected const SUCCESS = 200;
    protected const BAD_REQUEST = 400;
    protected const UNAUTHORIZED = 401;
    protected const FORBIDDEN = 403;
    protected const NOT_FOUND = 404;
}

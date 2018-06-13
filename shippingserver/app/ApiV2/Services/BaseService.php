<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 03-02-2017
 * Time: 19:23
 */

namespace ApiV2\Services;


use App\Exceptions\ApplicationException;
use App\Exceptions\ServiceException;
use App\Exceptions\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Log;

class BaseService implements AuthenticationAware
{

    protected $principal;

    public static function errorCode($errorSeq)
    {

        $serviceName = ClassName::class;

        $serviceSeq = ServiceErrors::getSeq($serviceName);

        $errorCode = $serviceSeq + $errorSeq;

    }

    public function getServiceName()
    {
        return ClassName::class;
    }

    public function setSecurityPrincipal($principal)
    {
        $this->principal = $principal;
    }

    public function getPrincipal()
    {
        return $this->principal;
    }

    protected function handle(\Exception $e)
    {

        if ($e instanceof ValidationException || $e instanceof AuthorizationException || $e instanceof ServiceException) {
            throw $e;
        }

        if ($e instanceof ApplicationException) {
            throw new ServiceException();
        }

        if ($e instanceof \Exception) {
            LOG::error("Service failed", (array)$e->getMessage());
            throw new ServiceException();
        }


    }


}
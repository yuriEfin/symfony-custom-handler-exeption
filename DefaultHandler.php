<?php

namespace App\Library\Api\Service\HandlerException;

use ...;

/**
 * Class DefaultFatalHandler
 */
class DefaultHandler extends BaseHandler implements HandlerErrorInterface
{
    /**
     * @var int
     */
    protected $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
    
    /**
     * DefaultFatalHandler constructor.
     * @param DefaultListenerExceptionInterface $listener
     */
    public function __construct(DefaultListenerExceptionInterface $listener)
    {
        $this->listener = $listener;
    }
    
    /**
     * @return ApiResponseInterface
     * @throws \ReflectionException
     */
    public function handle()
    {
        /** @var BaseResponse $apiResponse */
        $apiResponse = $this->listener->getResponse()->statusError();
        $exception = $this->listener->getException();
        
        $this->addCommonInfo();
        
        if ($exception->getCode() > 0) {
            $this->statusCode = $exception->getCode();
        }
        
        if ($this->statusCode < 500 || $this->listener->isDev()) { //Http exceptions less than 500 (internal errors)
            $ex = $this->getException($exception);
            $apiResponse->addError($ex); //Own ApiResponse with correct messages
        } else {
            $apiResponse->addError(new ApiException($this->listener->getTranslator()->trans('Internal server error'), $this->statusCode));
        }
        
        return $apiResponse;
    }
    
    /**
     * @return bool
     */
    public function supportException()
    {
        return $this->listener->getException() instanceof \Throwable;
    }
}

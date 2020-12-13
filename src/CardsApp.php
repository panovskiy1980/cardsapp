<?php

/*
 * The MIT License
 *
 * Copyright 2020 Ilya Panovskiy <panovskiy1980@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace CardsApp;

use CardsApp\Base\RouterInterface;
use CardsApp\Base\RequestInterface;
use CardsApp\Base\ResponseInterface;
use CardsApp\Base\StorageSessionInterface;

/**
 * CardsApp
 *
 * @author Ilya Panovskiy <panovskiy1980@gmail.com>
 */
class CardsApp
{
    const CONTROLLER_NAMESPACE = '\\CardsApp\\Controller\\';
    const CONTROLLER_SUFFIX = 'Controller';
    const CONTROLLER_METHOD_SUFFIX = 'Action';
    
    const DEFAULT_ROUT = 'default';
    const DEFAULT_ACTION = 'index';
    
    const MESSAGE = 'message';
    const STATUS = 'status';
    
    const OK_STATUS = 'OK';
    const ERROR_STATUS = 'Error';
    
    
    /** @var RouterInterface $router */ 
    private RouterInterface $router;
    
    /** @var RequestInterface $request */ 
    private RequestInterface $request;
    
    /** @var ResponseInterface $response */ 
    private ResponseInterface $response;
    
    /** @var StorageSessionInterface $storage */
    private StorageSessionInterface $storage;


    /**
     * @param RouterInterface $router
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param StorageSessionInterface $storage
     */
    public function __construct(
        RouterInterface $router, 
        RequestInterface $request, 
        ResponseInterface $response,
        StorageSessionInterface $storage
    ) {
        $this->router = $router;
        $this->request = $request;
        $this->response = $response;
        $this->storage = $storage;
    }
    
    /**
     * Run the App
     */
    public function run()
    {
        [$controller, $method] = 
            $this->assignRout($this->router->getRoute());
        
        if (isset($controller, $method)) {
            try {
                $data[self::MESSAGE] = 
                    (new $controller($this->request, $this->storage))
                        ->$method();
                $data[self::STATUS] = self::OK_STATUS;
            } catch (\Throwable $exc) {
                $data[self::MESSAGE] = $exc->getMessage();
                $data[self::STATUS] = self::ERROR_STATUS;
            }
        } else {
            $data[self::MESSAGE] = "Page does not exist.";
            $data[self::STATUS] = self::ERROR_STATUS;
        }
        
        echo $this->response->getResponse($data);
    }

    /**
     * Assign rout
     * 
     * @param array $rout
     * @return array
     */
    private function assignRout(array $rout): array
    {
        switch (count($rout)) {
            case 2:
                $routParams = [$rout[0], $rout[1]];
                break;
            case 1:
                $routParams = [$rout[0], self::DEFAULT_ACTION];
                break;
            case 0:
                $routParams = [self::DEFAULT_ROUT, self::DEFAULT_ACTION];
                break;
            default:
                return [NULL, NULL];
        }
        
        $controllerName = $this->getControllerName($routParams[0]);
        $methodName = $this->getControllerMethodName($routParams[1]);
        
        return [$controllerName, $methodName];
    }

    /**
     * Get controller name
     * 
     * @param string $routParam
     * @return string
     */
    private function getControllerName(string $routParam): string
    {
        return self::CONTROLLER_NAMESPACE 
            . ucfirst($routParam) 
            . self::CONTROLLER_SUFFIX;
    }
    
    /**
     * Get controller method name
     * 
     * @param string $routParam
     * @return string
     */
    private function getControllerMethodName(string $routParam): string
    {
        return $routParam . self::CONTROLLER_METHOD_SUFFIX;
    }
}

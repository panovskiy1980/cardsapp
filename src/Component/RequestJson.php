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

namespace CardsApp\Component;

use CardsApp\Base\RequestInterface;

/**
 * RequestJson
 *
 * @author Ilya Panovskiy <panovskiy1980@gmail.com>
 */
class RequestJson implements RequestInterface
{
    /**
     * Get request
     * 
     * @return array
     */
    public function getRequest(): array
    {
        return $this->getRequestData();
    }
    
    /**
     * Returns a request type
     * 
     * @return string
     */
    public function getRequestType(): string
    {
        return (string) $_SERVER["REQUEST_METHOD"];
    }
    
    /**
     * Get request data
     * 
     * TODO: add filtering
     * 
     * @return array
     * @throws WrongHttpMethod
     */
    private function getRequestData(): array
    {
        switch ($this->getRequestType()) {
            case 'GET':
                return $_GET;
            case 'POST':
            case 'PUT':
            case 'DELETE':
                return $this->getJsonRequestData();
            default:
                return [];
        }
    }
    
    /**
     * Returns parsed request data
     * 
     * @return array
     */
    private function getJsonRequestData(): array
    {
        $json = json_decode(file_get_contents('php://input'), TRUE);
        
        if (empty($json)) {
            return [];
        }
        
        return $json;
    }
}

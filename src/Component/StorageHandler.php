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

use CardsApp\Base\StorageSessionInterface;

/**
 * StorageHandler
 *
 * @author Ilya Panovskiy <panovskiy1980@gmail.com>
 */
class StorageHandler implements StorageSessionInterface
{
    /**
     * Session start
     * 
     * @return bool
     */
    public function sessionStart(): bool
    {
        return session_start();
    }
    
    /**
     * Get session id
     * 
     * @return string
     */
    public function getSessionId(): string
    {
        return session_id();
    }
    
    /**
     * Get session data
     * 
     * @return array
     */
    public function getSessionData(): array
    {
        $this->bootUpIfNoSid();
        
        $sid = $this->getSessionId();
        
        if (isset($_SESSION[$sid])) {
            return $_SESSION[$sid];
        }
        
        return [];
    }
    
    /**
     * Set session data
     * 
     * @param array $data
     * @return void
     */
    public function setSessionData(array $data): void
    {
        $this->bootUpIfNoSid();
        
        $_SESSION[$this->getSessionId()] = $data;
    }
    
    /**
     * Boot up if no sid
     * 
     * @return void
     */
    private function bootUpIfNoSid(): void
    {
        if (empty($this->getSessionId())) {
            $this->sessionStart();
        }
    }
}

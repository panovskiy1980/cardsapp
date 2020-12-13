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

namespace CardsApp\Controller;

use CardsApp\Base\AbstractController;
use CardsApp\Model\UserDeck;
use CardsApp\Model\CardsDeck;
use CardsApp\Validator\UserDeckConditionValidator as Validator;
use CardsApp\Validator\CardsDeckConditionValidator as CardsValidator;
use CardsApp\Exception\HttpMethodNotAllowedException as HttpException;

/**
 * UserController
 *
 * @author Ilya Panovskiy <panovskiy1980@gmail.com>
 */
class UserController extends AbstractController
{
    /** @var array $requestData */
    private array $requestData;

    /** @var array $storedData */
    private array $storedData;
    
    /** @var UserDeck $userDeck */
    private UserDeck $userDeck;
    
    
    /**
     * Index action
     * 
     * @return string
     * @throws HttpException
     */
    public function indexAction(): string
    {
        if ($this->request->getRequestType() != 'GET') {
            throw new HttpException('Only GET HTTP method allowed.');
        }
        
        return 'Hello';
    }
    
    /**
     * List action
     * 
     * @return array
     * @throws HttpException
     */
    public function listAction(): array
    {
        if ($this->request->getRequestType() != 'GET') {
            throw new HttpException('Only GET HTTP method allowed.');
        }
        
        $this->setParams();
        
        return $this->userDeck->listDeck($this->storedData);
    }
    
    /**
     * Create action
     * 
     * @return string
     * @throws HttpException
     */
    public function createAction(): string
    {
        if ($this->request->getRequestType() != 'POST') {
            throw new HttpException('Only POST HTTP method allowed.');
        }
        
        $this->setParams();
        
        $this->storage->setSessionData(
            $this->userDeck->createNewDeck($this->storedData)
        );
        
        return 'Deck successfully created.';
    }
    
    /**
     * Add card action
     * 
     * @return string
     * @throws HttpException
     */
    public function addcardAction(): string
    {
        if ($this->request->getRequestType() != 'PUT') {
            throw new HttpException('Only PUT HTTP method allowed.');
        }
        
        $this->setParams();
        
        $this->storage->setSessionData(
            $this->userDeck->appendCard(
                $this->storedData, 
                $this->requestData[UserDeck::CARD_NAME] ?? NULL
            )
        );
        
        return 'Card successfully added.';
    }
    
    /**
     * Remove card action
     * 
     * @return string
     * @throws HttpException
     */
    public function removecardAction(): string
    {
        if ($this->request->getRequestType() != 'DELETE') {
            throw new HttpException('Only DELETE HTTP method allowed.');
        }
        
        $this->setParams();
        
        $this->storage->setSessionData(
            $this->userDeck->removeCard(
                $this->storedData, 
                (int) ($this->requestData[UserDeck::CARD_ID] ?? NULL)
            )
        );
        
        return 'Card successfully removed.';
    }
    
    /**
     * Set parameters
     */
    private function setParams()
    {
        $this->requestData = $this->request->getRequest();
        $this->storedData = $this->storage->getSessionData();
        $this->userDeck = new UserDeck(
            new Validator(), 
            new CardsDeck(new CardsValidator())
        );
    }
}

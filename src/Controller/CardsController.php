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
use CardsApp\Model\CardsDeck;
use CardsApp\Validator\CardsDeckConditionValidator as Validator;
use CardsApp\Exception\HttpMethodNotAllowedException as HttpException;

/**
 * CardsController
 *
 * @author Ilya Panovskiy <panovskiy1980@gmail.com>
 */
class CardsController extends AbstractController
{
    /** @var array $requestData */
    private array $requestData;

    /** @var array $storedData */
    private array $storedData;
    
    /** @var CardsDeck $cardsDeck */
    private CardsDeck $cardsDeck;
    
    
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
        
        return "Examples:"
        . "/list GET /?page=1"
        . "/create POST {'name':'Card name', 'power':10}"
        . "/update PUT {'name':'Card name', 'new_name':'New Card', 'new_power':10}"
        . "/remove DELETE {'name':'Card name'}";
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
        
        return $this->cardsDeck->getListedDeck(
            $this->storedData[CardsDeck::STRUCT_NAME] ?? [],
            (int) ($this->requestData[CardsDeck::PAGE_NAME_PARAM] ?? NULL)
        );
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
        
        $this->storedData[CardsDeck::STRUCT_NAME] = 
            $this->cardsDeck->createNewCard(
                $this->storedData[CardsDeck::STRUCT_NAME] ?? [], 
                $this->requestData[CardsDeck::CARD_NAME_PARAM] ?? NULL, 
                (int) ($this->requestData[CardsDeck::POWER_NAME_PARAM] ?? NULL)
            );
        
        $this->storage->setSessionData($this->storedData);
        
        return 'Card successfully created.';
    }
    
    /**
     * Update action
     * 
     * @return string
     * @throws HttpException
     */
    public function updateAction(): string
    {
        if ($this->request->getRequestType() != 'PUT') {
            throw new HttpException('Only PUT HTTP method allowed.');
        }
        
        $this->setParams();
        
        $this->storedData[CardsDeck::STRUCT_NAME] = 
            $this->cardsDeck->updateCard(
                $this->storedData[CardsDeck::STRUCT_NAME] ?? [], 
                $this->requestData[CardsDeck::CARD_NAME_PARAM] ?? NULL, 
                $this->requestData[CardsDeck::NEW_CARD_NAME_PARAM] ?? NULL, 
                (int) 
                ($this->requestData[CardsDeck::NEW_POWER_NAME_PARAM] ?? NULL)
            );
        
        $this->storage->setSessionData($this->storedData);
        
        return 'Card successfully updated.';
    }
    
    /**
     * Remove action
     * 
     * @return string
     * @throws HttpException
     */
    public function removeAction(): string
    {
        if ($this->request->getRequestType() != 'DELETE') {
            throw new HttpException('Only DELETE HTTP method allowed.');
        }
        
        $this->setParams();
        
        $this->storedData[CardsDeck::STRUCT_NAME] = 
            $this->cardsDeck->removeCard(
                $this->storedData[CardsDeck::STRUCT_NAME], 
                $this->requestData[CardsDeck::CARD_NAME_PARAM] ?? NULL
            );
        
        $this->storage->setSessionData($this->storedData);
        
        return 'Card successfully removed.';
    }
    
    /**
     * Set parameters
     */
    private function setParams()
    {
        $this->requestData = $this->request->getRequest();
        $this->storedData = $this->storage->getSessionData();
        $this->cardsDeck = new CardsDeck(new Validator());
    }
}

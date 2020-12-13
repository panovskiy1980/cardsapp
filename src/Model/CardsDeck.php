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

namespace CardsApp\Model;

use CardsApp\Validator\CardsDeckConditionValidator as Validator;
use CardsApp\Exception\CheckDataException;

/**
 * CardsDeck
 *
 * @author Ilya Panovskiy <panovskiy1980@gmail.com>
 */
class CardsDeck
{
    const STRUCT_NAME = 'cards_deck';
    
    const CARD_NAME_PARAM = 'name';
    const NEW_CARD_NAME_PARAM = 'new_name';
    const POWER_NAME_PARAM = 'power';
    const NEW_POWER_NAME_PARAM = 'new_power';
    
    const PAGE_NAME_PARAM = 'page';
    const ITEMS_PER_PAGE = 3;
    
    const PERMANENT_DECK = [
        'Geralt' => 10,
        'Ciri' => 9,
        'Vesemir' => 5,
        'Triss' => 3,
        'Aard sign' => 0
    ];
    
    /** @var Validator $validator */
    private Validator $validator;

    
    /**
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Get compiled deck
     * 
     * @param array $storedDeck
     * @param int|null $page
     * @return array
     */
    public function getCompiledDeck(array $storedDeck): array
    {
        return array_merge(self::PERMANENT_DECK, $storedDeck);
    }
    
    /**
     * Initiate the deck
     * 
     * @param array $storedData
     * @return array
     */
    public function initDeck(array $storedData): array
    {
        if (
            !isset($storedData[self::STRUCT_NAME]) 
            || 
            empty($storedData[self::STRUCT_NAME])
        ) {
            $storedData[self::STRUCT_NAME] = $this->getCompiledDeck([]);
        }
        
        return $storedData;
    }

    /**
     * Get listed deck
     * 
     * @param array $storedDeck
     * @param int|null $page
     * @return array
     * @throws CheckDataException
     */
    public function getListedDeck(array $storedDeck, ?int $page): array
    {
        $this->validator->pageCondValidate($page);
        
        $listedDeck = array_slice(
            $this->getCompiledDeck($storedDeck), 
            ($page - 1) * self::ITEMS_PER_PAGE, 
            self::ITEMS_PER_PAGE, 
            TRUE
        );
        
        $this->validator->listedDeckCondValidate($listedDeck);
        
        return $listedDeck;
    }

    /**
     * Create new card
     * 
     * @param array $storedDeck
     * @param string|null $name
     * @param int|null $power
     * @return array
     * @throws CheckDataException
     */
    public function createNewCard(
        array $storedDeck, 
        ?string $name, 
        ?int $power
    ): array {
        $cardDeck = $this->getCompiledDeck($storedDeck);
        
        $this->validator->createCondValidate($cardDeck, $name, $power);
        
        $cardDeck[$name] = $power;
        
        return $cardDeck;
    }
    
    /**
     * Update card
     * 
     * @param array $storedDeck
     * @param string|null $name
     * @param string|null $newName
     * @param int|null $newPower
     * @return array
     * @throws CheckDataException
     */
    public function updateCard(
        array $storedDeck, 
        ?string $name, 
        ?string $newName, 
        ?int $newPower
    ): array {
        $cardDeck = $this->getCompiledDeck($storedDeck);
        
        $this->validator
            ->updateCondValidate($cardDeck, $name, $newName, $newPower);
        
        unset($cardDeck[$name]);

        $newName = $newName ?? $name;
        $newPower = $newPower ?? $cardDeck[$name];

        $cardDeck[$newName] = $newPower;
        
        return $cardDeck;
    }
    
    /**
     * Remove card
     * 
     * @param array $storedDeck
     * @param string|null $name
     * @return array
     * @throws CheckDataException
     */
    public function removeCard(array $storedDeck, ?string $name): array
    {
        $cardDeck = $this->getCompiledDeck($storedDeck);
        
        $this->validator->removeCondValidate($cardDeck, $name);
        
        unset($cardDeck[$name]);
        
        return $cardDeck;
    }
}

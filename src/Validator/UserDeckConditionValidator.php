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

namespace CardsApp\Validator;

use CardsApp\Model\UserDeck;
use CardsApp\Model\CardsDeck;
use CardsApp\Exception\CheckDataException;

/**
 * UserDeckConditionValidator
 *
 * @author Ilya Panovskiy <panovskiy1980@gmail.com>
 */
class UserDeckConditionValidator
{
    /**
     * List conditions validation
     * 
     * @param array $storedData
     * @return void
     * @throws CheckDataException
     */
    public function listCondValidate(array $storedData): void
    {
        if (!isset($storedData[UserDeck::USER_DECK_STRUCT_NAME])) {
            throw new CheckDataException('Deck does not exist.');
        }
    }
    
    /**
     * Create conditions validation
     * 
     * @param array $storedData
     * @return void
     * @throws CheckDataException
     */
    public function createCondValidate(array $storedData): void
    {
        if (isset($storedData[UserDeck::USER_DECK_STRUCT_NAME])) {
            throw new CheckDataException('Deck already exists.');
        }
    }
    
    /**
     * Append conditions validation
     * 
     * @param array $storedData
     * @param string|null $cardName
     * @throws CheckDataException
     */
    public function appendCondValidate(array $storedData, ?string $cardName)
    {
        if (!isset($storedData[UserDeck::USER_DECK_STRUCT_NAME])) {
            throw new CheckDataException('Deck does not exist.');
        }
        
        if (!isset($cardName)) {
            throw new CheckDataException(
                UserDeck::CARD_NAME . 'parameter should be present.'
            );
        }
        
        if (!isset($storedData[CardsDeck::STRUCT_NAME][$cardName]))
        {
            throw 
                new CheckDataException('Card does not exist in the main deck.');
        }
        
        $innerDeck = 
            $storedData[UserDeck::USER_DECK_STRUCT_NAME][UserDeck::INNER_DECK_NAME];
        
        if (count($innerDeck) >= UserDeck::CARDS_IN_DECK_LIMIT) {
            throw new CheckDataException(
                'There can not be more than ' . 
                UserDeck::CARDS_IN_DECK_LIMIT . ' in the deck.'
            );
        }
        
        foreach ($innerDeck as $value) {
            $gatherNames[] = array_keys($value)[0];
        }
        
        $counted = isset($gatherNames) ? array_count_values($gatherNames) : NULL;
        
        if (
            isset($counted[$cardName]) 
            && 
            $counted[$cardName] >= UserDeck::CARDS_INSTANCES_ALLOWED
        ) {
            throw new CheckDataException(
                $cardName . ' can not be added more than twice.'
            );
        }
    }
    
    /**
     * Remove conditions validation
     * 
     * @param array $storedData
     * @param int|null $cardId
     * @throws CheckDataException
     */
    public function removeCondValidate(array $storedData, ?int $cardId)
    {
        if (!isset($cardId)) {
            throw new CheckDataException(
                UserDeck::CARD_ID . 'parameter should be present.'
            );
        }
        
        if (!isset($storedData[UserDeck::USER_DECK_STRUCT_NAME][UserDeck::INNER_DECK_NAME][$cardId])) {
            throw new CheckDataException('Card does not exist.');
        }
    }
}

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

use CardsApp\Model\CardsDeck;
use CardsApp\Exception\CheckDataException;

/**
 * CardsDeckConditionValidator
 *
 * @author Ilya Panovskiy <panovskiy1980@gmail.com>
 */
class CardsDeckConditionValidator
{
    /**
     * Page conditions validation
     * 
     * @param int|null $page
     * @return void
     * @throws CheckDataException
     */
    public function pageCondValidate(?int $page): void
    {
        switch ($page <=> 0) {
            case -1: 
            case 0:
                throw new CheckDataException('Page can not be lesser than 1.');
        }
    }
    
    /**
     * Listed deck conditions validation
     * 
     * @param array $listedDeck
     * @return void
     * @throws CheckDataException
     */
    public function listedDeckCondValidate(array $listedDeck): void
    {
        if (empty($listedDeck)) {
            throw new CheckDataException('Page overreached.');
        }
    }

    /**
     * Create conditions validation
     * 
     * @param array $cardDeck
     * @param string|null $name
     * @param int|null $power
     * @return void
     * @throws CheckDataException
     */
    public function createCondValidate(
        array $cardDeck, 
        ?string $name, 
        ?int $power
    ): void {
        if (!isset($name, $power)) {
            throw new CheckDataException(
                'Parameters ' . CardsDeck::CARD_NAME_PARAM . ' and ' 
                . CardsDeck::POWER_NAME_PARAM . ' must be present.'
            );
        }
        
        if (in_array($name, array_keys($cardDeck))) {
            throw new CheckDataException(
                'Card with name ' . $name . ' already present in deck.'
            );
        }
    }
    
    /**
     * Update conditions validation
     * 
     * @param array $cardDeck
     * @param string|null $name
     * @param string|null $newName
     * @param int|null $newPower
     * @return void
     * @throws CheckDataException
     */
    public function updateCondValidate(
        array $cardDeck, 
        ?string $name, 
        ?string $newName, 
        ?int $newPower
    ): void {
        if (!isset($name)) {
            throw new CheckDataException(
                'Parameter ' . CardsDeck::CARD_NAME_PARAM . ' must be present.'
            );
        }
        
        if (!isset($newName) && !isset($newPower)) {
            throw new CheckDataException(
                'Parameters ' . CardsDeck::NEW_CARD_NAME_PARAM . ' or ' 
                . CardsDeck::NEW_POWER_NAME_PARAM . ' must be present.'
            );
        }
        
        if (in_array($name, array_keys(CardsDeck::PERMANENT_DECK))) {
            throw new CheckDataException(
                'Card with name ' . $name . ' can not be updated. '
                . 'It belongs to permanent deck.'
            );
        }
        
        if (in_array($newName, array_keys($cardDeck))) {
            throw new CheckDataException(
                'Card with name ' . $newName . ' already present in deck.'
            );
        }
        
        if (!in_array($name, array_keys($cardDeck))) {
            throw new CheckDataException(
                'Card with name ' . $name . ' not present in deck.'
            );
        }
    }
    
    /**
     * Remove conditions validation
     * 
     * @param array $cardDeck
     * @param string|null $name
     * @return void
     * @throws CheckDataException
     */
    public function removeCondValidate(array $cardDeck, ?string $name): void
    {
        if (!isset($name)) {
            throw new CheckDataException(
                'Parameter ' . CardsDeck::CARD_NAME_PARAM . ' must be present.'
            );
        }
        
        if (in_array($name, array_keys(CardsDeck::PERMANENT_DECK))) {
            throw new CheckDataException(
                'Card with name ' . $name . 
                ' can not be removed from permanent deck.'
            );
        }
        
        if (!in_array($name, array_keys($cardDeck))) {
            throw new CheckDataException(
                'Card with name ' . $name . ' does not exist.'
            );
        }
    }
}

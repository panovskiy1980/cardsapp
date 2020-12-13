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

use CardsApp\Model\CardsDeck;
use CardsApp\Validator\UserDeckConditionValidator as Validator;
use CardsApp\Exception\CheckDataException;

/**
 * UserDeck
 *
 * @author Ilya Panovskiy <panovskiy1980@gmail.com>
 */
class UserDeck
{
    const CARDS_IN_DECK_LIMIT = 10;
    const CARDS_INSTANCES_ALLOWED = 2;
    const USER_DECK_STRUCT_NAME = 'user_deck';
    const INNER_DECK_NAME = 'deck';
    const TOTAL_POWER = 'total_power';
    const CARD_NAME = 'card_name';
    const CARD_ID = 'card_id';
    
    /** @var Validator $validator */
    private Validator $validator;
    
    /** @var CardsDeck $cardsDeck */
    private CardsDeck $cardsDeck;

    
    /**
     * @param Validator $validator
     * @param CardsDeck $cardsDeck
     */
    public function __construct(Validator $validator, CardsDeck $cardsDeck)
    {
        $this->validator = $validator;
        $this->cardsDeck = $cardsDeck;
    }
    
    /**
     * List deck
     * 
     * @param array $storedData
     * @return array
     * @throws CheckDataException
     */
    public function listDeck(array $storedData): array
    {
        $this->validator->listCondValidate($storedData);
        
        return $storedData[self::USER_DECK_STRUCT_NAME];
    }
    
    /**
     * Create new deck
     * 
     * @param array $storedData
     * @return array
     * @throws CheckDataException
     */
    public function createNewDeck(array $storedData): array
    {
        $this->validator->createCondValidate($storedData);
        
        $storedData[self::USER_DECK_STRUCT_NAME][self::INNER_DECK_NAME] = [];
        $storedData[self::USER_DECK_STRUCT_NAME][self::TOTAL_POWER] = 0;
        
        return $storedData;
    }
    
    /**
     * Append card
     * 
     * @param array $storedData
     * @param string|null $cardName
     * @return array
     * @throws CheckDataException
     */
    public function appendCard(array $storedData, ?string $cardName): array
    {
        $storedData = $this->cardsDeck->initDeck($storedData);
        
        $this->validator->appendCondValidate($storedData, $cardName);
        
        $storedData[self::USER_DECK_STRUCT_NAME][self::INNER_DECK_NAME][] = 
            [$cardName => $storedData[CardsDeck::STRUCT_NAME][$cardName]];
        $storedData[self::USER_DECK_STRUCT_NAME][self::TOTAL_POWER] = 
            $this->getTotalPower(
                $storedData[self::USER_DECK_STRUCT_NAME][self::INNER_DECK_NAME]
            );
        
        return $storedData;
    }
    
    /**
     * Remove card
     * 
     * @param array $storedData
     * @param int|null $cardId
     * @return array
     * @throws CheckDataException
     */
    public function removeCard(array $storedData, ?int $cardId): array
    {
        $this->validator->removeCondValidate($storedData, $cardId);
        
        unset($storedData[self::USER_DECK_STRUCT_NAME][self::INNER_DECK_NAME][$cardId]);
        
        $storedData[self::USER_DECK_STRUCT_NAME][self::TOTAL_POWER] = 
            $this->getTotalPower(
                $storedData[self::USER_DECK_STRUCT_NAME][self::INNER_DECK_NAME]
            );
        
        return $storedData;
    }
    
    /**
     * Return total power
     * 
     * @param array $userDeck
     * @return int
     */
    private function getTotalPower(array $userDeck): int
    {
        $allPower = 0;
        
        foreach ($userDeck as $value) {
            $allPower += array_values($value)[0];
        }
        
        return $allPower;
    }
}

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

namespace Tests\CardsAppTest\ModelTest;

use PHPUnit\Framework\TestCase;
use CardsApp\Model\CardsDeck;
use CardsApp\Validator\CardsDeckConditionValidator as Validator;
use CardsApp\Exception\CheckDataException;
use Iterator;

/**
 * CardsDeckTest
 *
 * @author Ilya Panovskiy <panovskiy1980@gmail.com>
 */
class CardsDeckTest extends TestCase
{
    /** @var CardsDeck $cardsDeck */
    private CardsDeck $cardsDeck;
    
    
    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->cardsDeck = new CardsDeck(new Validator());
    }
    
    /**
     * @dataProvider storedDeckCompileProvider
     * @param array $storedDeck
     * @param array $expected
     * @return void
     */
    public function testGetCompiledDeck(
        array $storedDeck, 
        array $expected
    ): void {
        $actual = $this->cardsDeck->getCompiledDeck($storedDeck);
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @return Iterator
     */
    public function storedDeckCompileProvider(): Iterator
    {
        yield 'empty data' => [
            [],
            CardsDeck::PERMANENT_DECK
        ];
        yield 'stored data' => [
            [
                'Card1' => 1, 
                'Card2' => 2,
                'Card3' => 3
            ],
            CardsDeck::PERMANENT_DECK + [
                'Card1' => 1, 
                'Card2' => 2,
                'Card3' => 3
            ]
        ];
    }
    
    /**
     * @dataProvider storedDeckInitProvider
     * @param array $storedDeck
     * @param array $expected
     * @return void
     */
    public function testInitDeck(array $storedDeck, array $expected): void
    {
        $actual = $this->cardsDeck->initDeck($storedDeck);
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @return Iterator
     */
    public function storedDeckInitProvider(): Iterator
    {
        yield 'empty data' => [
            [],
            [
                CardsDeck::STRUCT_NAME => CardsDeck::PERMANENT_DECK
            ]
        ];
        yield 'stored data' => [
            [
                CardsDeck::STRUCT_NAME => CardsDeck::PERMANENT_DECK + 
                [
                    'Card1' => 1,
                    'Card2' => 2,
                    'Card3' => 3
                ]
            ],
            [
                CardsDeck::STRUCT_NAME => CardsDeck::PERMANENT_DECK + 
                [
                    'Card1' => 1,
                    'Card2' => 2,
                    'Card3' => 3
                ]
            ]
        ];
    }
    
    /**
     * @dataProvider storedDeckListProvider
     * @param array $storedDeck
     * @param int|null $page
     * @param array $expected
     * @return void
     */
    public function testGetListedDeck(
        array $storedDeck, 
        ?int $page, 
        array $expected
    ): void {
        $actual = $this->cardsDeck->getListedDeck($storedDeck, $page);
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @return Iterator
     */
    public function storedDeckListProvider(): Iterator
    {
        yield 'page 1' => [
            CardsDeck::PERMANENT_DECK,
            1,
            array_chunk(CardsDeck::PERMANENT_DECK, 3, TRUE)[0]
        ];
        yield 'page 2' => [
            CardsDeck::PERMANENT_DECK,
            2,
            array_chunk(CardsDeck::PERMANENT_DECK, 3, TRUE)[1]
        ];
    }
    
    /**
     * @dataProvider storedDeckListExceptProvider
     * @param array $storedDeck
     * @param int|null $page
     * @return void
     */
    public function testGetListedDeckException(
        array $storedDeck, 
        ?int $page
    ): void {
        $this->expectException(CheckDataException::class);
        
        $this->cardsDeck->getListedDeck($storedDeck, $page);
    }
    
    /**
     * @return Iterator
     */
    public function storedDeckListExceptProvider(): Iterator
    {
        yield 'page -1' => [
            CardsDeck::PERMANENT_DECK,
            -1
        ];
        yield 'page 0' => [
            CardsDeck::PERMANENT_DECK,
            0
        ];
        yield 'page 3' => [
            CardsDeck::PERMANENT_DECK,
            3
        ];
    }
    
    /**
     * @dataProvider storedDeckCreateProvider
     * @param array $storedDeck
     * @param string|null $name
     * @param int|null $power
     * @param array $expected
     * @return void
     */
    public function testCreateNewCard(
        array $storedDeck, 
        ?string $name, 
        ?int $power, 
        array $expected
    ): void {
        $actual = $this->cardsDeck->createNewCard($storedDeck, $name, $power);
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @return Iterator
     */
    public function storedDeckCreateProvider(): Iterator
    {
        yield 'success 1' => [
            [],
            'New Card',
            10,
            CardsDeck::PERMANENT_DECK + ['New Card' => 10]
        ];
        yield 'success 2' => [
            ['Some Card' => 5],
            'New Card',
            10,
            CardsDeck::PERMANENT_DECK + ['Some Card' => 5] + ['New Card' => 10]
        ];
    }
    
    /**
     * @dataProvider storedDeckCreateExceptProvider
     * @param array $storedDeck
     * @param string|null $name
     * @param int|null $power
     * @return void
     */
    public function testCreateNewCardException(
        array $storedDeck, 
        ?string $name, 
        ?int $power
    ): void {
        $this->expectException(CheckDataException::class);
        
        $this->cardsDeck->createNewCard($storedDeck, $name, $power);
    }
    
    /**
     * @return Iterator
     */
    public function storedDeckCreateExceptProvider(): Iterator
    {
        yield 'failure 1' => [
            [],
            NULL,
            NULL
        ];
        yield 'failure 2' => [
            [],
            'New Card',
            NULL
        ];
        yield 'failure 3' => [
            [],
            NULL,
            10
        ];
        yield 'failure 4' => [
            ['New Card' => 5],
            'New Card',
            10
        ];
    }
    
    /**
     * @dataProvider storedDeckUpdateProvider
     * @param array $storedDeck
     * @param string|null $name
     * @param string|null $newName
     * @param int|null $newPower
     * @param array $expected
     * @return void
     */
    public function testUpdateCard(
        array $storedDeck, 
        ?string $name, 
        ?string $newName, 
        ?int $newPower, 
        array $expected
    ): void {
        $actual = $this->cardsDeck->updateCard(
            $storedDeck, 
            $name, 
            $newName, 
            $newPower
        );
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @return Iterator
     */
    public function storedDeckUpdateProvider(): Iterator
    {
        yield 'success 1' => [
            ['Some Card' => 5],
            'Some Card',
            NULL,
            10,
            CardsDeck::PERMANENT_DECK + ['Some Card' => 10]
        ];
        yield 'success 2' => [
            ['Some Card' => 5],
            'Some Card',
            'New Card',
            NULL,
            CardsDeck::PERMANENT_DECK + ['New Card' => 5]
        ];
        yield 'success 3' => [
            ['Some Card' => 5],
            'Some Card',
            'New Card',
            10,
            CardsDeck::PERMANENT_DECK + ['New Card' => 10]
        ];
    }
    
    /**
     * @dataProvider storedDeckUpdateExceptProvider
     * @param array $storedDeck
     * @param string|null $name
     * @param string|null $newName
     * @param int|null $newPower
     * @return void
     */
    public function testUpdateCardException(
        array $storedDeck, 
        ?string $name, 
        ?string $newName, 
        ?int $newPower
    ): void {
        $this->expectException(CheckDataException::class);
        
        $this->cardsDeck->updateCard($storedDeck, $name, $newName, $newPower);
    }
    
    /**
     * @return Iterator
     */
    public function storedDeckUpdateExceptProvider(): Iterator
    {
        yield 'failure 1' => [
            ['Some Card' => 5],
            'Some Card',
            NULL,
            NULL
        ];
        yield 'failure 2' => [
            ['Some Card' => 5],
            NULL,
            'New Card',
            NULL
        ];
        yield 'failure 3' => [
            ['Some Card' => 5],
            NULL,
            NULL,
            10
        ];
        yield 'failure 4' => [
            ['Some Card' => 5],
            NULL,
            NULL,
            NULL
        ];
        $deck = CardsDeck::PERMANENT_DECK;
        yield 'failure 5' => [
            [],
            array_key_first($deck),
            array_key_first($deck),
            10
        ];
        yield 'failure 6' => [
            ['Some Card' => 5],
            'Some Card',
            'Some Card',
            NULL
        ];
        yield 'failure 7' => [
            ['Some Card' => 5],
            'False Card',
            'New Card',
            NULL
        ];
    }
    
    /**
     * @dataProvider storedDeckRemoveProvider
     * @param array $storedDeck
     * @param string|null $name
     * @param array $expected
     * @return void
     */
    public function testRemoveCard(
        array $storedDeck, 
        ?string $name, 
        array $expected
    ): void {
        $actual = $this->cardsDeck->removeCard($storedDeck, $name);
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @return Iterator
     */
    public function storedDeckRemoveProvider(): Iterator
    {
        yield 'success' => [
            ['Some Card' => 5],
            'Some Card',
            CardsDeck::PERMANENT_DECK
        ];
    }
    
    /**
     * @dataProvider storedDeckRemoveExceptProvider
     * @param array $storedDeck
     * @param string|null $name
     * @return void
     */
    public function testRemoveCardException(
        array $storedDeck, 
        ?string $name
    ): void {
        $this->expectException(CheckDataException::class);
        
        $this->cardsDeck->removeCard($storedDeck, $name);
    }
    
    /**
     * @return Iterator
     */
    public function storedDeckRemoveExceptProvider(): Iterator
    {
        yield 'failure 1' => [
            ['Some Card' => 5],
            NULL
        ];
        $deck = CardsDeck::PERMANENT_DECK;
        yield 'failure 2' => [
            ['Some Card' => 5],
            array_key_first($deck)
        ];
        yield 'failure 3' => [
            ['Some Card' => 5],
            'False card'
        ];
    }
}

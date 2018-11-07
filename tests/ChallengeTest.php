<?php

declare(strict_types=1);

use HybridAnalysis\Challenge;
use PHPUnit\Framework\TestCase;

class ChallengeTest extends TestCase
{
    /**
     * @var Challenge
     */
    private $challenge;

    public function setUp()
    {
        parent::setUp();

        $this->challenge = new Challenge();
    }

    public function bigLetterShortcutProvider(): array
    {
        return [
            ['Test Me Please', 'TMP'],
            ['TEst me please', 'TES'],
            ['Tst Me please', 'TMS'],
            ['tEst Me please', 'EMT'],
            ['test Me please', 'MTE'],
            ['test me please', 'TES'],
            ['test me pleasE', 'ETE'],
        ];
    }

    public function setOfLettersProvider(): array
    {
        return [
            [['Aa', 'aaa', 'aaaaa', 'BbBb', 'Aaaa', 'AaAaAa', 'a'], 'BbBb'],
            [['abc', 'acb', 'bac', 'foo', 'bca', 'cab', 'cba'], 'foo'],
            [['silvia', 'vasili', 'victor'], 'victor'],
            [['Tom Marvolo Riddle', 'I am Lord Voldemort', 'Harry Potter'], 'Harry Potter'],
            [['     ', 'a', ' '], 'a'],
        ];
    }

    /**
     * @dataProvider bigLetterShortcutProvider
     *
     * @param string $input
     * @param string $output
     */
    public function testMakeBigLetterShortcut(string $input, string $output): void
    {
        $this->assertEquals($output, $this->challenge->makeBigLetterShortcut($input));
    }

    public function testSortWords(): void
    {
        $this->assertEquals('Thi1s is2 4a T7est', $this->challenge->sortWords('is2 Thi1s T7est 4a'));
    }

    /**
     * @dataProvider setOfLettersProvider
     *
     * @param array  $set
     * @param string $unique
     */
    public function testFindUnique(array $set, string $unique): void
    {
        $this->assertEquals($unique, $this->challenge->findUnique($set));
    }

    public function testSortingKey(): void
    {
        $this->assertEquals(str_split('Cae12'), $this->challenge->sortKey(str_split('2e1Ca')));
    }

    public function testCalculatingNumericKey(): void
    {
        $this->assertEquals(str_split('45231'), $this->challenge->calculateNumericKey(str_split('2e1Ca'), str_split('Cae12')));
    }

    public function testEncodeMessage(): void
    {
        $this->assertEquals('ecrseonftiiatrm   on', $this->challenge->encodeMessage('secretinformation', '2e1Ca'));
    }
}
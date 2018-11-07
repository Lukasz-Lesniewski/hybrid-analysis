<?php

declare(strict_types=1);

namespace HybridAnalysis;

class Challenge
{
    /**
     * Write a function, which is taking one parameter(string, without any restriction to chars). From that string,
     * you should take all big letters and build from it the new one. When there is not enough of big letters or they
     * are missing, you should take the small ones instead. The returned string should contain only three big letters.
     *
     * @param string $letters
     *
     * @return string
     */
    public function makeBigLetterShortcut(string $letters): string
    {
        $newBigLetters = ''; // Big letters to be returned
        $newSmallLetters = ''; // Candidates if big letters will be missing
        $onlyLetters = preg_replace('/[^\w]/', '', $letters); // Cut out other chars

        foreach (str_split($onlyLetters) as $letter) {
            if (\strlen($newBigLetters) > 3) {
                break; // We already found output
            }

            if (mb_strtoupper($letter) === $letter) {
                $newBigLetters .= $letter;
                continue; // Go for the next letter
            }

            if (\strlen($newSmallLetters) < 3) {
                $newSmallLetters .= $letter;
            }
        }

        if (3 <= \strlen($newBigLetters)) {
            return substr($newBigLetters, 0, 3);
        }

        return $newBigLetters.strtoupper(substr($newSmallLetters, 0, 3 - \strlen($newBigLetters)));
    }

    /**
     * Your task is to sort a given string. Each word in the string will contain a single number. This number is the
     * position the word should have in the result.
     *
     * @param string $unsorted
     *
     * @return string
     */
    public function sortWords(string $unsorted): string
    {
        $data = [];

        foreach (explode(' ', $unsorted) as $word) {
            $number = (int)filter_var($word, FILTER_SANITIZE_NUMBER_INT);

            $data[$number] = $word;
        }

        ksort($data);

        return implode(' ', $data);
    }

    /**
     * There is an array of strings. All strings contains similar letters except one. Try to find it!
     * Strings may contain spaces. Spaces is not significant, only non-spaces symbols matters. E.g. string that contains
     * only spaces is like empty string.
     * It's guaranteed that array contains more than 3 strings.
     *
     * @param array $setOfLetters
     *
     * @return string
     */
    public function findUnique(array $setOfLetters): string
    {
        $indexStorage = [];
        $dataCounter = [];

        foreach ($setOfLetters as $index => $set) {
            $standardizedSet = $this->standardizeSetOfLetters($set);

            $indexStorage[$standardizedSet] = $index;

            if (!isset($dataCounter[$standardizedSet])) {
                $dataCounter[$standardizedSet] = 1;
            } else {
                $dataCounter[$standardizedSet]++;
            }
        }

        asort($dataCounter);

        return $setOfLetters[$indexStorage[key($dataCounter)]];
    }

    /**
     * @param string $set
     *
     * @return string
     */
    protected function standardizeSetOfLetters(string $set): string
    {
        $comparisonSet = preg_replace('/[^\w]/', '', $set); // Remove non significant symbols
        $comparisonSet = strtolower($comparisonSet); // Unify alphabet to not care about letter sizes
        $comparisonSet = str_split($comparisonSet); // Split per letter basis
        $comparisonSet = array_unique($comparisonSet); // Remove letter duplicates

        sort($comparisonSet);

        return implode('', $comparisonSet); // Grab letters together
    }

    /**
     * Encode the message based on the key that was passed
     *
     * @param string $message
     * @param string $key
     *
     * @return string
     */
    public function encodeMessage(string $message, string $key): string
    {
        $splitKey = str_split($key);

        $sortedKey = $this->sortKey($splitKey);
        $numericKey = $this->calculateNumericKey($splitKey, $sortedKey);

        $iterator = 0;
        $chunkSize = \strlen($key) - 1;
        $rowNumber = 0;
        $encoding = [];

        foreach (str_split($message) as $char) {
            if (!isset($encoding[$rowNumber])) {
                $encoding[$rowNumber] = [];
            }

            $encoding[$rowNumber][$numericKey[$iterator] - 1] = $char;

            $iterator++;

            if ($iterator > $chunkSize) {
                ksort($encoding[$rowNumber]);
                $iterator = 0;
                $rowNumber++;
            }
        }

        $encodedMessage = '';

        // Fill up missing places that can happen on last row
        $lastRow = \count($encoding) - 1;
        if (\count($encoding[$lastRow]) < $chunkSize + 1) {
            for ($i = 0; $i < $chunkSize; $i++) {
                if (!isset($encoding[$lastRow][$i])) {
                    $encoding[$lastRow][$i] = ' ';
                }
            }
            ksort($encoding[$lastRow]);
        }

        foreach ($encoding as $row) {
            foreach ($row as $char) {
                $encodedMessage .= $char;
            }
        }

        return $encodedMessage;
    }

    /**
     * Special sort with order of big letters, small letters and numbers at the end
     *
     * @param array $splitKey
     *
     * @return array
     */
    public function sortKey(array $splitKey): array
    {
        usort(
            $splitKey,
            function (string $left, string $right) {
                if (is_numeric($left) && is_numeric($right)) {
                    return $left >= $right;
                }

                if (!is_numeric($left) && !is_numeric($right)) {
                    return $left >= $right;
                }

                if (is_numeric($left)) {
                    return 1;
                }

                if (is_numeric($right)) {
                    return 1;
                }
            }
        );

        return $splitKey;
    }

    /**
     * Calculate numeric key based on the original key and it's sorted version
     *
     * @param array $splitKey
     * @param array $sortedKey
     *
     * @return array
     */
    public function calculateNumericKey(array $splitKey, array $sortedKey): array
    {
        $numericKey = [];
        $flippedKey = array_flip($splitKey);

        foreach ($sortedKey as $char) {
            $numericKey[] = ($flippedKey[$char] + 1);
        }

        return $numericKey;
    }
}
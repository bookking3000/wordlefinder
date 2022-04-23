<?php

namespace App\Tool;

use App\Entity\Request;

class ComplexityCalculator
{

    /**
     * Complexity is higher when the Response is longer, means more specific queries are not as complex as more general ones.
     */

    const CONSONANTS = 'bcdfghjklmnpqrstvwxz';
    const VOWELS = 'aeiouy';
    const UMLAUTS = 'äöü';

    protected function getComplexityMap(): array
    {
        return [
            3 => mb_str_split(self::UMLAUTS), //As Umlauts are very specific, they lower the complexity.
            6 => mb_str_split(self::VOWELS), //As Vowels are usually good Response-Reducers, they lower the complexity.
            20 => mb_str_split(self::CONSONANTS), //As Consonants are not very specific, they get a higher complexity.
            100 => mb_str_split('_'), //FuzzySearch gets the highest complexity.
        ];
    }

    public function calculateRequestComplexity(Request $wordSolverRequest, bool $fullInvestigate = true): int
    {
        $complexity = 0;
        $complexity += $this->calculateWordExpressionComplexity($wordSolverRequest);

        if (!$fullInvestigate) {
            return $complexity;
        }

        $complexity -= $this->calculateMustHaveCharComplexity($wordSolverRequest);
        $complexity -= $this->calculateForbiddenCharComplexity($wordSolverRequest);
        $complexity -= $this->calculateForbiddenCharIndexedComplexity($wordSolverRequest);

        return $complexity;
    }

    protected function calculateWordExpressionComplexity(Request $wordSolverRequest): int
    {
        $wordExpression = $wordSolverRequest->getWordExpression();
        $complexityMap = $this->getComplexityMap();

        return $this->investigate($complexityMap, $wordExpression);
    }

    protected function calculateMustHaveCharComplexity(Request $wordSolverRequest): int
    {
        $forbiddenChars = $wordSolverRequest->getMustHaveChars();
        $complexityMap = $this->getComplexityMap();

        return $this->investigate($complexityMap, $forbiddenChars, true);
    }

    protected function calculateForbiddenCharComplexity(Request $wordSolverRequest): int
    {
        $forbiddenChars = $wordSolverRequest->getForbiddenChars();
        $complexityMap = $this->getComplexityMap();

        return $this->investigate($complexityMap, $forbiddenChars, true);
    }

    protected function calculateForbiddenCharIndexedComplexity(Request $wordSolverRequest): int
    {
        $forbiddenChars = $wordSolverRequest->getIndexedForbiddenQueryExpression();
        $complexityMap = $this->getComplexityMap();

        $complexity = 0;
        foreach ($forbiddenChars as $forbiddenChar) {
            $charsToInvestigate = mb_str_split($forbiddenChar);
            foreach ($charsToInvestigate as $charToInvestigate) {
                //As this is an indexed query, it is weighted with 0.5.
                $complexity += round($this->investigate($complexityMap, $charToInvestigate) * 0.5);
            }
        }
        return $complexity;
    }

    protected function investigate(array $complexityMap, ?string $stringToExamine, bool $makeUnique = false): int
    {
        if ($makeUnique){
           $stringToExamine = implode('',array_unique(mb_str_split($stringToExamine)));
        }

        $complexity = 0;
        foreach ($complexityMap as $mappedComplexity => $characters) {
            foreach ($characters as $character) {
                $count = mb_substr_count($stringToExamine, $character);
                $complexity += $mappedComplexity * $count;
            }
        }
        return $complexity;
    }

}
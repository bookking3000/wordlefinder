<?php

namespace App\Tests;

use App\Entity\Request;
use App\Tool\ComplexityCalculator;
use PHPUnit\Framework\TestCase;

class ComplexityCalculatorTest extends TestCase
{

    public function testBasicWordExprComplexity(): void
    {
        $complexityCalculator = new ComplexityCalculator();
        $this->assertInstanceOf(ComplexityCalculator::class, $complexityCalculator);

        $request = new Request();
        $request->setWordExpression('_üs__');
        $request->setForbiddenChars('xyzabcc');
        $request->setIndexedForbiddenQueryExpression([5 => 'üä']);

        $a = $complexityCalculator->calculateRequestComplexity($request, false);
        $this->assertEquals(323, $a);

        //XYZABC now is forbidden and lowers complexity.
        $a = $complexityCalculator->calculateRequestComplexity($request,);
        $this->assertEquals(227, $a);
    }
}

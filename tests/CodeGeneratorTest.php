<?php

namespace IsaEken\HttpSnippet\Tests;

use IsaEken\HttpSnippet\CodeGenerator;
use PHPUnit\Framework\TestCase;

class CodeGeneratorTest extends TestCase
{
    public function testAddLineAndToString(): void
    {
        $codeGenerator = new CodeGenerator(1);

        $codeGenerator->addLine('echo "Hello, World!";', 1);
        $codeGenerator->addLine('return 0;');

        $codeGenerator->line_ending = "\n";
        $codeGenerator->intents = 4;
        $codeGenerator->intent_char = " ";

        $expectedResult = "        echo \"Hello, World!\";\n    return 0;\n";

        $this->assertEquals($expectedResult, $codeGenerator->toString());
    }

    public function testAddLinesAndToArray(): void
    {
        $codeGenerator = new CodeGenerator(0);

        $linesToAdd = ['echo "Hello, World!";', 'return 0;'];
        $codeGenerator->addLines($linesToAdd, 1);

        $codeGenerator->line_ending = "\n";
        $codeGenerator->intents = 4;
        $codeGenerator->intent_char = " ";

        $expectedResult = [
            "    echo \"Hello, World!\";",
            "    return 0;",
            ""
        ];

        $this->assertEquals($expectedResult, $codeGenerator->toArray());
    }

    public function testAddEmptyLine(): void
    {
        $codeGenerator = new CodeGenerator(0);

        $codeGenerator->addEmptyLine();

        $codeGenerator->line_ending = "\n";
        $codeGenerator->intents = 4;
        $codeGenerator->intent_char = " ";

        $expectedResult = [""];

        $this->assertEquals($expectedResult, $codeGenerator->toArray());
    }
}

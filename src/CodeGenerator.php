<?php

namespace IsaEken\HttpSnippet;

class CodeGenerator
{
    public function __construct(
        public int $intent = 0,
        public int $intents = 4,
        public string $intent_char = " ",
        public string $line_ending = "\r\n",
        public bool $line_numbers = false,
        public bool $ends_with_empty_line = true,
        public string $divider = '',
        public array $lines = [],
    ) {
        // ...
    }

    public function getLines(): array
    {
        return $this->lines;
    }

    public function addLine(string $code, int $intent = 0): self
    {
        $this->lines[] = [
            'code' => $code,
            'divider' => strlen($code) > 0 && ! is_null($this->divider),
            'intent' => $intent,
        ];

        return $this;
    }

    public function addEmptyLine(): self
    {
        return tap($this, function (self $codeGenerator) {
            $codeGenerator->addLine('');
        });
    }

    public function addLines(array $lines, int $intent = 0): self
    {
        return tap($this, function (self $codeGenerator) use ($lines, $intent) {
            foreach ($lines as $line) {
                $codeGenerator->addLine($line, $intent);
            }
        });
    }

    public function toArray(): array
    {
        $currentLine = 0;
        $lines = $this->getLines();
        $lines[count($lines) - 1]['divider'] = false;
        $maxCurrentLineCharacterLength = strlen((string) count($lines));
        $codes = [];

        foreach ($lines as $line) {
            $currentLine++;

            if ($this->line_numbers) {
                $lineNumber = $this->line_numbers ? "[$currentLine] " : '';
                $intent = str_repeat(
                    $this->intent_char,
                    ($line['intent'] * $this->intents) + (($maxCurrentLineCharacterLength + 3) - strlen($lineNumber))
                );
            } else {
                $lineNumber = '';
                $intent = str_repeat(
                    $this->intent_char,
                    ($line['intent'] * $this->intents)
                );
            }

            $code = $line['code'];

            if ($line['divider']) {
                $code .= $this->divider;
            }

            $codes[] = sprintf('%s%s%s', $lineNumber, $intent, $code);
        }

        if ($this->ends_with_empty_line && $codes[count($codes) - 1] !== '') {
            $codes[] = '';
        }

        return $codes;
    }

    public function toString(): string
    {
        return implode($this->line_ending, $this->toArray());
    }
}

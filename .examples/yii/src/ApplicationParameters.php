<?php

declare(strict_types=1);

namespace App;

final class ApplicationParameters
{
    private string $charset = 'UTF-8';
    private string $name = 'My Project';

    public function getCharset(): string
    {
        return $this->charset;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function charset(string $value): self
    {
        $new = clone $this;
        $new->charset = $value;
        return $new;
    }

    public function name(string $value): self
    {
        $new = clone $this;
        $new->name = $value;
        return $new;
    }
}

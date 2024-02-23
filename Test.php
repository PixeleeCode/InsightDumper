<?php

namespace Pixelee\InsightDumper;

final class Test
{
    private string $testProperty;

    public function getTestProperty(): string
    {
        return $this->testProperty;
    }

    public function setTestProperty(string $testProperty): Test
    {
        $this->testProperty = $testProperty;

        return $this;
    }
}

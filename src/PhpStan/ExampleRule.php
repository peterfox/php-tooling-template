<?php

namespace VendorName\Skeleton\PhpStan;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

class ExampleRule implements Rule
{
    public function getNodeType(): string
    {
        //todo return a node class
        return Node::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        // todo return found errors
        return [];
    }
}

<?php

namespace VendorName\Skeleton\Rector;

use PhpParser\Node;
use Rector\Rector\AbstractRector;

class ExampleRector extends AbstractRector
{
    /**
     * @return Node[]
     */
    public function getNodeTypes(): array
    {
        // todo return node classes in an array
        return [];
    }

    public function refactor(Node $node)
    {
        // todo return node
    }
}

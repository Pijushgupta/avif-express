<?php

namespace Avife\trait;

trait DomHelperTrait
{
    private function isInsideNoscript(\DOMNode $node)
    {
        while ($node = $node->parentNode) {
            if ($node instanceof \DOMElement && strtolower($node->tagName) === 'noscript') {
                return true;
            }
        }
        return false;
    }
}
<?php

namespace Avife\common;

if (!defined('ABSPATH')) exit;

use  Avife\interface\Lazyload;
use Masterminds\HTML5;

class Lazyloadhtml implements Lazyload
{

    public function handle($content)
    {
        $parser = new HTML5();
        $dom = $parser->loadHTML($content);

        // Add loading="lazy" where missing
        foreach (['img', 'iframe'] as $tagName) {
            foreach ($dom->getElementsByTagName($tagName) as $tag) {
                if ($this->isInsideNoscript($tag)) {
                    continue; // skip noscript content
                }
                if (!$tag->hasAttribute('loading')) {
                    $tag->setAttribute('loading', 'lazy');
                }
            }
        }

        // Save HTML using the same parser
        $updatedHtml = $parser->saveHTML($dom);

        return $updatedHtml;
    }

    private function isInsideNoscript(\DOMNode $node): bool
    {
        while ($node = $node->parentNode) {
            if (
                $node instanceof \DOMElement &&
                strtolower($node->tagName) === 'noscript'
            ) {
                return true;
            }
        }
        return false;
    }
}

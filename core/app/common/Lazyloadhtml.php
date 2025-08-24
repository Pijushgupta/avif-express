<?php

namespace Avife\common;

if (!defined('ABSPATH')) exit;

use  Avife\interface\Lazyload;
use Masterminds\HTML5;
use Avife\trait\DomHelperTrait;

class Lazyloadhtml implements Lazyload
{
    use DomHelperTrait;
    public function handle($content)
    {
        $parser = new HTML5(['encode_entities' => false]);
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

}

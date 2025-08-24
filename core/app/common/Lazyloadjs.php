<?php

namespace Avife\common;

if (!defined('ABSPATH')) exit;

use Avife\interface\Lazyload;
use Masterminds\HTML5;
use Avife\trait\DomHelperTrait;


class Lazyloadjs implements Lazyload
{
    use DomHelperTrait;
    private string $threshold;
    private string $rootMargin;
    private string $background;

    public function __construct($rootMargin = '0px 0px 200px 0px', $threshold = '0', $background = false)
    {
        $this->rootMargin = $rootMargin;
        $this->threshold = $threshold;
        $this->background = $background;
    }

    public function handle($content)
    {
        $parser = new HTML5(['encode_entities' => false]);
        $dom = $parser->loadHTML($content);

        // Handle <img>, <iframe>, <source>
        foreach (['img', 'iframe', 'source'] as $tagName) {
            foreach ($dom->getElementsByTagName($tagName) as $tag) {
                if ($this->isInsideNoscript($tag)) {
                    continue;
                }
                if ($tag->hasAttribute('src')) {
                    $tag->setAttribute('data-src', $tag->getAttribute('src'));
                    $tag->removeAttribute('src');
                }
            }
        }

        

        // Inject JS before </body>
        $body = $dom->getElementsByTagName('body')->item(0);
        if ($body) {
            $scriptEl = $dom->createElement('script');
            $scriptEl->appendChild($dom->createTextNode($this->addJS()));
            $body->appendChild($scriptEl);
        }
        // Save as HTML
        $updatedHtml = $parser->saveHTML($dom);

        //adding background lazy server side code 
        if($this->background) $updatedHtml = $this->lazyBackground($updatedHtml);
        
        return $updatedHtml;
    }

    public function addJS()
    {
        return <<<JS
            let loadMedia = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        let ele = entry.target;

                        if (['IMG', 'IFRAME'].includes(ele.tagName)) {
                            const dataSrc = ele.getAttribute('data-src');
                            if (dataSrc) {
                                ele.setAttribute('src', dataSrc);
                                ele.removeAttribute('data-src');
                            }
                        }

                        if (ele.tagName === 'SOURCE') {
                            const dataSrc = ele.getAttribute('data-src');
                            if (dataSrc) {
                                ele.setAttribute('src', dataSrc);
                                ele.removeAttribute('data-src');
                                const media = ele.closest('video, audio');
                                if (media) media.load();
                            }
                        }

                        if (ele.hasAttribute('data-bg')) {
                            const urls = ele.getAttribute('data-bg')
                                .split(',')
                                .map(url => 'url(' + url.trim() + ')');
                            ele.style.backgroundImage = urls.join(', ');
                            ele.removeAttribute('data-bg');
                        }

                        observer.unobserve(ele);
                    }
                });
            }, {
                rootMargin: "{$this->rootMargin}",
                threshold: {$this->threshold}
            });

        document.querySelectorAll('img[data-src], iframe[data-src], source[data-src], [data-bg]').forEach((element) => {
            loadMedia.observe(element);
        });
    JS;
    }


    

    private function lazyBackground($content){
        // style {background-image:url()}
        return  preg_replace_callback(
            '/<([a-zA-Z]+)([^>]*?)\sstyle\s*=\s*"([^"]*?)"/i',
            function ($matches) {
                $originalStyle = $matches[3];
                $allUrls = [];

                // Match both background and background-image URLs
                $cleanStyle = preg_replace_callback(
                    '/\b(background(?:-image)?)\s*:\s*([^;]*?)url\((["\']?)(.*?)\3\)([^;]*?)(;?)/i',
                    function ($m) use (&$allUrls) {
                        // Extract URL
                        $allUrls[] = $m[4];

                        // Keep everything else intact (position, repeat, size, etc.)
                        $before = trim(preg_replace('/url\((["\']?).*?\1\)/i', '', $m[2]));
                        $after = $m[5];

                        // Rebuild declaration without the URL
                        $declaration = $m[1] . ':' . trim($before . ' ' . $after) . $m[6];

                        return $declaration;
                    },
                    $originalStyle
                );

                $cleanStyle = trim($cleanStyle);
                $newStyle = !empty($cleanStyle) ? 'style="' . $cleanStyle . '"' : '';
                $dataBg = !empty($allUrls) ? 'data-bg="' . implode(',', $allUrls) . '"' : '';

                return "<{$matches[1]}{$matches[2]} $newStyle $dataBg";
            },
            $content
        );
    }
}

<?php

namespace App\Services;

use DOMDocument;
use DOMNode;

class TripRichText
{
    public static function sanitize(string $html): string
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8"><body>'.$html.'</body>', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
            $body = $document->getElementsByTagName('body')->item(0);

            return $body ? self::children($body) : '';
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    private static function children(DOMNode $node): string
    {
        $result = '';
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                $result .= htmlspecialchars($child->textContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            } elseif ($child->nodeType === XML_ELEMENT_NODE) {
                $tag = strtolower($child->nodeName);
                if (in_array($tag, ['script', 'style', 'iframe', 'object', 'svg', 'math', 'template'], true)) {
                    continue;
                }
                $content = self::children($child);
                $result .= in_array($tag, ['p', 'div', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'h2', 'h3', 'blockquote', 'br'], true)
                    ? ($tag === 'br' ? '<br>' : '<'.$tag.'>'.$content.'</'.$tag.'>')
                    : $content;
            }
        }

        return $result;
    }

    public static function plain(string $html): string
    {
        return trim(html_entity_decode(strip_tags(preg_replace('/<\/(?:p|div|h2|h3|li|blockquote)>|<br\s*\/?\s*>/i', "\n", $html)), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}

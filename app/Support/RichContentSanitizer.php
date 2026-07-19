<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class RichContentSanitizer
{
    /**
     * @var array<string, string[]>
     */
    protected array $allowedAttributes = [
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width'],
        'iframe' => ['src', 'width', 'height', 'allow', 'allowfullscreen', 'frameborder'],
    ];

    /**
     * @var string[]
     */
    protected array $allowedTags = [
        'p',
        'br',
        'strong',
        'b',
        'em',
        'i',
        'u',
        'ul',
        'ol',
        'li',
        'blockquote',
        'h1',
        'h2',
        'h3',
        'h4',
        'a',
        'img',
        'figure',
        'figcaption',
        'iframe',
    ];

    public function sanitize(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $document->loadHTML(
            '<div id="portal-rich-content">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $container = $document->getElementById('portal-rich-content');

        if (! $container instanceof DOMElement) {
            return '';
        }

        $this->sanitizeNode($container);

        $sanitized = '';
        foreach ($container->childNodes as $childNode) {
            $sanitized .= $document->saveHTML($childNode);
        }

        return trim($sanitized);
    }

    protected function sanitizeNode(DOMNode $node): void
    {
        for ($index = $node->childNodes->length - 1; $index >= 0; $index--) {
            $child = $node->childNodes->item($index);

            if (! $child instanceof DOMNode) {
                continue;
            }

            if ($child instanceof \DOMComment) {
                $node->removeChild($child);
                continue;
            }

            if ($child instanceof DOMElement) {
                $tagName = strtolower($child->tagName);

                if (in_array($tagName, ['script', 'style', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select'], true)) {
                    $node->removeChild($child);
                    continue;
                }

                if (! in_array($tagName, $this->allowedTags, true)) {
                    $this->unwrapElement($child);
                    continue;
                }

                $this->sanitizeAttributes($child);

                if ($tagName === 'iframe' && ! $this->isAllowedIframe($child)) {
                    $node->removeChild($child);
                    continue;
                }

                if ($tagName === 'img' && ! $this->isAllowedImage($child)) {
                    $node->removeChild($child);
                    continue;
                }

                if ($tagName === 'a' && ! $this->isAllowedLink($child)) {
                    $this->unwrapElement($child);
                    continue;
                }
            }

            $this->sanitizeNode($child);
        }
    }

    protected function sanitizeAttributes(DOMElement $element): void
    {
        $tagName = strtolower($element->tagName);
        $allowedAttributes = $this->allowedAttributes[$tagName] ?? [];

        for ($index = $element->attributes->length - 1; $index >= 0; $index--) {
            $attribute = $element->attributes->item($index);

            if ($attribute === null) {
                continue;
            }

            $attributeName = strtolower($attribute->name);

            if (str_starts_with($attributeName, 'on') || $attributeName === 'style' || ! in_array($attributeName, $allowedAttributes, true)) {
                $element->removeAttribute($attribute->name);
            }
        }

        if ($tagName === 'a' && $element->hasAttribute('href')) {
            $element->setAttribute('target', '_blank');
            $element->setAttribute('rel', 'noopener noreferrer');
        }

        if ($tagName === 'iframe') {
            $element->setAttribute('loading', 'lazy');
            $element->setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
        }

        if ($tagName === 'img' && $element->hasAttribute('width')) {
            $width = trim($element->getAttribute('width'));

            if (! in_array($width, ['25%', '50%', '75%', '100%'], true)) {
                $element->removeAttribute('width');
            }
        }
    }

    protected function isAllowedLink(DOMElement $element): bool
    {
        $href = trim($element->getAttribute('href'));

        return $href !== '' && (bool) preg_match('/^(https?:\/\/|mailto:|\/)/i', $href);
    }

    protected function isAllowedImage(DOMElement $element): bool
    {
        $src = trim($element->getAttribute('src'));

        return $src !== '' && (bool) preg_match('/^(https?:\/\/|\/storage\/)/i', $src);
    }

    protected function isAllowedIframe(DOMElement $element): bool
    {
        $src = trim($element->getAttribute('src'));

        if ($src === '') {
            return false;
        }

        return (bool) preg_match('#^https:\/\/(www\.)?(youtube\.com\/embed\/|youtube-nocookie\.com\/embed\/)#i', $src);
    }

    protected function unwrapElement(DOMElement $element): void
    {
        $parent = $element->parentNode;

        if ($parent === null) {
            return;
        }

        while ($element->firstChild !== null) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }
}

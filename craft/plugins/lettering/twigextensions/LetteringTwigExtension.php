<?php
/**
 * Lettering plugin for Craft CMS
 *
 * Lettering Twig Extension
 *
 * --snip--
 * Twig can be extended in many ways; you can add extra tags, filters, tests, operators, global variables, and
 * functions. You can even extend the parser itself with node visitors.
 *
 * http://twig.sensiolabs.org/doc/advanced.html
 * --snip--
 *
 * @author    Fred Carlsen
 * @copyright Copyright (c) 2016 Fred Carlsen
 * @link      http://sjelfull.no
 * @package   Lettering
 * @since     1.0.0
 */

namespace Craft;

use Twig_Extension;
use Twig_Filter_Method;
use \DOMDocument as LetteringDom;
use \DOMNode as LetteringDomNode;

class LetteringTwigExtension extends \Twig_Extension
{

    protected $encoding = 'UTF-8';

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'Lettering';
    }

    /**
     * Returns an array of Twig filters, used in Twig templates via:
     *
     *      {{ 'something' | someFilter }}
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'lettering' => new \Twig_Filter_Method($this, 'lettering'),
        );
    }

    /**
     * Our function called via Twig; it can do anything you want
     *
     * @return string
     */
    public function lettering($text = null, $class = 'chars')
    {

        if (!$text || strlen($text) === 0 || !method_exists(craft()->lettering, $class)) {
            return $text;
        }

        $dom = new LetteringDom();
        $dom->loadHTML(mb_convert_encoding('<div id="workingNode">'.$text.'</div>', 'HTML-ENTITIES', $this->encoding));

        $workingNode = $dom->getElementById('workingNode');
        
        $fragment = $dom->createDocumentFragment();

        foreach ($workingNode->childNodes as $node) {
            
            if ($node->nodeType !== 1) {
                continue;
            }
            
            $value = $node->nodeValue;
            $result = craft()->lettering->$class($value, $class);
            $node->nodeValue = '';
            
            $tempFragment = new LetteringDom();
            $tempFragment->loadHTML(mb_convert_encoding($result[$class], 'HTML-ENTITIES', $this->encoding));
            
            foreach ($tempFragment->getElementsByTagName('body')->item(0)->childNodes as $tempNode) {
                $tempNode = $node->ownerDocument->importNode($tempNode, true);
                $node->appendChild($tempNode);
            }

            $node->setAttribute('aria-label', trim(strip_tags($value)));
            $fragment->appendChild($node->cloneNode(true));

        }

        $workingNode->parentNode->replaceChild($fragment, $workingNode);
        $result = TemplateHelper::getRaw(preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $dom->saveHTML()));

        if (strlen(trim($result)) === 0) {
            $result = craft()->lettering->$class($text);
            return $result ? $result[$class] : $text;
        }
        
        return $result;

    }

}
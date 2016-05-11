<?php
/**
 * Lettering plugin for Craft CMS
 *
 * Lettering Service
 *
 * --snip--
 * All of your plugin’s business logic should go in services, including saving data, retrieving data, etc. They
 * provide APIs that your controllers, template variables, and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 * --snip--
 *
 * @author    Fred Carlsen
 * @copyright Copyright (c) 2016 Fred Carlsen
 * @link      http://sjelfull.no
 * @package   Lettering
 * @since     1.0.0
 */

namespace Craft;

class LetteringService extends BaseApplicationComponent
{

    public function chars($text) {
        return $this->injector($text);
    }

    public function words($text) {
        return $this->injector($text, 'words', ' ');
    }

    public function lines($text) {
        return $this->injector($text, 'lines', ' ');
    }

    protected function injector($text, $class = 'chars', $after = '') {

        switch ($class) {
            case 'words' :
                $parts = explode(' ', trim(strip_tags($text)));
                break;
            case 'lines' :
                $parts = preg_split('/<br[^>]*>/i', strip_tags((nl2br(trim($text))), '<br>'));
                break;
            default :
                $parts = str_split(trim(strip_tags($text)));
                break;
        }

        $count = 1;

        $formattedParts = array_map(function($part) use (&$count, $class, $after) {
            $part = '<span class="'.substr($class, 0, -1) . $count . '" aria-hidden="true">' . $part . '</span>' . $after;
            $count = $count + 1;
            return $part;
        }, $parts);

        $ariaLabel = TemplateHelper::getRaw(' aria-label="'. trim(strip_tags($text)) .'"');
        $joined = TemplateHelper::getRaw( implode('', $formattedParts) );
        
        $result = [
            'original' => $text,
            'ariaLabel' => $ariaLabel,
            $class => $joined,
        ];

        return $result;

    }

}
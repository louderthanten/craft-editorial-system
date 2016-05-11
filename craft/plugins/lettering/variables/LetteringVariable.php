<?php
/**
 * Lettering plugin for Craft CMS
 *
 * Lettering Variable
 *
 * --snip--
 * Craft allows plugins to provide their own template variables, accessible from the {{ craft }} global variable
 * (e.g. {{ craft.pluginName }}).
 *
 * https://craftcms.com/docs/plugins/variables
 * --snip--
 *
 * @author    Fred Carlsen
 * @copyright Copyright (c) 2016 Fred Carlsen
 * @link      http://sjelfull.no
 * @package   Lettering
 * @since     1.0.0
 */

namespace Craft;

class LetteringVariable
{

    public function lettering($text)
    {
        return craft()->lettering->chars($text);
    }

    public function chars($text)
    {
        return craft()->lettering->chars($text);
    }
    
    public function words($text)
    {
        return craft()->lettering->words($text);
    }

    public function lines($text)
    {
        return craft()->lettering->lines($text);
    }

}

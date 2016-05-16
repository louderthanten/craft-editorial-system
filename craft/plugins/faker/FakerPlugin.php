<?php

namespace Craft;

class FakerPlugin extends BasePlugin {

    function getName()
    {
        return Craft::t('Faker');
    }

    function getVersion()
    {
        return '0.1';
    }

    function getDeveloper()
    {
        return 'sjelfull';
    }

    function getDeveloperUrl()
    {
        return 'http://sjelfull.no';
    }

    function hasCpSection()
    {
        return false;
    }

    public function getSettingsHtml()
    {
        return false;
    }

}

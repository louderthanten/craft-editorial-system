<?php

namespace Craft;

class FakerVariable
{

    public function locale($locale)
    {
        craft()->faker->setLocale($locale);
        return $this;
    }

    public function fake($extraProviders = array())
    {
        return craft()->faker->factory($extraProviders);
    }
}

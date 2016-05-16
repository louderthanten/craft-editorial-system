<?php

namespace Craft;
require_once(CRAFT_PLUGINS_PATH . 'faker/vendor/autoload.php');


class FakerService extends BaseApplicationComponent {

    protected $faker;
    protected $supportedProviders;
    protected $locale;

    public function init()
    {
        // Default locale
        $this->locale = 'en_EN';

        // Supported providers out of the box
        $this->supportedProviders = array(
            'person'        => 'Person',
            'address'       => 'Address',
            'phonenumber'   => 'PhoneNumber',
            'company'       => 'Company',
            'internet'      => 'Internet',
            'lorem'         => 'Lorem',
            'datetime'      => 'DateTime',
            'useragent'     => 'UserAgent',
            'payment'       => 'Payment',
            'color'         => 'Color',
            'file'          => 'File',
            'image'         => 'Image',
            'uuid'          => 'Uuid',
            'barcode'       => 'Barcode',
            'miscellaneous' => 'Miscellaneous',
            'biased'        => 'Biased',
        );
    }

    /**
     * Allows you to override the default locale
     * @param $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Returns the Faker faking factory. Let the faking commence!
     * @param $providers
     * @return \Faker\Generator
     */
    public function factory($providers)
    {
        $this->faker = \Faker\Factory::create($this->locale);

        foreach ($providers as $provider)
        {
            if ( ! array_key_exists(strtolower($provider), $this->supportedProviders)) continue;

            $class = "\\Faker\\Provider\\" . $this->supportedProviders[ strtolower($provider) ];

            if (class_exists($class))
            {
                $this->faker->addProvider(new $class($this->faker));
            }
        }

        return $this->faker;
    }
}

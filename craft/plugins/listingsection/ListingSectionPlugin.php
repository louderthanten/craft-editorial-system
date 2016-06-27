<?php

namespace Craft;

class ListingSectionPlugin extends BasePlugin
{
	public function getName()
	{
		return Craft::t('Listing Section');
	}

	public function getVersion()
	{
		return '1.0';
	}

	public function getDeveloper()
	{
		return 'Trevor Davis';
	}

	public function getDeveloperUrl()
	{
		return 'http://trevordavis.net';
	}
}
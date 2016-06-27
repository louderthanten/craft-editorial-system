<?php
namespace Craft;

class CpBodyClassesPlugin extends BasePlugin
{

	public function init()
	{
		parent::init();

		// Get settings
		$s = $this->settings;

		// Load class services
		$c = craft()->cpBodyClasses;

		// If control panel page
		if (craft()->request->isCpRequest()) {
			// Add all requested groups
			if ($s->showUserGroups)        {$c->classUserGroups();}
			if ($s->showUserAdmin)         {$c->classUserAdmin();}
			if ($s->showCurrentSection)    {$c->classCurrentSection();}
			if ($s->showCurrentPage)       {$c->classCurrentPage();}
			if ($s->showProfileUserGroups) {$c->classProfileUserGroups();}
			if ($s->showProfileUserAdmin)  {$c->classProfileUserAdmin();}
		}

		// If any body classes have been set
		if (!empty($c->bodyClasses)) {
			// Apply to template variables
			craft()->urlManager->setRouteVariables(array(
				'bodyClass' => implode(' ', $c->bodyClasses)
			));
		}
	}

	public function getName()
	{
		return Craft::t('Control Panel Body Classes');
	}

	public function getDescription()
	{
		return 'Adds special classes to the Control Panel\'s <body> tag.';
	}

	public function getDocumentationUrl()
	{
		return 'https://github.com/lindseydiloreto/craft-cpbody';
	}

	public function getVersion()
	{
		return '1.2.0';
	}

	public function getSchemaVersion()
	{
		return '1.2.0';
	}

	public function getDeveloper()
	{
		return 'Double Secret Agency';
	}

	public function getDeveloperUrl()
	{
		return 'https://github.com/lindseydiloreto/craft-cpbody';
		//return 'http://doublesecretagency.com';
	}

	public function getSettingsHtml()
	{
		craft()->templates->includeCssResource('cpbodyclasses/css/settings.css');
		return craft()->templates->render('cpbodyclasses/_settings', array(
			'settings' => $this->getSettings(),
		));
	}

	protected function defineSettings()
	{
		return array(
			'showUserGroups'        => array(AttributeType::Bool, 'default' => true),
			'showUserAdmin'         => array(AttributeType::Bool, 'default' => true),
			'showCurrentSection'    => array(AttributeType::Bool, 'default' => false),
			'showCurrentPage'       => array(AttributeType::Bool, 'default' => false),
			'showProfileUserGroups' => array(AttributeType::Bool, 'default' => false),
			'showProfileUserAdmin'  => array(AttributeType::Bool, 'default' => false),
		);
	}

}

<?php

namespace Craft;

class ListingSection_ListingSectionFieldType extends BaseFieldType
{
	public function getName()
	{
		return Craft::t('Listing Section');
	}

	public function getInputHtml($name, $value)
	{
		return craft()->templates->render('_includes/forms/select', array(
			'name' => $name,
			'value' => $value,
			'options' => array_merge(array(''), $this->_getSections())
		));
	}

	private function _getSections()
	{
		$sections = craft()->db->createCommand()
					->select('handle as value, name as label')
					->from('sections')
					->order('name')
					->queryAll();

		return $sections;
	}
}

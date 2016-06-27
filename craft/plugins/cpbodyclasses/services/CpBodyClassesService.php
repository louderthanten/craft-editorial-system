<?php
namespace Craft;

class CpBodyClassesService extends BaseApplicationComponent
{

	public $bodyClasses = array();

	// User group classes
	public function classUserGroups()
	{
		// Get current user
		$user = craft()->userSession->getUser();
		// Add user group classes
		$this->_addUserGroupClasses('usergroup', $user);
	}

	// User admin class
	public function classUserAdmin()
	{
		// Get current user
		$user = craft()->userSession->getUser();
		// Add admin class
		$this->_addUserAdminClass('userlevel', $user);
	}

	// Current section
	public function classCurrentSection()
	{
		// Get first segment
		$section = craft()->request->getSegment(1);
		// If section can be determined
		if ($section) {
			$this->_addClass('currentsection', $section);
		}
	}

	// Current page
	public function classCurrentPage()
	{
		// Get all segments
		$segments = craft()->request->getSegments();
		// Compile page class
		$page = implode('-', $segments);
		$this->_addClass('currentpage', $page);
	}

	// Profile's user group
	public function classProfileUserGroups()
	{
		// Get user in profile
		$user = $this->_getProfileUser();
		// Add user group classes
		$this->_addUserGroupClasses('profile', $user);
	}

	// Profile's user admin
	public function classProfileUserAdmin()
	{
		// Get user in profile
		$user = $this->_getProfileUser();
		// Add admin class
		$this->_addUserAdminClass('profilelevel', $user);
	}

	// ======================================================================== //

	// Append body class to master array
	private function _addClass($prefix, $class)
	{
		$this->bodyClasses[] = $prefix.'-'.$class;
	}

	// Add classes for user groups
	private function _addUserGroupClasses($prefix, $user)
	{
		// Get user groups
		if ($user) {
			foreach ($user->getGroups() as $group) {
				$this->_addClass($prefix, $group->handle);
			}
		}
	}

	// Add class for admin
	private function _addUserAdminClass($prefix, $user)
	{
		// If user is an admin
		if ($user && $user['admin']) {
			$this->_addClass($prefix, 'admin');
		}
	}

	// Get user of profile being viewed
	private function _getProfileUser()
	{
		// Get URL segments
		$page   = craft()->request->getSegment(1);
		$userId = craft()->request->getSegment(2);
		// Return profile user (if valid)
		if (('users' == $page) && is_numeric($userId)) {
			return craft()->users->getUserById($userId);
		} else {
			return false;
		}
	}

}

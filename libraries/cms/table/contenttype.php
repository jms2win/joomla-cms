<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Tags table
 *
 * @package     Joomla.Libraries
 * @subpackage  Table
 * @since       3.1
 */
class JTableContenttype extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  A database connector object
	 *
	 * @since   3.1
	 */
	public function __construct($db)
	{
		parent::__construct('#__content_types', 'type_id', $db);
	}

	/**
	 * Overloaded check method to ensure data integrity.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.1
	 * @throws  UnexpectedValueException
	 */
	public function check()
	{
		// Check for valid name.
		if (trim($this->type_title) == '')
		{
			throw new UnexpectedValueException(sprintf('The title is empty'));
		}

		$this->type_title = ucfirst($this->type_title);

		if (empty($this->type_alias))
		{
			$this->type_alias = strtolower($this->type_title);
		}

		$this->type_alias = JApplication::stringURLSafe($this->type_alias);

		if (trim(str_replace('-', '', $this->type_alias)) == '')
		{
			$this->type_alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}

		return true;
	}

	/**
	 * Overridden JTable::store.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.1
	 */
	public function store($updateNulls = false)
	{
		// Verify that the alias is unique
		$table = JTable::getInstance('Contenttype', 'JTable');
		if ($table->load(array('type_alias' => $this->type_alias)) && ($table->type_id != $this->type_id || $this->type_id == 0))
		{
			$this->setError(JText::_('COM_TAGS_ERROR_UNIQUE_ALIAS'));

			return false;
		}

		return parent::store($updateNulls);
	}

	/**
	 * Method to expand the field mapping
	 *
	 * @param   boolean  $assoc  True to return an associative array.
	 *
	 * @return  mixed  Array or object with field mappings. Defaults to object.
	 *
	 * @since   3.1
	 */
	public function fieldmapExpand($assoc = true)
	{
		return $this->fieldmap = json_decode($this->fieldmappings, $assoc);
	}
}

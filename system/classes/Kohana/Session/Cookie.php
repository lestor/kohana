<?php
/**
 * Cookie-based session class.
 *
 * @package    Kohana
 * @category   Session
 * @author     Kohana Team
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_Session_Cookie extends Session {

	/**
	 * @param   string $id session id
	 * @return  string
	 */
	protected function _read($id = NULL)
	{
		return Cookie::get($this->_name, NULL);
	}

	/**
	 * @return  null
	 */
	protected function _regenerate()
	{
		// Cookie sessions have no id
		return NULL;
	}

	/**
	 * @return  boolean
	 */
	protected function _write()
	{
		return Cookie::set($this->_name, $this->__toString(), $this->_lifetime);
	}

	/**
	 * @return  boolean
	 */
	protected function _restart()
	{
		return TRUE;
	}

	/**
	 * @return  boolean
	 */
	protected function _destroy()
	{
		return Cookie::delete($this->_name);
	}

}

<?php
/**
 * Native PHP session class.
 *
 * @package    Kohana
 * @category   Session
 * @author     Kohana Team
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_Session_Native extends Session {

	/**
	 * @return  string
	 */
	public function id()
	{
		return session_id();
	}

	/**
	 * @param   string $id session id
	 * @return  null
	 */
	protected function _read($id = NULL)
	{
		/**
		 * session_set_cookie_params will override php ini settings
		 * If Cookie::$domain is NULL or empty and is passed, PHP
		 * will override ini and sent cookies with the host name
		 * of the server which generated the cookie
		 * 
		 * see issue #3604
		 * 
		 * see http://www.php.net/manual/en/function.session-set-cookie-params.php
		 * see http://www.php.net/manual/en/session.configuration.php#ini.session.cookie-domain
		 * 
		 * set to Cookie::$domain if available, otherwise default to ini setting
		 */
		$session_cookie_domain = empty(Cookie::$domain)
			? ini_get('session.cookie_domain')
			: Cookie::$domain;

		// Sync up the session cookie with Cookie parameters
		session_set_cookie_params(
			$this->_lifetime,
			Cookie::$path,
			$session_cookie_domain,
			Cookie::$secure,
			Cookie::$httponly
		);

		// Do not allow PHP to send Cache-Control headers
		session_cache_limiter(FALSE);

		// Set the session cookie name
		session_name($this->_name);

		if ($id)
		{
			// Set the session id
			session_id($id);
		}

		// Start the session
		session_start();

		// Use the $_SESSION global for storing data
		$this->_data =& $_SESSION;

		return NULL;
	}

	/**
	 * @return  string
	 */
	protected function _regenerate()
	{
		// Regenerate the session id
		session_regenerate_id();

		return session_id();
	}

	/**
	 * @return  boolean
	 */
	protected function _write()
	{
		// Write and close the session
		session_write_close();

		return TRUE;
	}

	/**
	 * @return  boolean
	 */
	protected function _restart()
	{
		// Fire up a new session
		$status = session_start();

		// Use the $_SESSION global for storing data
		$this->_data =& $_SESSION;

		return $status;
	}

	/**
	 * @return  boolean
	 */
	protected function _destroy()
	{
		// Destroy the current session
		session_destroy();

		// Did destruction work?
		$status = ! session_id();

		if ($status)
		{
			// Make sure the session cannot be restarted
			Cookie::delete($this->_name);
		}

		return $status;
	}

}

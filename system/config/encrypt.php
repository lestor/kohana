<?php

return array(

	'default' => array(
		/**
		 * The following options must be set:
		 *
		 * string   key       The secret key (must be 32 chars long!)
		 * integer  cipher    The encryption cipher, one of the Sodium cipher constants
		 */
		'key' => NULL,
		'cipher' => Encrypt::CIPHER_XCHACHA20_POLY1305_IETF
	)

);

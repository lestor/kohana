<?php
/**
 * The Sodium library provides two-way encryption of text and binary strings
 * using the [Sodium](http://php.net/sodium) extension, which consists of two
 * parts: the key and the the cipher.
 *
 * The Key
 * :  A secret passphrase that is used for encoding and decoding
 *
 * The Cipher
 * :  A [cipher](https://libsodium.gitbook.io/doc/secret-key_cryptography/aead) determines how the encryption
 *    is mathematically calculated. By default, the "XChaCha20-Poly1305-IETF" cipher is used.
 */
class Kohana_Encrypt {

	/**
	 * @var  string  Default instance name
	 */
	public static $default = 'default';

	/**
	 * @var  array  Encrypt class instances
	 */
	public static $instances = array();

	/**
	 * @var  string  Encryption key
	 */
	protected $_key;

	/**
	 * @var  integer  Encryption cipher
	 */
	protected $_cipher;

	/**
	 * @var int  The size of the Initialization Vector (IV) in bytes
	 */
	protected $_iv_size;

	/**
	 * AES-256-GCM
	 * NOTE: This is only available if you have supported hardware.
	 */
	const CIPHER_AES256_GCM = 'aes256gcm';

	/**
	 * ChaCha20 + Poly1305
	 */
	const CIPHER_CHACHA20_POLY1305 = 'chacha20poly1305';

	/**
	 * ChaCha20 + Poly1305 (IETF version)
	 */
	const CIPHER_CHACHA20_POLY1305_IETF = 'chacha20poly1305_ietf';

	/**
	 * XChaCha20 + Poly1305 [IETF version]
	 */
	const CIPHER_XCHACHA20_POLY1305_IETF = 'xchacha20poly1305_ietf';

	/**
	 * Returns a singleton instance of Encrypt. An encryption key must be
	 * provided in your "encrypt" configuration file.
	 *
	 *     $encrypt = Encrypt::instance();
	 *
	 * @param   string  $name   Configuration group name
	 * @return  Encrypt
	 * @throws  Kohana_Exception
	 */
	public static function instance($name = NULL)
	{
		if ($name === NULL)
		{
			// Use the default instance name
			$name = Encrypt::$default;
		}

		if ( ! isset(Encrypt::$instances[$name]))
		{
			// Load the configuration data
			$config = Kohana::$config->load('encrypt')->{$name};

			if ( ! isset($config['key']))
			{
				// No default encryption key is provided!
				throw new Kohana_Exception('No encryption key is defined in the encryption configuration group: :group',
					array(':group' => $name));
			}

			if ( ! isset($config['cipher']))
			{
				// Add the default cipher
				$config['cipher'] = self::CIPHER_XCHACHA20_POLY1305_IETF;
			}

			// Create a new instance
			Encrypt::$instances[$name] = new Encrypt($config['key'], $config['cipher']);
		}

		return Encrypt::$instances[$name];
	}

	/**
	 * Creates a new sodium wrapper.
	 *
	 * @param   string  $key      Encryption key
	 * @param   string  $cipher   Encryption cipher
	 * @throws Kohana_Exception
	 */
	public function __construct($key, $cipher)
	{
		if ( ! extension_loaded('sodium'))
		{
			throw new Kohana_Exception('PHP sodium extension is not available.');
		}

		if ($cipher === Encrypt::CIPHER_AES256_GCM)
		{
			if ( ! sodium_crypto_aead_aes256gcm_is_available())
			{
				throw new Kohana_Exception('AES-GCM is not supported on this platform.');
			}
		}

		// Store the key and cipher
		$this->_key = $key;
		$this->_cipher = $cipher;

		// Store the IV size
		$this->_iv_size = constant('SODIUM_CRYPTO_AEAD_'.strtoupper($this->_cipher).'_NPUBBYTES');
	}

	/**
	 * Encrypts a string and returns an encrypted string that can be decoded.
	 *
	 *     $data = $encrypt->encode($data);
	 *
	 * The encrypted binary data is encoded using [base64](http://php.net/base64_encode)
	 * to convert it to a string. This string can be stored in a database,
	 * displayed, and passed using most other means without corruption.
	 *
	 * @param   string  $data  Data to be encrypted
	 * @return  string
	 * @throws  Exception
	 */
	public function encode($data)
	{
		// Get an initialization vector
		$iv = random_bytes($this->_iv_size);

		// Encrypt the data using the configured options and generated IV
		$ciphertext = call_user_func(
			'sodium_crypto_aead_'.$this->_cipher.'_encrypt', $data, '', $iv, $this->_key
		);

		$encrypted = $iv.$ciphertext;

		// Use base64 encoding to convert to a string
		return base64_encode($encrypted);
	}

	/**
	 * Decrypts an encoded string back to its original value.
	 *
	 *     $data = $encrypt->decode($data);
	 *
	 * @param   string  $data  Encoded string to be decrypted
	 * @return  string|boolean
	 */
	public function decode($data)
	{
		// Convert the data back to binary
		$data = base64_decode($data, TRUE);

		if ($data === FALSE)
		{
			// The message contains an invalid base64 string
			return FALSE;
		}

		$iv = mb_substr($data, 0, $this->_iv_size, '8bit');

		if ($this->_iv_size !== strlen($iv))
		{
			// The iv is not the expected size
			return FALSE;
		}

		$ciphertext = mb_substr($data, $this->_iv_size, NULL, '8bit');

		// Return the decrypted data
		$decrypted = call_user_func(
			'sodium_crypto_aead_'.$this->_cipher.'_decrypt', $ciphertext, '', $iv, $this->_key
		);

		if ($decrypted === FALSE)
		{
			// The message was tampered with in transit
			return FALSE;
		}

		return $decrypted;
	}
}
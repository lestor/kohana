<?php

/**
 * Tests the Encrypt class
 */
class Kohana_EncryptTest extends Unittest_TestCase
{
	public function setUp() : void
	{
		parent::setUp();

		if ( ! extension_loaded('sodium'))
		{
			$this->markTestSkipped('PHP sodium extension is not available.');
		}
	}

	/**
	 * Provider for test_encode
	 *
	 * @return array of $key, $cipher, $data
	 * @throws Exception
	 */
	public function provider_encode()
	{
		return array(
			array('raeh1Quoobei5zohviDoovoh7sae2ais', Encrypt::CIPHER_AES256_GCM, 'Hello world!'),
			array('raeh1Quoobei5zohviDoovoh7sae2ais', Encrypt::CIPHER_CHACHA20_POLY1305, 'Hello world!'),
			array('raeh1Quoobei5zohviDoovoh7sae2ais', Encrypt::CIPHER_CHACHA20_POLY1305_IETF, 'Hello world!'),
			array('raeh1Quoobei5zohviDoovoh7sae2ais', Encrypt::CIPHER_XCHACHA20_POLY1305_IETF, 'Hello world!'),
		);
	}

	/**
	 * Test encode
	 *
	 * @param string  $key
	 * @param integer $cipher
	 * @param string  $data
	 *
	 * @dataProvider provider_encode
	 * @covers Encrypt::encode
	 * @throws Exception
	 */
	public function test_encode($key, $cipher, $data)
	{
		$encrypt = new Encrypt($key, $cipher);

		$encrypted_data = $encrypt->encode($data);

		$this->assertNotEquals($data, $encrypted_data);
		$this->assertStringNotContainsString(' ', $encrypted_data);
	}

	/**
	 * Provider for test_decode
	 *
	 * @return array of $key, $cipher, $data, $encrypted_data
	 * @throws Exception
	 */
	public function provider_decode()
	{
		return array(
			array('fcc38cb7a2c056ef7bf7193855b099ef', Encrypt::CIPHER_AES256_GCM, 'Hello world!', 'DgOAWd9CJsHnluErdW+vkMgajGqtIkphYQc77gc+02tEytoYsqeVCQ=='),
			array('fcc38cb7a2c056ef7bf7193855b099ef', Encrypt::CIPHER_CHACHA20_POLY1305, 'Hello world!', '7HmXW1STilPOFDeTFbphJqUQm4+W1xiW4kKqR2kyDIW0E1Iv'),
			array('fcc38cb7a2c056ef7bf7193855b099ef', Encrypt::CIPHER_CHACHA20_POLY1305_IETF, 'Hello world!', 'cpdWeUSAG/g9oTfDWQXdsHHFqykMzWgE/Ix5+/7239rGb+cSHgU/nQ=='),
			array('fcc38cb7a2c056ef7bf7193855b099ef', Encrypt::CIPHER_XCHACHA20_POLY1305_IETF, 'Hello world!', 'SkMPXIyLEPrKVFEQcSR7r4UB11aUHJOrQuE/kGoi28tRdgM+zDucKoq3IXPfPFcxMnhCQg=='),
		);
	}

	/**
	 * @param string $key
	 * @param integer $cipher
	 * @param string $data
	 * @param string $encrypted_data
	 *
	 * @dataProvider provider_decode
	 * @covers Encrypt::decode
	 * @throws Exception
	 */
	public function test_decode($key, $cipher, $data, $encrypted_data)
	{
		$encrypt = new Encrypt($key, $cipher);

		$this->assertNotEquals($data, $encrypted_data);
		$this->assertStringNotContainsString(' ', $encrypted_data);

		$decrypted_data = $encrypt->decode($encrypted_data);

		$this->assertEquals($data, $decrypted_data);
	}

	/**
	 * Provider for test_decode_invalid_data
	 *
	 * @return array of $key, $cipher, $invalid_encrypted_data
	 * @throws Exception
	 */
	public function provider_decode_invalid_data()
	{
		return array(
			array('Que0noh5Kei1IT2chohchaiX5Kaivaew', Encrypt::CIPHER_AES256_GCM, '/Invalid encoded string/'),
			array('Que0noh5Kei1IT2chohchaiX5Kaivaew', Encrypt::CIPHER_CHACHA20_POLY1305, '/Invalid encoded string/'),
			array('Que0noh5Kei1IT2chohchaiX5Kaivaew', Encrypt::CIPHER_CHACHA20_POLY1305_IETF, '/Invalid encoded string/'),
			array('Que0noh5Kei1IT2chohchaiX5Kaivaew', Encrypt::CIPHER_XCHACHA20_POLY1305_IETF, '/Invalid encoded string/'),
		);
	}

	/**
	 * Tests for decode when the data is not valid data
	 *
	 * @param string $key
	 * @param integer $cipher
	 * @param string $invalid_encrypted_data
	 *
	 * @dataProvider provider_decode_invalid_data
	 * @throws Kohana_Exception
	 */
	public function test_decode_invalid_data($key, $cipher, $invalid_encrypted_data)
	{
		$encrypt = new Encrypt($key, $cipher);

		$this->assertFalse($encrypt->decode($invalid_encrypted_data));
	}

	/**
	 * Provider for encode_decode
	 *
	 * @return array of $key, $cipher, $data
	 * @throws Exception
	 */
	public function provider_encode_decode()
	{
		return array(
			array('que5Cheic4oreingaesiev7guaqu7epu', Encrypt::CIPHER_AES256_GCM, 'Hello world!'),
			array('que5Cheic4oreingaesiev7guaqu7epu', Encrypt::CIPHER_CHACHA20_POLY1305, 'Hello world!'),
			array('que5Cheic4oreingaesiev7guaqu7epu', Encrypt::CIPHER_CHACHA20_POLY1305_IETF, 'Hello world!'),
			array('que5Cheic4oreingaesiev7guaqu7epu', Encrypt::CIPHER_XCHACHA20_POLY1305_IETF, 'Hello world!'),
		);
	}

	/**
	 * @param string $key
	 * @param integer $cipher
	 * @param string $data
	 *
	 * @dataProvider provider_decode
	 * @covers Encrypt::decode
	 * @throws Exception
	 */
	public function test_encode_decode($key, $cipher, $data)
	{
		$encrypt = new Encrypt($key, $cipher);

		$encrypted_data = $encrypt->encode($data);

		$this->assertNotEquals($data, $encrypted_data);
		$this->assertStringNotContainsString(' ', $encrypted_data);

		$decrypted_data = $encrypt->decode($encrypted_data);

		$this->assertEquals($data, $decrypted_data);
	}

	/**
	 * Provider for test_consecutive_encode_produce_different_results
	 *
	 * @return array of $key, $cipher, $data
	 * @throws Exception
	 */
	public function provider_consecutive_encode_produce_different_results()
	{
		return array(
			array('AeSh5vue4eef7Ixie0ojairahgheiZee', Encrypt::CIPHER_AES256_GCM, 'Hello world!'),
			array('AeSh5vue4eef7Ixie0ojairahgheiZee', Encrypt::CIPHER_CHACHA20_POLY1305, 'Hello world!'),
			array('AeSh5vue4eef7Ixie0ojairahgheiZee', Encrypt::CIPHER_CHACHA20_POLY1305_IETF, 'Hello world!'),
			array('AeSh5vue4eef7Ixie0ojairahgheiZee', Encrypt::CIPHER_XCHACHA20_POLY1305_IETF, 'Hello world!'),
		);
	}

	/**
	 * @param string $key
	 * @param integer $cipher
	 * @param string $data
	 *
	 * @dataProvider provider_consecutive_encode_produce_different_results
	 * @covers Encrypt::encode
	 * @throws Exception
	 * @throws Kohana_Exception
	 */
	public function test_consecutive_encode_produce_different_results($key, $cipher, $data)
	{
		$encrypt = new Encrypt($key, $cipher);

		$data_encrypted_first = $encrypt->encode($data);
		$data_encrypted_second = $encrypt->encode($data);

		$this->assertNotEquals($data_encrypted_first, $data_encrypted_second);
	}

	/**
	 * Provider for test_instance_returns_singleton
	 *
	 * @return array of $instance_name, $missing_config
	 * @throws Exception
	 */
	public function provider_instance_returns_singleton()
	{
		return array(
			array(
				'default',
				array(
					'key' => 'aw7iw2Phui8ieZoc3tiughoseib2feir',
				)
			),
			array(
				'blowfish',
				array(
					'key' => 'Eiqu9Ailaiwiu9Iewiw3ahluifohchoh',
					'cipher' => Encrypt::CIPHER_AES256_GCM,
				)
			),
			array(
				'tripledes',
				array(
					'key' => 'Leel1ahda8reeth8nieseegahphai6le',
					'cipher' => Encrypt::CIPHER_CHACHA20_POLY1305,
				)
			),
		);
	}

	/**
	 * Test to multiple calls to the instance() method returns same instance
	 * also test if the instances are appropriately configured.
	 *
	 * @param string $instance_name instance name
	 * @param array $config_array array of config variables missing from config
	 *
	 * @dataProvider provider_instance_returns_singleton
	 * @throws Kohana_Exception
	 */
	public function test_instance_returns_singleton($instance_name, array $config_array)
	{
		$config = Kohana::$config->load('encrypt');

		$config_group = $instance_name ? : Encrypt::$default;

		if ( ! array_key_exists($config_group, $config))
		{
			$config[$config_group] = array();
		}

		$config[$config_group] = array_merge($config[$config_group], $config_array);

		$encrypt_first = Encrypt::instance($instance_name);
		$encrypt_second = Encrypt::instance($instance_name);

		$this->assertInstanceOf('Encrypt', $encrypt_first);
		$this->assertInstanceOf('Encrypt', $encrypt_second);
		$this->assertSame($encrypt_first, $encrypt_second);
	}
}
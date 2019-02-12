# Encryption

Kohana supports built-in encryption and decryption via the [Sodium] class, which is a convenient wrapper for the [Sodium library](http://www.php.net/sodium).

To use the class, first start by ensuring you have the Sodium extension loaded to your PHP config. See the [Sodium Installation page](http://www.php.net/manual/en/sodium.setup.php) on php.net. The Sodium extension requires [libsodium](https://download.libsodium.org/doc/).

Next, copy the default config/encrypt.php from system/config folder to your application/config folder.

The default Encryption config file that ships with Kohana 3.3.x looks like this:

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
            'cipher' => Encrypt::CIPHER_XCHACHA20_POLY1305_IETF,
        ),

    );


A couple of notes about the config.
First, you may have multiple first-level keys other than 'default' if you need to.
In this respect, the config file is similar to having multiple databases defined in your config/database.php file.
Second, notice there is no key provided. You need to add that.
It is strongly recommended that you choose a high-strength random key using the [pwgen linux program](http://linux.die.net/man/1/pwgen)...

    shell> pwgen 32 1
    Leir9tighahg6awiaghaiV8Chee7waes

...or by going to [GRC.com/passwords.htm](https://www.grc.com/passwords.htm).

## Complete Config Example

Here's a sample encryption configuration with three types of encryption defined. **If you copy this example, please change your keys!**

    <?php
    // application/config/encrypt.php

    return array(

        'default' => array(
            'key'    => 'shiekahta5cohNgeineivu3Eif1yeeg3',
            'cipher' => Encrypt::CIPHER_XCHACHA20_POLY1305_IETF,
        ),
        'blowfish' => array(
            'key'    => 'Oj4geegheiweiyieYahliewei5vooho3',
            'cipher' => Encrypt::CIPHER_AES256_GCM,
        ),
        'tripledes' => array(
            'key'    => 'Ohk7Aaca8shush8theeg2Ie2eeGhaaki',
            'cipher' => Encrypt::CIPHER_CHACHA20_POLY1305,
        ),
    );

You can view the available encryption ciphers and modes on your system by running...

    shell> php -r "print_r(get_defined_constants());" | grep SODIUM

For more information on Sodium ciphers, visit [this page](https://libsodium.gitbook.io/doc/secret-key_cryptography/aead).

## Basic Usage

### Create an instance

To use the Encryption class, obtain an instance of the Encrypt class by calling it's *instance* method,
optionally passing the desired configuration group. If you do not pass a config group to the instance method,
the default group will be used.

    $encrypt = Encrypt::instance('tripledes');

### Encoding Data

Next, encode some data using the *encode* method:

    $encrypt = Encrypt::instance('tripledes');
    $encrypted_data = $encrypt->encode('Data to Encode');
    // $encrypted_data now contains 0N7i64BgUI2gqX29bU41Jm+BTWOsZG6H8zNQl0DOq6bh3NO4zk0=

[!!] Raw encrypted strings usually won't print in a browser, and may not store properly in a VARCHAR or TEXT field. For this reason, Kohana's Encrypt class automatically calls base64_encode on encode, and base64_decode on decode, to prevent this problem.

[!!] One word of caution. The length of the encoded data expands quite a bit, so be sure your database column is long enough to store the encrypted data. If even one character is truncated, the data will not be recoverable.

### Decoding Data

To decode some data, load it from the place you stored it (most likely your database) then pass it to the *decode* method:

    $encrypt = Encrypt::instance('tripledes');
    $decoded_string = $encrypt->decode($encrypted_data);
    echo $decoded_string;
    // prints 'Data to Encode'

You can't know in advance what the encoded string will be, and it's not reproducible, either.
That is, you can encode the same value over and over, but you'll always obtain a different encoded version,
even without changing your key, cipher and mode.  This is because Kohana adds some random entropy before encoding it with your value.
This ensures an attacker cannot easily discover your key and cipher, even given a collection of encoded values.
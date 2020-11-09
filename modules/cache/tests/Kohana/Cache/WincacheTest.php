<?php

/**
 * @package    Kohana/Cache
 * @group      kohana
 * @group      kohana.cache
 * @category   Test
 * @author     Kohana Team
 * @copyright  (c) 2009-2012 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Cache_WincacheTest extends Kohana_Cache_AbstractTest {

    /**
     * This method MUST be implemented by each driver to setup the `Cache`
     * instance for each test.
     *
     * This method should do the following tasks for each driver test:
     *
     *  - Test the Cache instance driver is available, skip test otherwise
     *  - Setup the Cache instance
     *  - Call the parent setup method, `parent::setUp()`
     *
     * @return  void
     */
    public function setUp() : void
    {
        parent::setUp();

        if ( ! extension_loaded('wincache'))
        {
            $this->markTestSkipped('Wincache PHP Extension is not available');
        }

        $this->cache(Cache::instance('wincache'));
    }

} // End Kohana_WincacheTest


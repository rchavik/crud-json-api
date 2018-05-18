<?php
namespace CrudJsonApi\Test\TestCase\Integration;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\File;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestCase;
use CrudJsonApi\Error\JsonApiExceptionRenderer;

abstract class JsonApiBaseTestCase extends IntegrationTestCase
{
    /**
     * fixtures property
     *
     * @var array
     */
    public $fixtures = [
        'plugin.CrudJsonApi.countries',
        'plugin.CrudJsonApi.currencies',
        'plugin.CrudJsonApi.cultures',
        'plugin.CrudJsonApi.national_capitals',
        'plugin.CrudJsonApi.national_cities',
    ];

    /**
     * Path to directory holding the JSON API response fixtures.
     *
     * @var
     */
    protected $_JsonDir;

    /**
     * Set up required RESTful resource routes.
     */
    public function setUp()
    {
        parent::setUp();

        // Enable PSR-7 integration testing
        $this->useHttpServer(true);

        Configure::write('Error.exceptionRenderer', JsonApiExceptionRenderer::class);

        Router::scope('/', function ($routes) {
            $routes->resources('Countries', [
                'inflect' => 'dasherize'
            ]);
            $routes->resources('Currencies', [ // single word belongsTo association
                'inflect' => 'dasherize'
            ]);
            $routes->resources('Cultures', [ // single word hasMany association
                'inflect' => 'dasherize'
            ]);
            $routes->resources('NationalCapitals', [ // multi-word belongsTo association
                'inflect' => 'dasherize'
            ]);
            $routes->resources('NationalCities', [ // multi-word hasMany association
                'inflect' => 'dasherize'
            ]);
        });

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/vnd.api+json'
            ]
        ]);

        // set path to the JSON API response fixtures
        $this->_JsonDir = Plugin::path('Crud') . 'tests' . DS . 'Fixture' . DS . 'JsonApiResponses';
    }

    /**
     * Tear down test.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Helper function to ensure a JSON API response returns the mandatory headers.
     *
     * @return void
     */
    protected function _assertJsonApiResponseHeaders()
    {
        $this->assertHeader('Content-Type', 'application/vnd.api+json');
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Helper function to load a json file for use as `expected` in the assertions.
     *
     * return @void
     */
    protected function _getExpected($file)
    {
        return trim((new File($this->_JsonDir . DS . $file))->read());
    }
}

<?php
namespace Czim\Filter\Test;

use Czim\Filter\Test\Helpers\TranslatableConfig;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    const TABLE_NAME_SIMPLE       = 'test_simple_models';
    const TABLE_NAME_TRANSLATIONS = 'test_simple_model_translations';
    const TABLE_NAME_RELATED      = 'test_related_models';

    /**
     * @var DatabaseManager
     */
    protected $db;

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // set minutes for cache to live
        //$app['config']->set('cache.ttl', 60);

        $app['config']->set('translatable', (new TranslatableConfig)->getConfig());
    }


    public function setUp(): void
    {
        parent::setUp();

        $this->migrateDatabase();
        $this->seedDatabase();
    }


    protected function migrateDatabase()
    {
        //$db = $this->app->make('db');

        // model we can test anything but translations with
        Schema::create(self::TABLE_NAME_SIMPLE, function($table) {
            $table->increments('id');
            $table->string('unique_field', 20);
            $table->integer('second_field')->unsigned()->nullable();
            $table->string('name', 255)->nullable();
            $table->integer('test_related_model_id');
            $table->integer('position')->nullable();
            $table->boolean('active')->nullable()->default(false);
            $table->timestamps();
        });

        // model we can also test translations with
        Schema::create(self::TABLE_NAME_RELATED, function($table) {
            $table->increments('id');
            $table->string('name', 255)->nullable();
            $table->string('some_property', 20)->nullable();
            $table->integer('test_simple_model_id')->nullable();
            $table->integer('position')->nullable();
            $table->boolean('active')->nullable()->default(false);
            $table->timestamps();
        });

        Schema::create(self::TABLE_NAME_TRANSLATIONS, function($table) {
            $table->increments('id');
            $table->integer('test_simple_model_id')->unsigned();
            $table->string('locale', 12);
            $table->string('translated_string', 255);
            $table->timestamps();
        });

    }

    abstract protected function seedDatabase();
}

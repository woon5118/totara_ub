<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package totara_competency
 */

namespace degeneration;

use degeneration\items\item;
use Faker\Factory;
use Faker\Generator;

/**
 * Auto-loading our pseudo PSR-4
 */
spl_autoload_register(function ($class_name) {
    if (strpos($class_name, 'degeneration') === 0) {
        $class_name = substr($class_name, 13);
        $class_name = str_replace('\\', DIRECTORY_SEPARATOR, $class_name);

        if (file_exists($file = __DIR__ . DIRECTORY_SEPARATOR . $class_name . '.php')) {
            include_once $file;
        }

    }
});

define('CLI_SCRIPT', 'yes');

require_once(__DIR__ . '/../../../../config.php');
require_once(App::config()->libdir . '/phpunit/classes/util.php');

if (!file_exists(App::config()->dirroot . '/vendor/fzaninotto/faker/src/autoload.php')) {
    echo "In order to execute this script you need to execute: 'composer require fzaninotto/faker'!" . PHP_EOL;
    echo "Please don't commit changes to composer.json and composer.lock files." . PHP_EOL;
    exit(1);
}

require_once App::config()->dirroot . '/vendor/fzaninotto/faker/src/autoload.php';

class App {

    /**
     * Size for the generated data
     *
     * @var string|null
     */
    protected $size = null;

    /**
     * Faker instance
     *
     * @var Generator;
     */
    protected static $faker = null;

    /**
     * Do generate something
     */
    public static function do() {
        $app = new static();
        $app->set_size()
            ->generate();
    }

    public function set_size(string $size = 'sm') {
        $this->size = strtolower($size);

        return $this;
    }

    public function get_item_size(string $key = null) {
        $size = $this->size ?: 'm';

        $method = "get_{$size}_size";

        if (!method_exists($this, $method)) {
            throw new \Exception("Size '{$size}' is not supported yet");
        }

        return $key ? ($this->{$method}()[$key] ?? null) : $this->{$method}();
    }

    public function generate() {
        echo 'There is nothing to generate yet';

        return $this;
    }

    public function output(string $message) {
        echo $message . PHP_EOL;

        return $this;
    }

    /**
     * Get data testing generator
     *
     * @return \testing_data_generator
     */
    public static function generator() {
        return \phpunit_util::get_data_generator();
    }

    /**
     * Get totara competency generator
     *
     * @return \totara_competency_generator
     */
    public static function competency_generator() {
        return static::generator()->get_plugin_generator('totara_competency');
    }

    /**
     * @return Generator
     */
    public static function faker() {
        if (!static::$faker) {
            static::$faker = Factory::create();
        }

        return static::$faker;
    }

    /**
     * Transaction
     *
     * @param \Closure $closure
     * @return mixed|null
     */
    public static function transaction(\Closure $closure) {
        $transaction = self::db()->start_delegated_transaction();
        try {
            $result = $closure();
        } catch (\Exception $exception) {
            $transaction->rollback($exception);
            // ^^ Throws another stupid exception anyway
            return null;
        }
        $transaction->allow_commit();

        return $result;
    }

    /**
     * Database connection
     *
     * @return \moodle_database
     */
    public static function db(): \moodle_database {
        return $GLOBALS['DB'];
    }

    /**
     * Configuration object
     *
     * @return \stdClass
     */
    public static function config(): \stdClass {
        return $GLOBALS['CFG'];
    }

    /**
     * Return a number which is a percentage of the total count
     *
     * @param int $count
     * @param int $percentage
     * @return int
     */
    public function get_percentage(int $count, int $percentage): int {
        return $count * $percentage / 100;
    }
}

class Cache {

    /**
     * Cached items library
     *
     * @var array
     */
    protected $items = [];

    /**
     * Cache instance
     *
     * @var null|static
     */
    protected static $cache = null;

    /**
     * Cache constructor.
     */
    private function __construct() {
        static::$cache = $this;
    }

    /**
     * Get cache instance
     *
     * @return static
     */
    public static function get() {
        if (!static::$cache) {
            return new static();
        }

        return static::$cache;
    }

    /**
     * Add created item to the cache
     *
     * @param item $item
     * @return $this
     */
    public function add(item $item) {

        if (!isset($this->items[$class = get_class($item)])) {
            $this->items[$class] = [];
        }

        $this->items[$item->get_data('id')] = $item;

        return $this;
    }

    /**
     * Get an item (items) from cache
     *
     * @param string $class Item class to get
     * @param int|null $id Item id
     * @return array|mixed|null
     */
    public function fetch(string $class, ?int $id = null) {
        if (is_null($id)) {
            return $this->items[$class] ?? [];
        }

        return $this->items[$class][$id] ?? null;
    }
}
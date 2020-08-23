<?php
/**
 * This file is part of Totara Core
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * MIT License
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package totara_tui
 */

namespace totara_tui\local\mediation;

use core\lock\lock;
use core\lock\lock_config;

/**
 * Resolver class
 *
 * This class is responsible for resolving the content to return, and uses the mediator class
 * to delivery it to the client.
 *
 * When mediating anything from TUI we need to use a resolver and mediator to do so.
 */
abstract class resolver {

    /**
     * The mediator that will be used to deliver the actual content.
     * @var mediator
     */
    private $mediator;

    /**
     * The revision number, -1 if we're in developer mode.
     * @var int
     */
    private $rev;

    /**
     * The path to the cachefile for the mediated resource.
     * @var string
     */
    private $cachefile;

    /**
     * The etag for the content being resolved.
     * How this is calculated is up to the implementing resolver.
     * @var string
     */
    private $etag;

    /**
     * Resolver constructor.
     * @param string $mediator The class name of the mediator to use to deliver content.
     * @param string $rev The revision number for this resource request.
     * @throws \coding_exception If mediator is not available or of the correct type.
     */
    public function __construct(string $mediator, string $rev) {
        $this->rev = $rev;
        if (!class_exists($mediator)) {
            throw new \coding_exception('The given mediator class does not exist', $mediator);
        }
        if (!isset(\class_parents($mediator)[mediator::class])) {
            throw new \coding_exception('The given mediator does not parent the mediator abstract class', $mediator);
        }
        $this->mediator = new $mediator($this->calculate_etag());
    }

    /**
     * Returns the path to the cachefile for this resource.
     * @return string
     */
    abstract protected function calculate_cachefile(): string;

    /**
     * Returns the etag to use for this resource.
     * @return string
     */
    abstract protected function calculate_etag(): string;

    /**
     * Returns the SHA sum that should be used for comparison against the etag.
     * @return string
     */
    abstract protected function get_sha_for_etag_comparison(): string;

    /**
     * Returns the content that should be cached
     * @return string|file
     */
    abstract protected function get_content_to_cache();

    /**
     * Returns true if the resolver supports a developer mode resolution. False otherwise.
     * @return bool
     */
    protected function support_dev_mode(): bool {
        return true;
    }

    /**
     * The rev for this resolver
     * @return int
     */
    final public function get_rev() {
        return $this->rev;
    }

    /**
     * Resolve the requested resource.
     */
    final public function resolve() {
        if (defined('ABORT_AFTER_CONFIG') && ABORT_AFTER_CONFIG && (!defined('ABORT_AFTER_CONFIG_CANCEL') || !ABORT_AFTER_CONFIG_CANCEL)) {
            $this->delivery_from_cache();
        } else if ($this->should_use_dev_mode()) {
            $this->deliver_dev_mode();
        } else {
            $this->deliver_production_mode();
        }
    }

    /**
     * Returns true of the resolution should use the dev mode.
     * @return bool
     */
    final public function should_use_dev_mode(): bool {
        return ($this->support_dev_mode() && $this->rev == '-1');
    }

    /**
     * Returns the path to the cachefile for this resource.
     * @return string
     */
    final protected function get_cachefile(): string {
        if ($this->cachefile === null) {
            $this->cachefile = $this->calculate_cachefile();
        }
        return $this->cachefile;
    }

    /**
     * Return the contents of the cache file.
     * @return string
     */
    final protected function get_cachefile_contents(): string {
        return file_get_contents($this->get_cachefile());
    }

    /**
     * Returns true if the cache file exists, false otherwise.
     * @return bool
     */
    final protected function cache_file_exists(): bool {
        return file_exists($this->get_cachefile());
    }

    /**
     * Unlinks the cachefile.
     */
    final protected function unlink_cache_file() {
        @unlink($this->get_cachefile());
    }

    /**
     * Returns the etag for this resource.
     * @return string
     */
    final protected function get_etag() {
        if ($this->etag === null) {
            $this->etag = $this->calculate_etag();
        }
        return $this->etag;
    }

    /**
     * Stores the given contents in the cachefile.
     * @param string $absolutepath
     * @param string|file $content
     * @return bool
     */
    final protected function store_in_cache(string $absolutepath, $content): bool {
        global $CFG;

        clearstatcache();
        if (!file_exists(dirname($absolutepath))) {
            make_localcache_directory('totara_tui', false);
            @mkdir(dirname($absolutepath), $CFG->directorypermissions, true);
        }

        // Prevent serving of incomplete file from concurrent request,
        // the rename() should be more atomic than fwrite().
        ignore_user_abort(true);

        // The strategy is to create a temp file that is the cached content, and then to move it into place.
        // This will avoid race conditions, and partial file serve problems.
        $temp_file = $absolutepath . '-' . bin2hex(random_bytes(6)) . '.tmp';

        if ($content instanceof file) {
            $content_file = (string)$content;
            if (strpos($content_file, '..') !== false) {
                // This should never happen, don't entertain it, just quit.
                debugging('Safety check: path traversal is not allowed', DEBUG_DEVELOPER);
                return $this->store_in_cache($absolutepath, 'Invalid file path provided for copying.');
            }
            if (substr($content_file, 0, strlen($CFG->srcroot)) !== $CFG->srcroot) {
                debugging('Safety check: attempted to cache file from outside of srcroot directory.', DEBUG_DEVELOPER);
                return $this->store_in_cache($absolutepath, 'Invalid file path provided for copying.');
            }

            // Prevent serving of incomplete file from concurrent request,
            // Rename should be more atomic the copy if the file is large.
            copy($content_file, $temp_file);
        } else {
            // Write out the single file for all those using decent browsers.
            $fp = fopen($temp_file, 'xb');
            if (!$fp) {
                debugging('Unable to read create tempfile ' . $temp_file, DEBUG_DEVELOPER);
                return false;
            }
            fwrite($fp, $content);
            fclose($fp);
        }

        // Tidy things up.
        rename($temp_file, $absolutepath);
        @chmod($absolutepath, $CFG->filepermissions);
        @unlink($temp_file); // Just in case anything fails.

        ignore_user_abort(false);
        if (connection_aborted()) {
            die;
        }

        return true;
    }

    /**
     * Deliver the content from the cache, if it exists in the cache.
     *
     * If the content can be delivered this function will exit.
     * If the content exists on the client, is stale,
     * If the content does not exist in the cache this function will pass through and code will continue executing.
     */
    final protected function delivery_from_cache() {
        $cachefile = $this->get_cachefile();
        if (file_exists($cachefile)) {
            if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                // We do not actually need to verify the etag value because our files
                // never change in cache because we increment the rev counter.
                $this->mediator->send_unmodified_from_cache();
            }
            $this->mediator->send_cached_file($cachefile);
        }
    }

    /**
     * Delivers the resource for developer mode.
     */
    protected function deliver_dev_mode() {
        $cache = \cache::make_from_params(\cache_store::MODE_APPLICATION, 'totara_tui', 'scss_cache');
        $last_sha = $cache->get($this->get_etag());
        $sha = false;

        $cachefile_exists = $this->cache_file_exists();

        if ($cachefile_exists && $last_sha === false) {
            // We have a cachefile but we don't know the last sha that was used.
            // This shouldn't happen but... regenerate the file to ensure we have the correct content.
            @unlink($this->get_cachefile());
            $cachefile_exists = false;
        }
        if ($cachefile_exists) {
            // The cache file exists, compare the SHA of the theme files to the SHA we have stored in the cache and if they
            // match then we can use the cachefile, otherwise we'll need to generate.
            $sha = $this->get_sha_for_etag_comparison();
            if ($sha !== $last_sha) {
                // The sha' have changed, we need to regenerate.
                $this->unlink_cache_file();
                $cachefile_exists = false;
            }
        }

        $content = false;
        if (!$cachefile_exists) {
            // The cache file does not
            if ($sha === false) {
                $sha = $this->get_sha_for_etag_comparison();
            }
            $content = $this->get_content_to_cache();
            $this->store_in_cache($this->get_cachefile(), $content);
            $cache->set($this->get_etag(), $sha);
        }

        // Regenerate the etag to include the sha now that we know what is it.
        $this->mediator->update_etag($this->get_etag());

        // Next we're going to check if the browser has an old version of the css file, but that is unchanged (even though
        // it is expired). If that is the case then the sha will match the etag and we can tell the browser to use the
        // stale but correct file that it has.
        if ($this->mediator->compare_if_none_match_etag()) {
            // The client has an old version that should not be cached, but it is correct and accurate to what we are about
            // to server. This will be quicker.
            $this->mediator->send_unmodified_from_cache();
        } else {
            if ($content === false || $content instanceof file) {
                // We need to read it from the cache file.
                $content = $this->get_cachefile_contents();
            }
            $this->mediator->send_uncached($content);
        }
    }

    /**
     * Delivers the resource for production mode.
     */
    protected function deliver_production_mode() {
        // Make sure that only one client is generating CSS at a time.
        // All other clients who got to this path can wait until the first completes.
        $lock = $this->get_generation_lock();

        // We're out of the lock, check if we were waiting and the file now exists thanks to someone else.
        if ($this->cache_file_exists()) {
            $lock->release();
            $this->mediator->send_cached_file($this->get_cachefile());
        }

        $content = $this->get_content_to_cache();

        $this->store_in_cache($this->get_cachefile(), $content);

        // Now that the CSS has been generated and/or stored, release the lock.
        // This will allow waiting clients to use the newly generated and stored CSS.
        $lock->release();

        // Real browsers - this is the expected result!
        if ($content instanceof file) {
            $this->mediator->send_cached_file($this->get_cachefile());
        } else {
            $this->mediator->send_cached($content);
        }
    }

    /**
     * Acquires a generation lock.
     * @return object
     */
    protected function get_generation_lock(): object {
        // Standardise the return so that we know we have the release method.
        return new class($this->get_etag()) {
            /** @var lock|null  */
            private $lock;
            public function __construct(string $key) {
                $lockfactory = lock_config::get_lock_factory('totara_tui_resource_generation');
                $this->lock = $lockfactory->get_lock($key, rand(90, 120), 600);
            }
            /** Releases the lock if one was acquired */
            public function release() {
                if ($this->lock) $this->lock->release();
            }
        };
    }
}

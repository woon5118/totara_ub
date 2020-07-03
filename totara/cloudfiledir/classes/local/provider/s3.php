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
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @package totara_cloudfiledir
 */

namespace totara_cloudfiledir\local\provider;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

/**
 * S3 AWS cloud file storage provider.
 */
final class s3 extends base {
    /**
     * @var S3Client
     */
    private $client;

    /**
     * Is the provider ready to be connected?
     *
     * @return bool
     */
    public function is_ready(): bool {
        if (strlen($this->bucket) === 0) {
            return false;
        }
        if (class_exists('Aws\S3\Exception\S3Exception')) {
            return true;
        }
        if (!defined('TOTARA_CLOUDFILEDIR_S3_AUTOLOAD')) {
            define('TOTARA_CLOUDFILEDIR_S3_AUTOLOAD', __DIR__ . '/../../../lib/vendor/autoload.php');
        }
        include_once(TOTARA_CLOUDFILEDIR_S3_AUTOLOAD);

        if (!class_exists('Aws\S3\Exception\S3Exception')) {
            // Library not installed manually.
            error_log('Missing Amazon S3 SDK, see totara/cloudfiledir/lib/composer.json');
            return false;
        }
        return true;
    }

    /**
     * Get fully configured and ready to use S3 client.
     * @return S3Client|null
     */
    protected function get_client(): ?S3Client {
        if ($this->client !== null) {
            return ($this->client === false) ? null : $this->client;
        }
        if (!$this->is_ready()) {
            $this->client = false;
            return null;
        }

        // see for more details: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_configuration.html

        $options = $this->options;
        if (empty($options['version'])) {
            $options['version'] = '2006-03-01';
        }
        if (!isset($this->options['region'])) {
            $options['region'] = 'us-east-1';
        }
        if (!empty($options['endpoint']) && !isset($options['use_path_style_endpoint'])) {
            // Undocumented option to support 3rd party stores that emulate S3 without the buckets as extra hosts in URL.
            $options['use_path_style_endpoint'] = true;
        }

        // HINT: Uncomment following when debugging connection issues in PHPUnit.
        //ob_end_flush(); $options['debug'] = true;

        $this->client = new S3Client($options);
        return $this->client;
    }

    /**
     * Test connection to provider.
     *
     * @return bool success
     */
    public function test_connection(): bool {
        $client = $this->get_client();
        if (!$client) {
            return false;
        }

        try {
            return $client->doesBucketExist($this->bucket);
        } catch (S3Exception $ex) {
            $this->log_exception($ex, 'Error testing connection');
            return false;
        }
    }

    /**
     * Is the content with given contenthash available from this store?
     * @param string $contenthash
     * @return bool
     */
    public function is_content_available(string $contenthash): bool {
        $client = $this->get_client();
        if (!$client) {
            return false;
        }

        try {
            return $client->doesObjectExist($this->bucket, $this->get_object_name($contenthash));
        } catch (S3Exception $ex) {
            $this->log_exception($ex, 'Cannot find if content file available ' . $contenthash);
            return false;
        }
    }

    /**
     * Upload content file to cloud store.
     * @param string $contenthash
     * @param string $filepath
     * @return bool success
     */
    public function upload_content(string $contenthash, string $filepath): bool {
        $client = $this->get_client();
        if (!$client) {
            return false;
        }

        try {
            $client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $this->get_object_name($contenthash),
                'SourceFile' => $filepath
            ]);
            return true;
        } catch (S3Exception $ex) {
            $this->log_exception($ex, 'Cannot upload content file ' . $contenthash);
            return false;
        }
    }

    /**
     * Upload content file to cloud store from file handle.
     * @param string $contenthash
     * @param resource $handle
     * @param int $contentlength
     * @return bool success
     */
    public function upload_content_stream(string $contenthash, $handle, int $contentlength): bool {
        $client = $this->get_client();
        if (!$client) {
            return false;
        }
        if (!$handle) {
            return false;
        }

        try {
            $client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $this->get_object_name($contenthash),
                'Body' => $handle,
                'ConentLength' => $contentlength,
            ]);
            @fclose($handle);
            return true;
        } catch (S3Exception $ex) {
            @fclose($handle);
            $this->log_exception($ex, 'Cannot upload content file from stream ' . $contenthash);
            return false;
        }
    }

    /**
     * Download content file from store.
     * @param string $contenthash
     * @param string $filepath
     * @return bool success, false if file does not exist or on error
     */
    public function download_content(string $contenthash, string $filepath): bool {
        $client = $this->get_client();
        if (!$client) {
            return false;
        }

        try {
            $client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $this->get_object_name($contenthash),
                'SaveAs' => $filepath,
            ]);
            return true;
        } catch (S3Exception $ex) {
            $this->log_exception($ex, 'Cannot download content file ' . $contenthash);
            return false;
        }
    }

    /**
     * Delete content file from store
     * @param string $contenthash
     * @return bool success
     */
    public function delete_content(string $contenthash): bool {
        $client = $this->get_client();
        if (!$client) {
            return false;
        }

        try {
            $client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $this->get_object_name($contenthash),
            ]);
            return true;
        } catch (S3Exception $ex) {
            $this->log_exception($ex, 'Cannot delete content file ' . $contenthash);
            return false;
        }
    }

    /**
     * List all contents in the cloud bucket.
     * @return \Iterator|null returning content hashes null means error
     */
    public function list_contents(): ?\Iterator {
        $client = $this->get_client();
        if (!$client) {
            return null;
        }

        try {
            $listopions = ['Bucket' => $this->bucket];
            if ($this->prefix !== '') {
                $listopions['prefix'] = $this->prefix . '/';
            }
            $pages = $client->getPaginator('ListObjects', $listopions);
            $nameparser = $this->get_object_name_parser();

            return new class($pages, $nameparser) implements \Iterator {
                private $contenthashes;
                private $i = 0;
                private $pages;
                /** @var callable */
                private $nameparser;
                public function __construct(\Iterator $pages, callable $nameparser) {
                    $this->pages = $pages;
                    $this->nameparser = $nameparser;
                    $this->preload_contents();
                }

                private function preload_contents() {
                    if (!$this->pages) {
                        return;
                    }
                    if (!$this->pages->valid()) {
                        $this->pages = null;
                        return;
                    }
                    $current = $this->pages->current();
                    if (isset($current['Contents'])) {
                        foreach ($current['Contents'] as $object) {
                            $contenthash = call_user_func($this->nameparser, $object['Key']);
                            if ($contenthash === false) {
                                continue;
                            }
                            $this->contenthashes[] = $contenthash;
                        }
                    }
                    $this->pages->next();
                    if (!$this->contenthashes) {
                        $this->preload_contents();
                    }
                }

                public function current() {
                    return reset($this->contenthashes);
                }

                public function next() {
                    if (!$this->contenthashes) {
                        $this->preload_contents();
                    }
                    if (!$this->contenthashes) {
                        return;
                    }
                    $this->i++;
                    reset($this->contenthashes);
                    unset($this->contenthashes[key($this->contenthashes)]);
                }

                public function key() {
                    return $this->i;
                }

                public function valid() {
                    return $this->contenthashes;
                }

                public function rewind() {
                    if ($this->i !== 0) {
                        throw new \coding_exception('contents iterator cannot be rewound');
                    }
                }
            };
        } catch (S3Exception $ex) {
            $this->log_exception($ex, 'Cannot obtain list of content files');
            return null;
        }
    }

    /**
     * Delete all files from bucket, this is intended for tests only!
     *
     * @internal
     * @return bool success
     */
    public function clear_test_bucket(): bool {
        if (!PHPUNIT_TEST) {
            throw new \coding_exception('Bucket clearing is intended for phpunit tests only');
        }

        $client = $this->get_client();
        if (!$client) {
            return false;
        }

        $contenthashes = $this->list_contents();
        if ($contenthashes === null) {
            return false;
        }

        foreach ($contenthashes as $contenthash) {
            // NOTE: no need to optimise this, it is for testing only!
            $client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $this->get_object_name($contenthash),
            ]);
        }
        return true;
    }
}

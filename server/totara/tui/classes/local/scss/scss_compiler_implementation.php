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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package core
 */

namespace totara_tui\local\scss;

use ScssPhp\ScssPhp\Formatter\OutputBlock;
use totara_tui\local\scss\transforms\transform;

/**
 * SCSS compiler implementation.
 *
 * Extends \ScssPhp\ScssPhp\Compiler with support for webpack-style loader
 * transforms, otherwise works identically.
 *
 * To use transforms, first register the transform with register_transform(),
 * then prefix the import with the transform name and a !:
 * `@import 'definitions_only!examplestyle`
 *
 * In the case of multiple transforms, they are executed from right to left,
 * with the output of each transform being passed to the next.
 */
class scss_compiler_implementation extends \ScssPhp\ScssPhp\Compiler {
    /**
     * @var array Map of transform names to instances
     */
    private $transforms = [];

    /**
     * Check if a transform with the specified name is registered
     *
     * @param string $name
     * @return bool
     */
    public function has_transform(string $name): bool {
        return isset($this->transforms[$name]);
    }

    /**
     * Register a transform with a particular name
     *
     * @param string $name
     * @param transform $transform
     */
    public function register_transform(string $name, transform $transform) {
        $this->transforms[$name] = $transform;
    }

    /**
     * {@inheritdoc}
     */
    public function findImport($url) {
        // handle transforms in url - resolve just path part and readd transforms after
        $pos = strrpos($url, '!');
        if ($pos !== false) {
            $transforms = substr($url, 0, $pos);
            $path = substr($url, $pos + 1);
            $result = $this->baseFindImport($path);
            return $result === null ? null : "$transforms!$result";
        }

        return $this->baseFindImport($url);
    }

    /**
     * Return the file path for an import url if it exists
     *
     * @param string $url
     * @return string|null
     */
    protected function baseFindImport($url) {
        // copied from lib/scssphp/Compiler.php findImport()
        // changes:
        // * updated to use $this->is_file() instead of is_file()

        $urls = [];

        // for "normal" scss imports (ignore vanilla css and external requests)
        if (! preg_match('~\.css$|^https?://~', $url)) {
            // try both normal and the _partial filename
            $urls = [$url, preg_replace('~[^/]+$~', '_\0', $url)];
        }

        $has_extension = preg_match('/[.]s?css$/', $url);

        foreach ($this->importPaths as $dir) {
            if (is_string($dir)) {
                // check urls for normal import paths
                foreach ($urls as $full) {
                    $separator = (
                        ! empty($dir) &&
                        substr($dir, -1) !== '/' &&
                        substr($full, 0, 1) !== '/'
                    ) ? '/' : '';
                    $full = $dir . $separator . $full;

                    if ($this->is_file($file = $full . '.scss') ||
                        ($has_extension && $this->is_file($file = $full))
                    ) {
                        return $file;
                    }
                }
            } else if (is_callable($dir)) {
                // check custom callback for import path
                $file = call_user_func($dir, $url);

                if (! is_null($file)) {
                    return $file;
                }
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function importFile($path, OutputBlock $out) {
        $request = $this->parse_import_request($path);

        $realpath = realpath($request->path);
        if ($realpath === false) {
            $realpath = $path;
        }

        $cache_key = ($request->transforms ? implode('!', $request->transforms) : '') . $realpath;

        if (isset($this->importCache[$cache_key])) {
            $this->handleImportLoop($realpath);

            $tree = $this->importCache[$cache_key];
        } else {
            $code = $this->load_file($request->path);
            $resource = new transform_resource(
                $request->path,
                function ($code, $path) {
                    $parser = $this->parserFactory($path);
                    return $parser->parse($code);
                }
            );
            $resource->set_code($code);
            // run any requested transforms on source
            $this->execute_transforms($request->transforms, $resource);
            $tree = $resource->get_ast();
            $this->importCache[$cache_key] = $tree;
        }

        $pi = pathinfo($request->path);
        array_unshift($this->importPaths, $pi['dirname']);
        $this->compileChildrenNoReturn($tree->children, $out);
        array_shift($this->importPaths);
    }

    /**
     * Parse import request, e.g. 'foo' or 'transform1!transform2!foo'.
     *
     * @param string $request
     * @return object Object with 'path' and 'transforms' fields.
     */
    protected function parse_import_request(string $request): object {
        $pos = strrpos($request, '!');
        $transforms = [];
        if ($pos === false) {
            $path = $request;
        } else {
            $path = substr($request, $pos + 1);
            if ($pos !== 0) {
                $transforms = explode('!', substr($request, 0, $pos));
            }
        }

        return (object)[
            'path' => $path,
            'transforms' => $transforms,
        ];
    }

    /**
     * Check if the provided file exists and is a file.
     *
     * @param string $path
     * @return bool
     */
    protected function is_file(string $path): bool {
        return is_file($path);
    }

    /**
     * Load the provided file from the filesystem.
     *
     * @param string $path
     * @return string|null
     */
    protected function load_file(string $path): ?string {
        $content = file_get_contents($path);
        if ($content === false) {
            return null;
        }
        return $content;
    }

    /**
     * Execute the provided transforms on the provided resource.
     *
     * @param string[] $names Transform names
     * @param transform_resource $resource Resource to transform.
     */
    protected function execute_transforms(array $names, transform_resource $resource) {
        // transforms execute from right to left (like webpack)
        for ($i = count($names) - 1; $i >= 0; $i--) {
            $this->execute_transform($names[$i], $resource);
        }
    }

    /**
     * Execute the provided transform on the provided resource.
     *
     * @param string $name Transform name
     * @param transform_resource $resource Resource to transform.
     */
    protected function execute_transform(string $name, transform_resource $resource) {
        if (!$this->has_transform($name)) {
            throw new \coding_exception("Unknown transform \"$name\"");
        }
        $transform = $this->transforms[$name];
        $resource->set_current_transform_name($name);
        $transform->execute($resource);
        $resource->set_current_transform_name(null);
    }
}

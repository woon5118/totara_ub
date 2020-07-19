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

namespace core\tui\scss;

use ScssPhp\ScssPhp\Block;

/**
 * Represents an SCSS file being transformed.
 */
class transform_resource {
    /**
     * @var callable Function that takes SCSS code and returns an AST.
     */
    private $parse;

    /**
     * @var string Original file path.
     */
    private $path;

    /**
     * @var string SCSS code.
     */
    private $code;

    /**
     * @var object Abstract syntax tree.
     */
    private $ast;

    /**
     * @var string Name of the current transform. Only used for debugging.
     */
    private $current_transform_name;

    /**
     * @var string Name of the transform that switched to AST-only.
     */
    private $ast_only_transform_name;

    /**
     * Construct a new resource.
     *
     * @param string $path Original path of resource.
     * @param callable $parse Function of signature ($code, $path) which turns code into an AST.
     */
    public function __construct(string $path, callable $parse) {
        $this->path = $path;
        $this->parse = $parse;
    }

    /**
     * Get the path of the file we're transforming.
     *
     * @return string
     */
    public function get_path(): ?string {
        return $this->path;
    }

    /**
     * Get the code we're transforming. Will fail if $this->set_ast() has been called.
     *
     * @throws \coding_exception if resource is AST-only.
     * @return string
     */
    public function get_code(): string {
        if ($this->is_ast_only()) {
            throw new \coding_exception(
                "{$this->current_transform_name}: Code is no longer available for this resource " .
                "because the \"{$this->ast_only_transform_name}\" transform switched it to being AST-only."
            );
        }
        return $this->code;
    }

    /**
     * Update the code after changing it.
     *
     * @param string $code
     */
    public function set_code(string $code) {
        $this->code = $code;
        $this->ast = null;
    }

    /**
     * Get the AST for this resource. Will parse from $code if not in AST-only mode.
     *
     * @return \ScssPhp\ScssPhp\Block
     */
    public function get_ast(): Block {
        if (!isset($this->ast)) {
            if (!isset($this->code)) {
                throw new \coding_exception("AST and source code are unavailable for this resource");
            }
            $this->ast = ($this->parse)($this->code, $this->path);
        }
        return $this->ast;
    }

    /**
     * Update the AST. Will also switch to AST-only mode.
     *
     * @param \ScssPhp\ScssPhp\Block $ast
     */
    public function set_ast(Block $ast) {
        $this->ast = $ast;
        $this->mark_ast_modified();
    }

    /**
     * Flag the AST as having been modified. Switches to AST-only mode, as we cannot generate SCSS code from the AST.
     */
    public function mark_ast_modified() {
        if ($this->code !== null) {
            $this->ast_only_transform_name = $this->current_transform_name;
        }
        $this->code = null;
    }

    /**
     * Get whether the resource is AST-only or not.
     *
     * @return bool
     */
    public function is_ast_only(): bool {
        return isset($this->ast) && !isset($this->code);
    }

    /**
     * Set the current transform name. Only used for debugging.
     *
     * @param string $transform_name
     */
    public function set_current_transform_name(?string $transform_name) {
        $this->current_transform_name = $transform_name;
    }
}

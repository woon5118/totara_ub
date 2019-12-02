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
 * @package totara_core
 */

/**
 * This file contains code for working with 2D geometry and 2D math, for example
 * in positioning calculations.
 */

/**
 * Represents an x and y coordinate pair in two-dimensional space.
 */
export class Point {
  constructor(x, y) {
    this.x = x;
    this.y = y;
  }

  /**
   * Adds a Point to the location of this Point and returns a new Point.
   *
   * @param {Point} point
   * @returns {Point}
   */
  add(point) {
    return new Point(this.x + point.x, this.y + point.y);
  }

  /**
   * Subtracts a Point from the location of this Point and returns a new Point.
   *
   * @param {Point} point
   * @returns {Point}
   */
  sub(point) {
    return new Point(this.x - point.x, this.y - point.y);
  }
}

/**
 * Stores a width and height pair.
 */
export class Size {
  constructor(width, height) {
    this.width = width;
    this.height = height;
  }
}

/**
 * Describes the width, height, and location of a rectangle.
 */
export class Rect {
  constructor(x, y, width, height) {
    this.left = x;
    this.top = y;
    this.right = x + width;
    this.bottom = y + height;
    this.width = width;
    this.height = height;
  }

  static fromPositions({ left, top, right, bottom }) {
    return new Rect(left, top, right - left, bottom - top);
  }

  getPosition() {
    return new Point(this.left, this.top);
  }

  getSize() {
    return new Size(this.width, this.height);
  }

  /**
   * Adds a point to the location of this Rect and returns a new Rect.
   *
   * @param {Point} point
   * @returns {Rect}
   */
  add(point) {
    return new Rect(
      this.left + point.x,
      this.top + point.y,
      this.width,
      this.height
    );
  }

  /**
   * Subtracts a point from the location of this Rect and returns a new Rect.
   *
   * @param {Point} point
   * @returns {Rect}
   */
  sub(point) {
    return new Rect(
      this.left - point.x,
      this.top - point.y,
      this.width,
      this.height
    );
  }
}

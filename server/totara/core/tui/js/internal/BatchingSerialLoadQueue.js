/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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

import pending from 'totara_core/pending';

const STATE_IDLE = 0;
const STATE_WAITING = 1;
const STATE_EXECUTING = 2;

/**
 * Queue requests to be loaded, but only execute one batch at once
 * (next batch must wait until the first has completed).
 *
 * @internal
 */
export default class BatchingSerialLoadQueue {
  /**
   * @param {object} options
   * @param {function} options.handler
   * @param {number} [options.wait=0]
   * @param {function} [options.equals]
   */
  constructor({ handler, wait = 0, equals }) {
    this._handler = handler;
    this._wait = wait;
    this._equals = equals || ((a, b) => a == b);

    // requests in queue
    this._queuedRequests = [];
    // callbacks to call when queue loaded
    this._queuedCallbacks = [];

    this.state = STATE_IDLE;
  }

  /**
   * Add an array of requests to the queue.
   *
   * @param {array} requests
   * @returns {Promise}
   */
  enqueue(requests) {
    return new Promise((resolve, reject) => {
      if (Array.isArray(requests)) {
        requests.forEach(x => {
          if (!this.contains(x)) {
            this._queuedRequests.push(x);
          }
        });
      }

      this._queuedCallbacks.push([resolve, reject]);

      this._checkRunner();
    });
  }

  /**
   * Check if item is in queue.
   *
   * @param {*} item
   * @returns {boolean}
   */
  contains(item) {
    return this._queuedRequests.some(x => this._equals(x, item));
  }

  /**
   * Check to see if timeout for runner should be started.
   */
  _checkRunner() {
    if (this.state == STATE_IDLE && this._queuedRequests.length > 0) {
      this.state = STATE_WAITING;
      const done = pending();
      setTimeout(() => {
        done();
        this._runQueue();
      }, this._wait);
    }
  }

  /**
   * Drain and process queue.
   */
  _runQueue() {
    this.state = STATE_EXECUTING;
    const queue = this._queuedRequests;
    this._queuedRequests = [];
    const callbacks = this._queuedCallbacks;
    this._queuedCallbacks = [];
    Promise.resolve(this._handler(queue))
      .then(
        () => callbacks.forEach(x => x[0]()),
        () => callbacks.forEach(x => x[1]())
      )
      .then(() => {
        this.state = STATE_IDLE;
        this._checkRunner();
      });
  }
}

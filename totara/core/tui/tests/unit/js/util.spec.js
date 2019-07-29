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

import {
  uniqueId,
  pull,
  pick,
  result,
  memoize,
  memoizeLoad,
  url,
} from 'totara_core/util';

jest.mock('totara_core/config');

let counter = 1;
const incr = () => counter++;

describe('uniqueId', () => {
  it('returns an incrementing unique id', () => {
    let lastResult = uniqueId();
    for (let i = 0; i < 5; i++) {
      const result = uniqueId();
      expect(result).toBe(lastResult + 1);
      lastResult = result;
    }
  });
});

describe('pull', () => {
  it('removes the provided element', () => {
    const arr = [1, 2, 3];
    pull(arr, 2);
    expect(arr).toEqual([1, 3]);
  });

  it('only removes the first instance', () => {
    const arr = [1, 2, 3, 2];
    pull(arr, 2);
    expect(arr).toEqual([1, 3, 2]);
  });

  it('does nothing if element is not found', () => {
    const arr = [1, 3];
    pull(arr, 2);
    expect(arr).toEqual([1, 3]);
  });
});

describe('pick', () => {
  it('copies out selected keys', () => {
    expect(pick({ a: 1, b: 2, c: 3 }, ['a', 'c'])).toEqual({ a: 1, c: 3 });
  });

  it('ignores not found keys', () => {
    expect(pick({ a: 1 }, ['c'])).toEqual({});
    expect(pick({}, ['c'])).toEqual({});
  });

  it('requires keys to be an array', () => {
    expect(() => pick({}, {})).toThrow('keys must be an array');
  });
});

describe('result', () => {
  it('calls a functin and returns the result', () => {
    expect(result(() => 3)).toBe(3);
  });

  it('passes non-function values through', () => {
    expect(result(1)).toBe(1);
    expect(result('b')).toBe('b');
    var obj = {};
    expect(result(obj)).toBe(obj);
  });
});

describe('memoize', () => {
  it('returns the same result for each invocation', () => {
    const fn = jest.fn(() => incr());
    const memoFn = memoize(fn);

    const result = memoFn();
    // check that we get the same result calling memoFn a second time
    expect(memoFn()).toBe(result);

    expect(fn).toHaveBeenCalledTimes(1);
  });

  it('keys result by argument', () => {
    const fn = jest.fn(key => key + '-' + incr());
    const memoFn = memoize(fn);

    const resultA = memoFn('a');
    expect(memoFn('a')).toBe(resultA);
    expect(fn).toHaveBeenCalledTimes(1);
    expect(fn).toHaveBeenLastCalledWith('a');

    const resultB = memoFn('b');
    expect(memoFn('b')).toBe(resultB);
    expect(fn).toHaveBeenCalledTimes(2);
    expect(fn).toHaveBeenLastCalledWith('b');
  });

  it('allows providing a custom key function', () => {
    const add = jest.fn((a, b) => a + b);
    const keyFn = jest.fn((a, b) => `${a}-${b}`);
    const memoAdd = memoize(add, keyFn);

    memoAdd(1, 2);
    memoAdd(1, 2);
    expect(add).toHaveBeenCalledTimes(1);
  });
});

describe('memoizeLoad', () => {
  it('only calls fn once', async () => {
    const fn = jest.fn(async () => 3);
    const memoLoad = memoizeLoad(fn);

    const promise1 = memoLoad();
    await expect(promise1).resolves.toBe(3);
    // exact same promise is returned from subsequent calls
    expect(memoLoad()).toBe(promise1);
  });

  it('unless it rejects', async () => {
    const fn = jest
      .fn()
      .mockReturnValueOnce(Promise.reject('error'))
      .mockReturnValueOnce(Promise.resolve(2));
    const memoLoad = memoizeLoad(fn);

    await expect(memoLoad()).rejects.toBe('error');

    const promise1 = memoLoad();
    await expect(promise1).resolves.toBe(2);
    // exact same promise is returned from subsequent calls
    expect(memoLoad()).toBe(promise1);
  });
});

describe('url', () => {
  it('prepends base url unless absolute', () => {
    expect(url('/hello')).toBe('http://foo/hello');
    expect(url('/hello.php')).toBe('http://foo/hello.php');
    expect(url('/hello?a=b')).toBe('http://foo/hello?a=b');
    expect(url('//bar/hello')).toBe('//bar/hello');
    expect(url('https://bar/hello')).toBe('https://bar/hello');
    expect(url('foo://bar/hello')).toBe('foo://bar/hello');
    expect(url('http://bar/hello.php')).toBe('http://bar/hello.php');
  });

  it('requires a leading /', () => {
    expect(() => url('hello')).toThrow();
  });

  it('formats the provided params', () => {
    expect(
      url('/hello', { a: 'one&two three', b: 2, c: true, d: [3, 4] })
    ).toBe('http://foo/hello?a=one%26two%20three&b=2&c=true&d[0]=3&d[1]=4');

    expect(
      url('/hello', {
        d: [3, 4],
        e: { f: 5, g: 6 },
        f: [
          [1, 2],
          [3, 4],
        ],
        g: { f: { a: 5 }, g: { a: 6 } },
      })
    ).toBe(
      'http://foo/hello?d[0]=3&d[1]=4&e[f]=5&e[g]=6&' +
        'f[0][0]=1&f[0][1]=2&f[1][0]=3&f[1][1]=4&' +
        'g[f][a]=5&g[g][a]=6'
    );

    expect(url('/xyz?a=1', { b: 2 })).toBe('http://foo/xyz?a=1&b=2');
    expect(url('/xyz?a=1&', { b: 2 })).toBe('http://foo/xyz?a=1&b=2');
    expect(url('/xyz?', { b: 2 })).toBe('http://foo/xyz?b=2');
    expect(url('/xyz', {})).toBe('http://foo/xyz');
    expect(url('/xyz.php?a=1', { b: 2 })).toBe('http://foo/xyz.php?a=1&b=2');
    expect(url('/xyz.php', {})).toBe('http://foo/xyz.php');
    expect(url('//xyz', { b: 2 })).toBe('//xyz?b=2');
  });

  it('passes through URL constructor if defined', () => {
    expect(url('/hello')).toBe('http://foo/hello');
    global.URL = jest.fn();
    url('/oo');
    expect(global.URL).toHaveBeenLastCalledWith('http://foo/oo');
    delete global.URL;
  });
});

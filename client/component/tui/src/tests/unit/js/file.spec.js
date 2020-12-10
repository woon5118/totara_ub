/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * Totara Enterprise Extensions is provided only to Totara
 * Learning Solutions LTD's customers and partners, pursuant to
 * the terms and conditions of a separate agreement with Totara
 * Learning Solutions LTD or its affiliate.
 *
 * If you do not have an agreement with Totara Learning Solutions
 * LTD, you may not access, use, modify, or distribute this software.
 * Please contact [licensing@totaralearning.com] for more information.
 *
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @module tui
 */
import { getReadableSize } from 'tui/file';

jest.mock('tui/i18n', () => {
  function MockLangString(key, component, param) {
    if (!(this instanceof MockLangString)) {
      return new MockLangString(key, component, param);
    }

    this.key = key;
    this.component = component;
    this.param = param;
  }

  MockLangString.prototype = {
    constructor: MockLangString,

    toRequest() {
      return {
        component: this.component,
        key: this.key,
      };
    },

    loaded() {
      return true;
    },

    toString() {
      const map = {
        core: {
          sizegb: 'GB',
          sizemb: 'MB',
          sizekb: 'KB',
          sizeb: 'Byte',
        },
        totara_core: {
          filesize: '{{size}} {{unit}}',
        },
      };

      let str = map[this.component][this.key];
      if (!this.param) {
        return str;
      }

      return str.replace(/\{\{(.*?)\}\}/gi, (full, prop) => {
        return this.param[prop] != null ? this.param[prop] : full;
      });
    },
  };

  return {
    langString(key, component, param) {
      return new MockLangString(key, component, param);
    },
    loadLangStrings() {
      return Promise.resolve('x');
    },
  };
});

describe('getReadAbleSize', () => {
  it('gives GB size', async () => {
    // This is 1 GB
    expect(await getReadableSize(1073741824)).toEqual('1 GB');

    // This is 2 GB
    expect(await getReadableSize(2147483648)).toEqual('2 GB');
  });

  it('gives MB size', async () => {
    // This is 1 MB
    expect(await getReadableSize(1048576)).toEqual('1 MB');

    // This is 2 MB
    expect(await getReadableSize(2097152)).toEqual('2 MB');
  });

  it('gives KB size', async () => {
    // This is 1 KB
    expect(await getReadableSize(1024)).toEqual('1 KB');

    // This is 2 KB
    expect(await getReadableSize(2048)).toEqual('2 KB');
  });

  it('gives Byte size', async () => {
    // This is 100 Bytes
    expect(await getReadableSize(100)).toEqual('100 Byte');

    // This is 102 Bytes
    expect(await getReadableSize(102)).toEqual('102 Byte');
  });
});

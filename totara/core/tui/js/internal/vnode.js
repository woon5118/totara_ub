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

import { pick } from '../../js/util';

const DATA_KEYS = [
  'class',
  'staticClass',
  'style',
  'attrs',
  'props',
  'domProps',
  'on',
  'nativeOn',
  'directives',
  'scopedSlots',
  'slot',
  'ref',
  'key',
];

/**
 * Extract data obect from VNode
 *
 * @param {VNode} vnode
 * @param {boolean} isComp Is component (vs DOM node)
 */
function extractData(vnode, isComp) {
  const data = pick(vnode.data || {}, DATA_KEYS);
  if (isComp) {
    const cOpts = vnode.componentOptions;
    Object.assign(data, {
      props: cOpts.propsData,
      on: cOpts.listeners,
    });
  }

  return data;
}

/**
 * Create a shallow clone of a Vue VNode
 *
 * @param {VNode} vnode
 */
export function cloneVNode(vnode) {
  // use the context that the original vnode was created in.
  const h = vnode.context && vnode.context.$createElement;
  const isComp = !!vnode.componentOptions;
  const isText = !vnode.tag;
  const children = isComp ? vnode.componentOptions.children : vnode.children;

  if (isText) return vnode.text;

  const data = extractData(vnode, isComp);

  const tag = isComp ? vnode.componentOptions.Ctor : vnode.tag;

  const childNodes = children ? children.map(c => cloneVNode(c)) : undefined;

  const cloned = h(tag, data, childNodes);

  if (isComp) {
    cloned.componentOptions.tag = vnode.componentOptions.tag;
  }

  return cloned;
}

/**
 * Normalize listener to common format (array)
 *
 * @param {Function|Function[]} val
 */
function normalizeListeners(val) {
  if (val.fns) {
    return normalizeListeners(val.fns);
  }
  return Array.isArray(val) ? val : [val];
}

/**
 * Merge two listener values
 *
 * @param {Function|Function[]} a
 * @param {Function|Function[]} b
 */
export function mergeListeners(a, b) {
  return normalizeListeners(a).concat(normalizeListeners(b));
}

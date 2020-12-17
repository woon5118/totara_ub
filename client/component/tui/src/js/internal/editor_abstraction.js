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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @module tui
 */

import tui from 'tui/tui';
import apollo from 'tui/apollo_client';
// eslint-disable-next-line no-unused-vars
import { EditorInterface } from 'tui/editor';
import textareaFallback from './editor_textarea_fallback';
import configQuery from 'core/graphql/editor';

/**
 * @typedef {Object} EditorIdentifier
 * @property {string} component
 * @property {string} area
 * @property {number} instanceId
 */

/**
 * Get editor configuration info from the server.
 *
 * @param {object} opts
 * @param {number} opts.format
 * @param {string} opts.variant
 * @param {object} opts.identifier
 * @returns {EditorConfigResult}
 */
export async function getEditorConfig({
  format,
  variant,
  usageIdentifier,
  contextId,
}) {
  const usageId = usageIdentifier;

  const result = await apollo.query({
    query: configQuery,
    variables: {
      framework: 'tui',
      format,
      variant_name: variant,
      context_id: contextId,
      usage_identifier: usageId
        ? {
            component: usageId.component,
            area: usageId.area,
            instance_id: usageId.instanceId,
          }
        : null,
    },
  });

  const config = result.data.editor;

  const hasInterface = !!config.js_module;

  return new EditorConfigResult({
    interface: hasInterface ? config.js_module : textareaFallback,
    options:
      hasInterface && config.variant.options
        ? JSON.parse(config.variant.options)
        : {},
    contextId: config.context_id,
  });
}

class EditorConfigResult {
  /**
   * @private
   * @param {object} opts
   * @param {(string|EditorInterface)} opts.interface
   */
  constructor(opts) {
    this._interface = opts.interface;
    this._options = opts.options;
    this._contextId = opts.contextId;
  }

  /**
   * Get editor interface object/instance.
   *
   * @returns {Promise<EditorInterface>}
   */
  async loadInterface() {
    if (typeof this._interface === 'string') {
      return tui.defaultExport(await tui.import(this._interface));
    } else {
      return this._interface;
    }
  }

  /**
   * @returns {object}
   */
  getEditorOptions() {
    return this._options;
  }

  /**
   * @returns {?number}
   */
  getContextId() {
    return this._contextId;
  }
}

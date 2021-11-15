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
 * @module editor_weka
 */

// eslint-disable-next-line no-unused-vars
import { EditorState } from 'ext_prosemirror/state';
import { getDefaultDocument } from './helpers/editor';

/**
 * Immutable value container for Weka
 */
export default class WekaValue {
  /**
   * @private
   */
  constructor() {
    this._editor = null;
    this._state = null;
    this._doc = null;
    this._html = null;
    this._map = null;
    // prevent this (and state) from becoming reactive
    Object.preventExtensions(this);
  }

  /**
   * Create a WekaValue from editor state.
   *
   * @param {EditorState} editorState
   * @param {Editor} editor
   */
  static fromState(editorState, editor) {
    const ws = new WekaValue();
    ws._editor = editor;
    ws._state = editorState;
    return ws;
  }

  /**
   * Create a WekaValue from a serialized document.
   *
   * @param {object} doc
   */
  static fromDoc(doc) {
    const ws = new WekaValue();
    ws._doc = doc;
    return ws;
  }

  /**
   * Create a WekaValue containing HTML.
   *
   * (used to initialise Weka from HTML source)
   *
   * WekaValues created from HTML cannot be converted to docs or have their state retrieved.
   *
   * @param {string} html
   */
  static fromHtml(html) {
    const ws = new WekaValue();
    ws._html = html;
    return ws;
  }

  /**
   * Create an empty WekaValue.
   */
  static empty() {
    return WekaValue.fromDoc(getDefaultDocument());
  }

  /**
   * Check if WekaValue has been enabled for use with editor.
   *
   * @param {Editor} editor
   * @returns {boolean}
   */
  inflated(editor) {
    return this._editor == editor || (this._map && !!this._map[editor.uid]);
  }

  /**
   * Enable WekaValue for use with editor.
   *
   * @param {Editor} editor
   * @param {EditorState} editorState
   */
  inflate(editor, editorState) {
    if (!this._editor) {
      this._editor = editor;
      this._state = editorState;
    } else {
      // not first editor
      if (!this._map) {
        this._map = {};
      }
      this._map[editor.uid] = editorState;
    }
  }

  /**
   * Get EditorState.
   *
   * @param {Editor} [editor] Get state for this editor. Otherwise will return state for first editor.
   * @returns {EditorState}
   */
  getState(editor = null) {
    if (!editor) {
      return this._state;
    }
    if (this._editor === editor) {
      return this._state;
    } else if (this._map && this._map[editor.uid]) {
      // not first editor
      return this._map[editor.uid];
    } else {
      return null;
    }
  }

  /**
   * Convert to JSON doc format.
   * @param {Boolean} applyFormatter
   */
  getDoc(applyFormatter = true) {
    if (applyFormatter) {
      this.applyFormatters();
      this._doc = null;
    }

    if (this._doc) {
      return this._doc;
    }

    this._doc = this._state.toJSON().doc;
    if (this._doc && this._editor) {
      this._doc = this._editor._execSerializedVisitors(this._doc, 'save');
    }
    return this._doc;
  }

  applyFormatters() {
    if (!this._editor || !this._state) {
      return;
    }

    let formatters = this._editor.extensionFormatters;
    formatters.forEach(
      /**
       *
       * @param {Function} formatter
       */
      formatter => {
        this._state = formatter(this._state);
      }
    );
  }

  /**
   * Does this WekaValue have HTML content?
   *
   * (used to initialise Weka from HTML source)
   */
  hasHtml() {
    return !!this._html;
  }

  /**
   * Get the HTML content of this WekaValue.
   *
   * (used to initialise Weka from HTML source)
   */
  getHtml() {
    return this._html;
  }

  /**
   * Check if document has any content.
   */
  get isEmpty() {
    if (this._state) {
      const { doc } = this._state;
      return (
        doc.childCount === 0 ||
        (doc.childCount === 1 &&
          doc.firstChild.isTextblock &&
          doc.firstChild.content.size === 0)
      );
    } else if (this._doc) {
      if (childCount(this._doc) === 0) {
        return true;
      }
      if (childCount(this._doc) === 1) {
        const child = this._doc.content[0];
        return child.type == 'paragraph' && childCount(child) === 0;
      }
      return false;
    } else {
      return true;
    }
  }

  get fileStorageItemId() {
    return this._editor ? this._editor.getFileStorageItemId() : null;
  }
}

function childCount(node) {
  if (!node) return 0;
  if (!node.content) return 0;
  return node.content.length;
}

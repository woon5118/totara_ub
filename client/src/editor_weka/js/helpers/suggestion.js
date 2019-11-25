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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @module editor_weka
 */

import { ResolvedPos } from 'ext_prosemirror/model';
import Vue from 'vue';

export default class Suggestion {
  /**
   * Create a new suggestion instance.
   *
   * @param {Editor} editor
   */
  constructor(editor) {
    this._instance = null;
    this._editor = editor;
  }

  /**
   *
   * @param {RegExp} $regex
   * @param {ResolvedPos} $position
   * @return {{range: {from: *, to: *}, text: string}|null}
   */
  matcher($regex, $position) {
    if (!$position || !($position instanceof ResolvedPos)) {
      return null;
    }

    const text = $position.doc.textBetween(
      $position.start(),
      $position.end(),
      '\0',
      '\0'
    );

    // Empty string, no point to return the matcher.
    if (text === '') {
      return null;
    }

    let match;
    $regex.lastIndex = 0;
    while ((match = $regex.exec(text))) {
      // Javascript doesn't have lookbehinds; this hacks a check that first character is " " or the line beginning.
      let beforeCharacters = match.input.slice(
        Math.max(0, match.index - 1),
        match.index
      );

      // If the before characters are not an empty string, but any other characters, then we skip this functionality.
      if (!/^[\s\0]?$/.test(beforeCharacters)) {
        continue;
      }

      // The absolute position of the match in the document
      let from = match.index + $position.start(),
        to = from + match[0].length;

      // If the position is located within matched substring, return that range.
      if (from < $position.pos && to >= $position.pos) {
        return {
          range: {
            from,
            to,
          },
          text: match[0],
        };
      }
    }

    return null;
  }

  /**
   * Destroy the instance.
   */
  destroyInstance() {
    if (this._instance !== null) {
      this._instance.$destroy();
      this._editor.viewExtrasLiveEl.removeChild(this._instance.$el);
      this._instance = null;
    }
  }

  /**
   * Resets the component object.
   * @returns {{apply: boolean, from: number, text: string, to: number}}
   */
  resetComponent() {
    return { apply: false, text: '', from: 0, to: 0 };
  }

  /**
   * Show popup with the list of matching suggestions.
   *
   * @param {EditorView} view
   * @param {Object} component
   * @param {Object} range
   * @param {String} text
   * @param {Function} callback
   */
  showList({ view, component, state: { range, text }, callback }) {
    const element = document.createElement('span');

    this._editor.viewExtrasLiveEl.appendChild(element);
    const position = view.coordsAtPos(range.from);
    const parentCoords = this._editor.viewExtrasLiveEl.offsetParent.getBoundingClientRect();

    this._instance = new (Vue.extend(component.component))({
      parent: this._editor.getParent(),
      propsData: {
        x: position.left - parentCoords.left,
        y: position.bottom - parentCoords.top,
        pattern: text,
      },
    });

    this._instance.$mount(element);
    this._instance.$on('item-selected', ({ id, text }) => {
      this.destroyInstance();

      // Add the component node into the editor.
      const node = this.getNode(
        view.state,
        component.name,
        component.attrs(id, text)
      );
      this.convertToNode(range, node);

      // If callback function provided.
      if (typeof callback === 'function') {
        callback();
      }
    });
    this._instance.$on('dismiss', () => {
      this.destroyInstance();
      this._editor.view.focus();
    });
  }

  /**
   * Gets the node to replace text.
   * @param {EditorState} state
   * @param {String} name
   * @param {Object} props
   * @returns {Node}
   */
  getNode(state, name, props) {
    return state.schema.node(name, props);
  }

  /**
   * Convert text to component node.
   * @param {Object} range
   * @param {Object} node
   */
  convertToNode(range, node) {
    this._editor.execute((state, dispatch) => {
      let tr = this.createTransaction(state, range, node);

      // Dispatch the transaction with the above changes.
      dispatch(tr);

      // Bring back the focus to the editor.
      this._editor.view.focus();
    });
  }

  /**
   * Create transaction to replace text with node.
   *
   * @param {EditorState} state
   * @param {Object} range
   * @param {Node} node
   * @returns {Transaction}
   */
  createTransaction(state, range, node) {
    let tr = state.tr;

    // Replace the typed text with the selected node component.
    tr = tr.replaceWith(range.from, range.to, node);

    return tr;
  }

  /**
   *
   * @param {Transaction} transaction
   * @param {Object} oldState
   * @param {RegExp} regex
   * @param {Object} cache
   * @return {Object}
   */
  apply(transaction, oldState, regex, cache) {
    const { selection } = transaction;

    let next = Object.assign({}, oldState);

    if (selection.from === selection.to) {
      const match = this.matcher(regex, selection.$from);

      if (match) {
        next.active = true;
        next.range = match.range;
        next.text = match.text;

        if (cache) {
          cache.text = match.text;
          cache.from = match.range.from;
          cache.to = match.range.to;
        }

        return next;
      }
    }

    next.active = false;
    next.range = {};
    next.text = null;

    return next;
  }
}

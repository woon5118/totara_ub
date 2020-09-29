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
import { getClosestScrollable } from 'tui/dom/scroll';
import { position } from 'tui/lib/popover';
import { Rect, Size } from 'tui/geometry';
import { getBoundingClientRect } from 'tui/dom/position';
import ResizeObserver from 'tui/polyfills/ResizeObserver';

export default class Suggestion {
  /**
   * Create a new suggestion instance.
   *
   * @param {Editor} editor
   */
  constructor(editor) {
    this._instance = null;
    this._editor = editor;
    this._updatePosition = this._updatePosition.bind(this);
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
    if (this._scrollableContainers) {
      this._scrollableContainers.forEach(x =>
        x.removeEventListener('scroll', this._updatePosition)
      );
      this._scrollableContainers = null;
    }
    if (this._resizeObserver) {
      this._resizeObserver.disconnect();
      this._resizeObserver = null;
    }
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
    if (this._instance !== null) {
      this.destroyInstance();
    }

    const element = document.createElement('span');
    this._editor.viewExtrasLiveEl.appendChild(element);

    this._view = view;
    this._range = range;

    this._location = Vue.observable(this._getLocation());

    this._instance = new (Vue.extend(component.component))({
      parent: this._editor.getParent(),
      propsData: {
        location: this._location,
        pattern: text,
      },
    });

    this._instance.$mount(element);

    this._scrollableContainers = [];
    let scrollable = getClosestScrollable(this._editor.viewExtrasLiveEl);
    while (scrollable) {
      this._scrollableContainers.push(scrollable);
      scrollable.addEventListener('scroll', this._updatePosition);
      scrollable = getClosestScrollable(scrollable.parentNode);
    }
    this._scrollableContainers.push(document);
    document.addEventListener('scroll', this._updatePosition);

    this._resizeObserver = new ResizeObserver(this._updatePosition);
    this._resizeObserver.observe(this._instance.$el);

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
    Vue.nextTick(this._updatePosition);
  }

  /**
   * Update position of suggestion menu
   */
  _updatePosition() {
    Object.assign(this._location, this._getLocation());
  }

  /**
   * Work out location to place suggestion menu
   *
   * @returns {{x: number, y: number}}
   */
  _getLocation() {
    const html = document.documentElement;
    const parentCoords = getBoundingClientRect(
      this._editor.viewExtrasLiveEl.offsetParent
    );
    const refCoords = this._view.coordsAtPos(this._range.from);
    const refRect = Rect.fromPositions(
      Object.assign({}, refCoords, { right: refCoords.right + 1 })
    ).sub(parentCoords.getPosition());
    const viewport = new Rect(0, 0, html.clientWidth, html.clientHeight).sub(
      parentCoords.getPosition()
    );

    const pos = position({
      position: ['bottom', 'left'],
      ref: refRect,
      viewport,
      size: this._instance
        ? new Size(
            this._instance.$el.offsetWidth,
            this._instance.$el.offsetHeight
          )
        : new Size(50, 50),
      padding: 0,
    });

    return {
      x: pos.location.x,
      y: pos.location.y,
    };
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

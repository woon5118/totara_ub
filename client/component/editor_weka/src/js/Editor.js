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

import { EditorState } from 'ext_prosemirror/state';
import { EditorView } from 'ext_prosemirror/view';
import { keymap } from 'ext_prosemirror/keymap';
import { history } from 'ext_prosemirror/history';
import { baseKeymap } from 'ext_prosemirror/commands';
import { dropCursor } from 'ext_prosemirror/dropcursor';
import { gapCursor } from 'ext_prosemirror/gapcursor';
import { inputRules } from 'ext_prosemirror/inputrules';
import { buildKeymap } from './keymap';
import { createSchema } from './schema';
import ComponentView from './ComponentView';
import './transaction';
import textPlaceholder from './plugins/text_placeholder';
import WekaValue from './WekaValue';
import { uniqueId } from 'tui/util';
import { notify } from 'tui/notifications';
import { langString, loadLangStrings } from 'tui/i18n';

export default class Editor {
  /**
   * Create a new Editor instance.
   *
   * @param {object} [options]
   * @param {WekaValue} [options.value] WekaValue.
   * @param {*} [options.parent] Parent Vue component.
   */
  constructor(options) {
    this.uid = uniqueId();
    this._options = options;
    this._parent = options.parent;
    this.viewExtrasEl = options.viewExtrasEl;
    this.viewExtrasLiveEl = options.viewExtrasLiveEl;

    /** @type {EditorView} */
    this.view = null;
    /** @type {EditorState} */
    this.state = null;

    this._extensions = options.extensions || [];

    this.fileStorage = options.fileStorage;
    this._extensions.forEach(ext => ext.setEditor(this));

    const nodes = {};
    this._nodeViews = {};
    const marks = {};
    this._markViews = {};
    this._allVueComponents = [];
    this._extensions.forEach(ext => {
      this._extractExtensionSchema(
        ext,
        'node',
        'nodes',
        nodes,
        this._nodeViews
      );
      this._extractExtensionSchema(
        ext,
        'mark',
        'marks',
        marks,
        this._nodeViews
      );
    });

    this.schema = createSchema({ nodes, marks });

    // Default plugins to have textPlaceholder.
    this._plugins = [textPlaceholder(options.placeholder)];

    this._extensions.forEach(ext => {
      const extPlugins = ext.plugins();
      if (extPlugins) {
        extPlugins.forEach(plugin => this._plugins.push(plugin));
      }
    });

    this._toolbarItemInstances = this._extensions.reduce(
      (acc, ext) => (ext.toolbarItems ? acc.concat(ext.toolbarItems()) : acc),
      []
    );

    this._toolbarItems = this._toolbarItemInstances.map(x => x.getDef());

    this._mapKeys = this._extensions
      .map(x => x.keymap && x.keymap.bind(x))
      .filter(Boolean);

    this._inputRules = this._extensions.reduce(
      (acc, ext) => (ext.inputRules ? acc.concat(ext.inputRules()) : acc),
      []
    );

    this.dispatch = this.dispatch.bind(this);
    this.execute = this.execute.bind(this);
    this.getFileStorageItemId = this.getFileStorageItemId.bind(this);

    this.setValue(options.value);
  }

  /**
   *
   * @param {Number} value
   */
  updateFileItemId(value) {
    this.fileStorage.updateFileItemId(value);
  }

  _extractExtensionSchema(plugin, name, methodName, schema, views) {
    const pluginnodes = plugin[methodName] && plugin[methodName]();
    if (pluginnodes) {
      for (const key in pluginnodes) {
        const node = pluginnodes[key];
        if (schema[key]) {
          console.warn(`[editor_weka] ${name} "${key}" was redefined`);
        }
        schema[key] = node.schema;
        if (node.component) {
          this._allVueComponents.push(node.component);
          views[key] = this._componentNodeView.bind(
            this,
            node.component,
            node.componentContext
          );
        }
        if (node.view) {
          views[key] = node.view;
        }
      }
    }
  }

  /**
   * Set the editor state to the specified state.
   */
  setValue(value) {
    if (!value.inflated(this)) {
      const state = EditorState.fromJSON(this._editorConfig(), {
        doc: value.getDoc(),
        selection: { anchor: 0, head: 0, type: 'text' },
      });
      value.inflate(this, state);
    }

    this._value = value;
    this.state = value.getState(this);

    if (this.view) {
      this.view.updateState(this.state);
    }
  }

  /**
   * Get editor config object to pass to EditorState.
   *
   * @private
   * @return object
   */
  _editorConfig() {
    return {
      schema: this.schema,
      plugins: this._createPlugins(),
    };
  }

  /**
   * Create editor view for the specified node.
   *
   * @param {Node} el DOM node.
   * @return {EditorView}
   */
  createView(el) {
    this.view = new EditorView(el, {
      state: this.state,
      dispatchTransaction: this.dispatch,
      nodeViews: this._nodeViews,
      attributes: {
        class: 'tui-weka-editor',
      },
    });

    this.view.dom.addEventListener('focus', e => {
      if (this._options.onFocus) {
        this._options.onFocus(e);
      }
    });
    this.view.dom.addEventListener('blur', e => {
      if (this._options.onBlur) {
        this._options.onBlur(e);
      }
    });

    return this.view;
  }

  allVueComponents() {
    return this._allVueComponents;
  }

  allStrings() {
    return this._toolbarItems.filter(x => x.label).map(x => x.label);
  }

  getParent() {
    return this._parent;
  }

  _componentNodeView(component, componentContext, node, view, getPos) {
    return new ComponentView(component, {
      editor: this,
      node: node,
      view,
      getPos,
      parent: this._parent,
      context: componentContext,
    });
  }

  /**
   * Dispatch a transaction.
   *
   * @param {Transaction} transaction
   */
  dispatch(transaction) {
    const newState = this.state.apply(transaction);
    this.view.updateState(newState);
    this.state = newState;
    this._value = WekaValue.fromState(newState, this);

    if (this._options.onTransaction) {
      this._options.onTransaction({
        value: this._value,
        transaction,
      });
    }

    if (!transaction.docChanged || transaction.getMeta('preventUpdate')) {
      return;
    }

    if (this._options.onUpdate) {
      this._options.onUpdate(this._value);
    }
  }

  /**
   * Returning the file storage item's id that are holding all the files.
   *
   * @return {Number | null}
   */
  getFileStorageItemId() {
    return this.fileStorage.getFileStorageItemId();
  }

  /**
   * Create plugins.
   *
   * @returns {Array<Plugin>}
   */
  _createPlugins() {
    let plugins = [];
    if (Array.isArray(this._plugins)) {
      plugins = this._plugins;
    }

    // Note: we use the plugins defined by the extension first, then following up with the base plugins keymap defined by the editor.
    // With this structure, we care assure that the handleKey event can be triggered first, then the base handling.
    return plugins.concat(
      inputRules({ rules: this._inputRules }),
      keymap(buildKeymap(this.schema, this._mapKeys)),
      keymap(baseKeymap),
      dropCursor(),
      gapCursor(),
      history()
    );
  }

  getToolbarItems() {
    this._toolbarItemInstances.forEach(b => b.update(this));
    return this._toolbarItems;
  }

  /**
   * Execute a command.
   *
   * @param {function} command
   */
  execute(command) {
    try {
      return command(this.state, this.dispatch, this.view);
    } catch (e) {
      console.error('[Weka] Failed to execute command.');
      console.error(e);
      const str = langString('error_failed_to_execute', 'editor_weka');
      loadLangStrings([str]).then(() =>
        notify({ type: 'error', message: str.toString() })
      );
      return false;
    }
  }

  /**
   * Check if a command can be executed.
   *
   * @param {function} command
   */
  canExecute(command) {
    return command(this.state, null, null);
  }

  /**
   *
   * @param {String} name
   * @return {Boolean}
   */
  hasNode(name) {
    const schema = this.state.schema;
    return name in schema.nodes;
  }

  /**
   * Destroy the editor and release all resources.
   */
  destroy() {
    if (this.view) {
      this.view.destroy();
    }
  }
}

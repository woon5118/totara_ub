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

import Vue from 'vue';
import { getMarkRange } from './util';

/**
 * ProseMirror NodeView impementation for Vue components.
 */
export default class ComponentView {
  constructor(component, { editor, node, view, getPos, parent, context }) {
    this.component = component;
    this.editor = editor;
    this.node = node;
    this.view = view;
    this.parent = parent;
    this.context = context;
    this.isNode = !!this.node.marks;
    this.isMark = !this.isNode;
    this.getPos = this.isNode ? getPos : null;
    this.createVue();
    this.dom = this.vm.$el;
    this.contentDOM = this.vm.$refs.content;
  }

  /**
   * Create the Vue component instance.
   */
  createVue() {
    const Component = Vue.extend(this.component);

    const props = {
      nodeInfo: this.getNodeInfo(),
      contextInfo: this.getContextInfo(),
      updateAttrs: attrs => this.updateAttrs(attrs),
      replaceWith: fn => this.replaceWith(fn),
      getPos: this.getPos,
      getRange: this.getRange.bind(this),
    };

    this.vm = new Component({
      parent: this.parent,
      propsData: props,
    }).$mount();

    this.vm.$on('remove', this.remove.bind(this));
  }

  /**
   * Get the `nodeInfo` object to pass to the component.
   *
   * @returns {object}
   */
  getNodeInfo() {
    const nodeInfo = {
      node: this.node,
      editor: this.editor,
    };

    // prevent vue from making it and its child objects observable
    // the reason for this is we want to prevent the prosemirror document from
    // becoming reactive which could slow performance
    Object.preventExtensions(nodeInfo);

    return nodeInfo;
  }

  /**
   * Get the `contextInfo` object to pass to the component.
   *
   * @returns {object}
   */
  getContextInfo() {
    const contextInfo = {
      context: this.context,
    };
    Object.preventExtensions(contextInfo);
    return contextInfo;
  }

  /**
   * Called when node is updated.
   *
   * @see {@link https://prosemirror.net/docs/ref/#view.NodeView.update}
   * @param {Node} node
   * @param {Array<Decoration>} decorations
   */
  update(node, decorations) {
    if (node.type !== this.node.type) {
      return false;
    }

    if (node === this.node && this.decorations === decorations) {
      return true;
    }

    this.node = node;

    this.updateComponentProps({
      nodeInfo: this.getNodeInfo(),
    });

    return true;
  }

  /**
   * Pass updated props to the component.
   *
   * @param {object} props
   */
  updateComponentProps(props) {
    if (!this.vm._props) {
      return;
    }
    // hack to update props - disable the warning when you modify them directly
    const oldSilent = Vue.config.silent;
    Vue.config.silent = true;
    Object.entries(props).forEach(([key, value]) => {
      this.vm._props[key] = value;
    });
    Vue.config.silent = oldSilent;
  }

  /**
   * Update the node's attrs.
   *
   * @param {object} attrs
   */
  updateAttrs(attrs) {
    if (!this.view.editable) {
      return;
    }

    const { state } = this.view;
    const { type } = this.node;
    const pos = this.getPos();
    const newAttrs = Object.assign({}, this.node.attrs, attrs);
    const transaction = this.isMark
      ? state.tr
          .removeMark(pos.from, pos.to, type)
          .addMark(pos.from, pos.to, type.create(newAttrs))
      : state.tr.setNodeMarkup(pos, null, newAttrs);

    this.view.dispatch(transaction);
  }

  /**
   * Replace the current node with a different node.
   *
   * @param {function} fn Called with schema, should return replacement node.
   */
  replaceWith(fn) {
    const schema = this.view.state.schema;
    const content = fn(schema);
    if (content === undefined) {
      return;
    }
    const range = this.getRange();
    const transaction = this.view.state.tr.replaceWith(
      range.from,
      range.to,
      content
    );
    this.view.dispatch(transaction);
  }

  /**
   * Remove the current node.
   */
  remove() {
    const tr = this.view.state.tr;
    const range = this.getRange();
    const transaction = tr.delete(range.from, range.to);
    this.view.dispatch(transaction);
  }

  /**
   * Get the size of the node.
   *
   * @returns {number}
   */
  getSize() {
    return this.node.nodeSize;
  }

  /**
   * Get the pos range of the node.
   *
   * @returns {{ from: number, to: number }}
   */
  getRange() {
    if (this.isNode) {
      const pos = this.getPos();
      return { from: pos, to: pos + this.getSize() };
    }
    if (this.isMark) {
      const pos = this.view.posAtDOM(this.dom);
      const $pos = this.view.state.doc.resolve(pos);
      return getMarkRange($pos, this.node.type);
    }
  }

  /**
   * @see {@link https://prosemirror.net/docs/ref/#view.NodeView.destroy}
   */
  destroy() {
    this.vm.$destroy();
  }

  /**
   * @see {@link https://prosemirror.net/docs/ref/#view.NodeView.ignoreMutation}
   */
  ignoreMutation(mutation) {
    // default behavior: ignore all non-selection mutations unless there is content
    if (!this.contentDOM && mutation.type !== 'selection') {
      return true;
    }

    // added behavior: if there is content, ignore all non-selection mutations that don't target the content
    // otherwise we are unable to modify the dom without the entire node rerendering
    if (
      this.contentDOM &&
      mutation.type !== 'selection' &&
      !this.contentDOM.contains(mutation.target)
    ) {
      return true;
    }

    return false;
  }
}

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
 * @module editor_weka
 */

import BaseExtension from './Base';
import { ToolbarItem } from '../toolbar';
import { langString, loadLangStrings } from 'tui/i18n';
import { pickFiles, UploadError } from '../utils/upload';
import ImageBlock from 'editor_weka/components/nodes/ImageBlock';
import VideoBlock from 'editor_weka/components/nodes/VideoBlock';
import AudioBlock from 'editor_weka/components/nodes/AudioBlock';
import { getMediaType } from '../api';
import { IMAGE, VIDEO } from '../helpers/media';
import { getJsonAttrs } from './util';
import { notify } from 'tui/notifications';

class MediaExtension extends BaseExtension {
  /**
   *
   * @param {Object} opt
   */
  constructor(opt) {
    super(opt);
    this.acceptTypes = opt.accept_types || [];
  }

  nodes() {
    return {
      image: {
        schema: {
          atom: true,
          group: 'block',
          inline: false,
          selectable: true,
          attrs: {
            filename: { default: undefined },
            // This is from the support of file_rewrite_plugin_file, it will be picked up by the
            // server side to reformat the plugin file url.
            url: { default: undefined },
            alttext: { default: undefined },
          },

          parseDOM: [
            {
              tag: 'div.tui-editorWeka-imageBlock',
              getAttrs: getJsonAttrs,
            },
            {
              tag: 'div.tui-imageBlock',
              getAttrs: getJsonAttrs,
            },
          ],

          toDOM(node) {
            return [
              'div',
              {
                class: 'tui-editorWeka-imageBlock',
                'data-attrs': JSON.stringify({
                  filename: node.attrs.filename,
                  alttext: node.attrs.alttext,
                  url: node.attrs.url,
                }),
              },
            ];
          },
        },
        component: ImageBlock,
        componentContext: {
          replaceWithAttachment: this._replaceImageWithAttachment.bind(this),
          updateImage: this._updateImage.bind(this),
          hasAttachmentNode: this._hasAttachmentsNode.bind(this),
          removeNode: this.removeNode.bind(this),
          getFileUrl: this._getFileUrl.bind(this),
          getItemId: this._getItemId.bind(this),
        },
      },

      video: {
        schema: {
          atom: true,
          group: 'block',
          inline: false,
          attrs: {
            filename: { default: undefined },
            // This is from the support of file_rewrite_plugin_file, it will be picked up by the
            // server side to reformat the plugin file url.
            url: { default: undefined },
            mime_type: { default: undefined },
          },

          parseDOM: [
            {
              tag: 'div.tui-editorWeka-videoBlock',
              getAttrs: getJsonAttrs,
            },
            {
              tag: 'div.tui-videoBlock',
              getAttrs: getJsonAttrs,
            },
          ],

          toDOM(node) {
            return [
              'div',
              {
                class: 'tui-editorWeka-videoBlock',
                'data-attrs': JSON.stringify({
                  filename: node.attrs.filename,
                  url: node.attrs.url,
                  mime_type: node.attrs.mime_type,
                }),
              },
            ];
          },
        },
        component: VideoBlock,
        componentContext: {
          replaceWithAttachment: this._replaceVideoWithAttachment.bind(this),
          hasAttachmentNode: this._hasAttachmentNode.bind(this),
          removeNode: this.removeNode.bind(this),
          getFileUrl: this._getFileUrl.bind(this),
          getItemId: this._getItemId.bind(this),
        },
      },

      audio: {
        schema: {
          atom: true,
          group: 'block',
          inline: false,
          attrs: {
            filename: { default: undefined },
            // This is from the support of file_rewrite_plugin_file, it will be picked up by the
            // server side to reformat the plugin file url.
            url: { default: undefined },

            // This is needed to display embedded audio file.
            mime_type: { default: undefined },
          },

          parseDOM: [
            {
              tag: 'div.tui-editorWeka-audioBlock',
              getAttrs: getJsonAttrs,
            },
            {
              tag: 'div.tui-audioBlock',
              getAttrs: getJsonAttrs,
            },
          ],

          toDOM(node) {
            return [
              'div',
              {
                class: 'tui-editorWeka-audioBlock',
                'data-attrs': JSON.stringify({
                  filename: node.attrs.filename,
                  url: node.attrs.url,
                  mime_type: node.attrs.mime_type,
                }),
              },
            ];
          },
        },

        component: AudioBlock,
        componentContext: {
          hasAttachmentNode: this._hasAttachmentNode.bind(this),
          replaceWithAttachment: this._replaceAudioWithAttachment.bind(this),
          removeNode: this.removeNode.bind(this),
          getFileUrl: this._getFileUrl.bind(this),
          getItemId: this._getItemId.bind(this),
        },
      },
    };
  }

  toolbarItems() {
    if (!this.editor.fileStorage.enabled) return [];
    return [
      new ToolbarItem({
        group: 'upload',
        label: langString('embedded_media', 'editor_weka'),
        icon: 'editor_weka|image',
        execute: editor => {
          pickFiles(editor).then(
            /**
             *
             * @param {FileList|Array} files
             * @return {Promise<void>}
             */
            async files => {
              if (files) {
                this._startUploading(files);
              }
            }
          );
        },
      }),
    ];
  }

  /**
   *
   * @param {FileList|Array} files
   * @return {Promise<void>}
   * @private
   */
  async _startUploading(files) {
    try {
      const submitFiles = await this.editor.fileStorage.uploadFiles(
        files,
        this.acceptTypes
      );

      if (submitFiles.length === 0) {
        return;
      }

      const schema = this.editor.state.schema;
      const images = await Promise.all(
        submitFiles.map(({ file, id, url }) => {
          return getMediaType({
            filename: file,
            itemId: id,
          }).then(
            /**
             *
             * @param {String} mediaType
             * @param {String} mimeType
             * @return {*}
             */
            ({ mediaType, mimeType }) => {
              if (IMAGE === mediaType) {
                return schema.node('image', {
                  filename: file,
                  alttext: '',
                  url: url,
                });
              } else if (VIDEO === mediaType) {
                return schema.node('video', {
                  filename: file,
                  url: url,
                  mime_type: mimeType,
                });
              } else {
                return schema.node('audio', {
                  url: url,
                  filename: file,
                  mime_type: mimeType,
                });
              }
            }
          );
        })
      );

      this.editor.execute((state, dispatch) => {
        const transaction = state.tr,
          { selection } = transaction;

        // First we need to check whether the current selection is in paragraph or not.
        if (this._hasAttachmentsNode()) {
          // Check if the attachments node is appearing in the schema.
          // If there is we will search for whether the current cursor is in it or not.
          const { attachments } = schema.nodes;

          if (
            selection.$from.parent &&
            attachments.name === selection.$from.parent.type.name
          ) {
            // Parent is in attachments block. Need to move to the end of this node.
            transaction.insert(transaction.selection.$from.after(), images);

            dispatch(transaction);
            return;
          }
        }

        if (selection.empty) {
          let from = selection.$from,
            node = from.parent;

          if (node.type.name === 'paragraph' && node.content.size === 0) {
            // Check that if it is an empty paragraph, then we will remove it.
            transaction.replaceWith(from.before(), from.after(), images);
            this.editor.view.focus();
          } else {
            transaction.insert(from.pos, images);
          }
        } else {
          transaction.replaceWith(selection.from, selection.to, images);
        }

        dispatch(transaction);
      });
    } catch (e) {
      console.error(e);
      if (e instanceof UploadError) {
        notify({ type: 'error', message: e.message });
      } else {
        const str = langString('error_upload_failed', 'editor_weka');
        loadLangStrings([str]).then(() =>
          notify({ type: 'error', message: str.toString() })
        );
      }
    }
  }

  /**
   *
   * @param {Function}  getRange
   * @param {String}    filename
   * @param {String}    alttext
   *
   * @private
   */
  _replaceImageWithAttachment(getRange, { filename, alttext, size }) {
    this.editor.execute((state, dispatch) => {
      const transaction = state.tr,
        range = getRange();

      let attachment = state.schema.node('attachment', {
        filename: filename,
        url: this._getFileUrl(filename),
        size: size,
        option: {
          alttext: alttext,
        },
      });

      dispatch(
        transaction.replaceWith(
          range.from,
          range.to,
          state.schema.node('attachments', null, [attachment])
        )
      );
    });
  }

  /**
   *
   * @param {Function} getRange
   * @param {String}  filename
   * @param {Number}  size
   * @private
   */
  _replaceVideoWithAttachment(getRange, { filename, size }) {
    this.editor.execute((state, dispatch) => {
      const transaction = state.tr,
        range = getRange();

      let attachment = state.schema.node('attachment', {
        filename: filename,
        url: this._getFileUrl(filename),
        size: size,
        option: {},
      });

      dispatch(
        transaction.replaceWith(
          range.from,
          range.to,
          state.schema.node('attachments', null, [attachment])
        )
      );
    });
  }

  /**
   * @param {Function} getRange
   * @param {String} altText
   * @param {String} filename
   * @param {String} mimeType
   * @private
   */
  _updateImage(getRange, { filename, alttext, mimeType }) {
    this.editor.execute((state, dispatch) => {
      const transaction = state.tr,
        range = getRange();

      let node = state.schema.node('image', {
        filename: filename,
        url: this._getFileUrl(filename),
        alttext: alttext,
        mime_type: mimeType,
      });

      dispatch(transaction.replaceWith(range.from, range.to, node));
      this.editor.view.focus();
    });
  }

  /**
   *
   * @param {Function}  getRange
   * @param {String}    filename
   * @param {Number}    size
   * @private
   */
  _replaceAudioWithAttachment(getRange, { filename, size }) {
    this.editor.execute((state, dispatch) => {
      const transaction = state.tr,
        range = getRange();

      let attachment = state.schema.node('attachment', {
        filename: filename,
        url: this._getFileUrl(filename),
        size: size,
        option: {},
      });

      dispatch(
        transaction.replaceWith(
          range.from,
          range.to,
          state.schema.node('attachments', null, [attachment])
        )
      );
    });
  }

  _hasAttachmentNode() {
    return this.editor.hasNode('attachment');
  }

  _hasAttachmentsNode() {
    return this.editor.hasNode('attachments');
  }

  /**
   * Given the filename, then this API can return the url for the filename in the file storage.
   *
   * @param {String} filename
   * @return {String|Null}
   *
   * @private
   */
  _getFileUrl(filename) {
    let file = this.editor.fileStorage.getFile(filename);
    if (!file) {
      return null;
    }

    return file.url;
  }

  /**
   * Returning the current item's id where the files are uploaded to.
   *
   * @return {Number | null}
   * @private
   */
  _getItemId() {
    return this.editor.fileStorage.getFileStorageItemId();
  }
}

export default opt => new MediaExtension(opt);

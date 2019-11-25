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

import { prepareDraftFileArea } from '../api';
import { upload, parseFiles } from '../utils/upload';
import { langString, loadLangStrings } from 'tui/i18n';

const has = Object.prototype.hasOwnProperty;

/**
 * File manager for weka editor. This file storage should only support multiple extensions per editor only.
 * Multiple editors should have multiple instances of this kind.
 */
export default class FileStorage {
  /**
   * The file storage will have to know the component, area and instanceId where it
   * is serving the files.
   *
   * @param {Number|null}         contextId
   * @param {Number|String|null}  itemId
   * @constructor
   */
  constructor({ contextId, itemId }) {
    this.repositoryData = null;
    this.files = {};

    this.itemId = null;
    if (itemId) {
      this.itemId = itemId;
    }

    this.contextId = contextId;
  }

  /**
   * @returns {boolean}
   */
  get enabled() {
    return !!this.itemId;
  }

  /**
   *
   * @param {Number} value
   */
  updateFileItemId(value) {
    this.itemId = value;
  }

  /**
   *
   * @return {Number|null}
   */
  getContextId() {
    return this.contextId;
  }

  /**
   *
   * @param {Number}  repository_id
   * @param {String}  url
   */
  setRepositoryData({ repository_id, url }) {
    if (this.repositoryData) {
      return;
    }

    this.repositoryData = {
      repositoryId: repository_id,
      url: url,
    };
  }

  /**
   *
   * @return {Promise<void>}
   * @private
   */
  async _fetchRepositoryData() {
    return new Promise(resolve => {
      if (this.repositoryData) {
        resolve('done');
        return;
      }

      prepareDraftFileArea(this.contextId).then(
        /**
         *
         * @param {Number} repository_id
         * @param {String} url
         */
        ({ repository_id, url }) => {
          this.setRepositoryData({ repository_id, url });
          resolve('done');
        }
      );
    });
  }

  /**
   *
   * @return {?{
   *   repositoryId: Number,
   *   url: String
   * }}
   */
  getRepositoryData() {
    return this.repositoryData;
  }

  /**
   * @return {Number}
   */
  getFileStorageItemId() {
    return this.itemId;
  }

  /**
   *
   * @param {String}        file
   * @param {Number|null}   size
   * @param {String|null}   url
   */
  addFile({ file, size, url }) {
    if (has.call(this.files, file)) {
      return;
    }

    this.files[file] = { file, size, url };
  }

  /**
   * Finding the file object within the file storage base on the filezname. Since it is
   * already had been stored with the filename as a key.
   * @param {String} filename
   * @return {{
   *   file: String,
   *   size: Number,
   *   url: String
   * }|null}
   */
  getFile(filename) {
    if (!has.call(this.files, filename)) {
      return null;
    }

    return this.files[filename];
  }

  /**
   *
   * @param {File} rawFile
   * @param {Array} acceptTypes
   */
  async uploadFile(rawFile, acceptTypes) {
    await this._fetchRepositoryData();
    const { repositoryId, url } = this.repositoryData;

    const result = await upload({
      file: rawFile,
      itemId: this.itemId,
      contextId: this.contextId,
      acceptTypes,
      repositoryId,
      url,
    });

    if (result.id && result.file) {
      let newFile = Object.assign({}, result);
      newFile.size = rawFile.size;

      this.addFile(newFile);
      return newFile;
    } else if (result.event === 'fileexists') {
      const {
        existingfile: { filename: existingFileName },
        newfile: { filename, url },
      } = result;

      if (has.call(this.files, existingFileName)) {
        const { size } = this.files[existingFileName];
        const newFile = {
          id: this.itemId,
          url: url,
          file: filename,
          size: size,
        };

        this.addFile(newFile);
        return newFile;
      }
    }

    let message = langString('invalid_response', 'editor_weka');
    await loadLangStrings([message]);

    throw new Error(message.toString());
  }

  /**
   *
   * @param {FileList|Array}  files
   * @param {Array}           acceptTypes
   * @return {Promise<void>}
   */
  async uploadFiles(files, acceptTypes) {
    files = parseFiles(files);

    return new Promise((resolve, reject) => {
      if (files.length === 0) {
        resolve([]);
        return;
      }

      Promise.all(files.map(rawFile => this.uploadFile(rawFile, acceptTypes)))
        .then(submitFiles => {
          resolve(submitFiles);
        })
        .catch(e => {
          reject(e);
        });
    });
  }
}

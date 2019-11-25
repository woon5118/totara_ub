<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Simon Chester <simon.chester@totaralearning.com>
  @module editor_weka
-->

<template>
  <div :id="uid" class="tui-weka no-yui-ids">
    <Toolbar v-if="showToolbar" :items="toolbarItems" />
    <div
      ref="editorHost"
      class="tui-rendered"
      :data-placeholder="placeholder"
    />

    <div ref="extras">
      <!-- This is where modals/custom components will be added/removed -->
    </div>

    <div ref="extrasLive" aria-live="polite" role="status">
      <!-- This is where live-updated UI will be added/removed -->
    </div>
  </div>
</template>

<script>
import tui from 'tui/tui';
import { throttle } from 'tui/util';
import Editor from '../js/Editor';
import Toolbar from 'editor_weka/components/toolbar/Toolbar';
import { loadLangStrings } from 'tui/i18n';
import editorWeka from 'editor_weka/graphql/weka';
import FileStorage from '../js/helpers/file';
import WekaValue from '../js/WekaValue';
import { createImmutablePropWatcher } from 'tui/vue_util';

const propEqual = (val, old) => JSON.stringify(old) == JSON.stringify(val);
const warnChange = prop => createImmutablePropWatcher('Weka', prop, propEqual);

export default {
  components: {
    Toolbar,
  },

  props: {
    placeholder: {
      type: String,
      default: '',
    },
    instanceId: [Number, String],
    fileItemId: [Number, String],
    component: String,
    area: String,
    doc: Object,
    options: {
      type: Object,
      validator: prop =>
        prop.showtoolbar !== undefined && prop.extensions !== undefined,
    },
    value: WekaValue,
  },

  data() {
    return {
      toolbarItems: [],
      toolbarEnabled: false,
    };
  },

  computed: {
    showToolbar() {
      if (!this.toolbarEnabled) {
        return false;
      }

      return this.toolbarItems.length > 0;
    },
  },

  watch: {
    doc(value) {
      if (!value) {
        if (!this.value) {
          this.displayedValue = WekaValue.empty();
          if (this.editor) {
            this.editor.setValue(this.displayedValue);
          }
        }
        return;
      }

      if (!this.displayedValue || this.displayedValue.getDoc() !== value) {
        this.displayedValue = WekaValue.fromDoc(value);
        if (this.editor) {
          this.editor.setValue(this.displayedValue);
        }
      }
    },

    value(value) {
      if (this.displayedValue === value) {
        return;
      }
      this.displayedValue = value || WekaValue.empty();
      if (this.editor) {
        this.editor.setValue(this.displayedValue);
      }
    },

    component: warnChange('component'),
    area: warnChange('area'),
    instanceId: warnChange('instanceId'),
    options: warnChange('options'),

    /**
     * @param {Number|String} value
     */
    fileItemId(value) {
      this.editor.updateFileItemId(value);
    },
  },

  created() {
    this.updateToolbarThrottled = throttle(this.updateToolbar, 100);
    if (this.value) {
      this.displayedValue = this.value;
    } else if (this.doc) {
      this.displayedValue = WekaValue.fromDoc(this.doc);
    } else {
      this.displayedValue = WekaValue.empty();
    }
    this.finalOptions = null;
  },

  mounted() {
    this.createEditor();
  },

  beforeDestroy() {
    if (this.editor) {
      this.editor.destroy();
    }
  },

  methods: {
    /**
     * Setup the editor options such as showing toolbar, the extensions that the editor needs.
     */
    async setupOptions() {
      if (this.options) {
        this.finalOptions = Object.assign({}, this.options);
        this.toolbarEnabled = this.finalOptions.showtoolbar;
        return;
      }

      // Start populating the options from the graphql call.
      const result = await this.$apollo.query({
        query: editorWeka,
        fetchPolicy: 'no-cache',
        variables: {
          instance_id: this.instanceId,
          component: this.component,
          area: this.area,
          draft_id: this.fileItemId,
        },
      });

      this.finalOptions = Object.assign({}, result.data.editor);
      this.toolbarEnabled = this.finalOptions.showtoolbar;
    },

    /**
     * This function will try to fetch the extensions via HTTP call. This function will not cache the
     * modules that had already been loaded.
     *
     * @return {Promise<[]>}
     */
    async getExtensions() {
      await this.setupOptions();
      let extensions = [];

      if (Array.isArray(this.finalOptions.extensions)) {
        extensions = await Promise.all(
          this.finalOptions.extensions.map(({ tuicomponent, options }) => {
            let opt = {};

            if (options !== undefined && options !== null) {
              opt = JSON.parse(options);
            }

            return tui
              .import(tuicomponent)
              .then(component => component.default(opt));
          })
        );
      }

      return extensions;
    },

    /**
     * @return {Promise<{
     *   item_id: {Number},
     *   repository_id: {Number},
     *   url: {String}
     * }|null>}
     */
    async getRepositoryData() {
      await this.setupOptions();
      return this.finalOptions.repository_data || null;
    },

    /**
     * @return {Promise}
     */
    async getCurrentFiles() {
      await this.setupOptions();
      return this.finalOptions.files || [];
    },

    async createEditor() {
      if (this.editor) {
        return;
      }

      const extensions = await this.getExtensions(),
        repositoryData = await this.getRepositoryData();

      let fileStorage = new FileStorage({
        itemId: this.fileItemId,
        contextId: this.finalOptions.context_id || null,
      });

      if (repositoryData !== null) {
        fileStorage.setRepositoryData(repositoryData);
      }

      let files = await this.getCurrentFiles();
      Array.prototype.forEach.call(files, ({ filename, url, file_size }) => {
        fileStorage.addFile({
          file: filename,
          url: url,
          size: file_size,
        });
      });

      this.editor = new Editor({
        value: this.displayedValue,
        placeholder: this.placeholder,
        parent: this,
        viewExtrasEl: this.$refs.extras,
        viewExtrasLiveEl: this.$refs.extrasLive,
        extensions: extensions,
        fileStorage: fileStorage,
        onUpdate: this.$_onUpdate.bind(this),
        onTransaction: () => {
          this.updateToolbarThrottled();
        },
        onFocus: e => {
          this.$emit('focus', e);
        },
        onBlur: e => {
          this.$emit('blur', e);
        },
      });

      const components = this.editor.allVueComponents();

      await Promise.all([
        tui.loadRequirements({ components }),
        loadLangStrings(this.editor.allStrings()),
      ]);

      this.view = this.editor.createView(this.$refs.editorHost);

      this.updateToolbar();

      // Event emitted to make the parent component knowing that this editor has been mounted properly.
      this.$emit('editor-mounted');
      this.$emit('editor-ready');
    },

    updateToolbar() {
      this.toolbarItems = this.editor.getToolbarItems();
    },

    /**
     * Emit new value.
     *
     * @param {WekaValue} value
     */
    $_onUpdate(value) {
      this.displayedValue = value;
      this.$emit('input', value);
      if (this.$listeners.update) {
        this.$emit('update', {
          getJSON: () => value.getDoc(),
          isEmpty: () => value.empty,
          getFileStorageItemId: () => value.fileStorageItemId,
        });
      }
    },
  },
};
</script>

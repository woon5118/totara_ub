<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Simon Chester <simon.chester@totaralearning.com>
  @module totara_core
-->

<script>
import amd from 'tui/amd';

export default {
  props: {
    icon: {
      type: String,
      required: true,
    },
    title: {
      type: String,
      default: undefined,
    },
    alt: {
      type: String,
      default: undefined,
    },
    size: {
      type: [Number, String],
      default: undefined,
    },
    customClass: {
      type: [String, Object, Array],
      default: undefined,
    },
  },

  data() {
    return {
      loaded: false,
      iconHtml: null,
    };
  },

  mounted() {
    this.$_renderIcon();
    // re-render any time a prop changes
    Object.keys(this.$options.props).forEach(key => {
      this.$watch(key, this.$_renderIcon);
    });
  },

  methods: {
    async $_renderIcon() {
      const templates = await amd('core/templates');
      const classes =
        (this.size ? 'ft-size-' + this.size : '') +
        ' ' +
        (this.customClass || '');
      this.iconHtml = await templates.renderIcon(this.icon, this.alt, classes, {
        title: this.title,
      });
      this.loaded = true;
    },
  },

  render(h) {
    return this.loaded
      ? h('span', { domProps: { innerHTML: this.iconHtml } })
      : null;
  },
};
</script>

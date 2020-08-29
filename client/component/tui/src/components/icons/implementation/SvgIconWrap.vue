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
  @module tui
-->

<script>
export default {
  functional: true,
  props: {
    iconName: {
      type: String,
      default: 'box',
    },
    rootFill: {
      type: String,
      default: 'currentColor',
    },
    htmlContent: String,
    viewBox: String,
    title: String,
    alt: String,
    size: {
      type: [Number, String],
      default: 200,
      validator(prop) {
        if (prop == null) {
          return true;
        }
        const num = Number(prop);
        return [100, 200, 300, 400, 500, 600, 700].includes(num);
      },
    },
    state: String,
    customClass: {
      type: [String, Object, Array],
      default: undefined,
    },
  },

  render(h, { props, data }) {
    return [
      h('svg', {
        class: [
          'tui-svgIcon',
          props.size && 'tui-svgIcon--size-' + props.size,
          props.state && 'tui-svgIcon--state-' + props.state,
          props.flipRtl && 'tui-svgIcon--flipRtl',
          data.staticClass,
          data.class,
          props.customClass,
        ],
        attrs: {
          xmlns: 'http://www.w3.org/2000/svg',
          'xmlns:xlink': 'http://www.w3.org/1999/xlink',
          width: '1em',
          height: '1em',
          viewBox: props.viewBox,
          role: 'presentation',
          focusable: 'false',
          fill: props.rootFill,
        },
        domProps: {
          innerHTML: props.htmlContent,
        },
      }),
      props.alt && h('span', { class: 'sr-only' }, props.alt),
    ];
  },
};
</script>

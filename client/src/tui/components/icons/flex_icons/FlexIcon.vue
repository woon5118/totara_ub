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
  @module totara_core
-->

<script>
export default {
  // only functional components are able to render with multiple root elements
  functional: true,

  props: {
    data: {
      type: Object,
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
      validator(prop) {
        return (
          -1 !==
          ['100', '200', '300', '400', '500', '600', '700'].indexOf(
            String(prop)
          )
        );
      },
    },
    styleClass: {
      type: Object,
      default: () => ({}),
    },
    customClass: {
      type: [String, Object, Array],
      default: undefined,
    },
  },

  render(h, { props }) {
    return [
      h('span', {
        class: [
          'flex-icon ft-fw ft',
          props.size && 'ft-size-' + props.size,
          props.data.classes,
          props.customClass,
          {
            'tui-icon--action': props.styleClass.action,
            'tui-icon--disabled': props.styleClass.disabled,
          },
        ],
        attrs: {
          'aria-hidden': 'true',
          'data-flex-icon': props.data.identifier,
          title: props.title,
        },
      }),
      props.alt && h('span', { class: 'sr-only' }, props.alt),
    ];
  },
};
</script>

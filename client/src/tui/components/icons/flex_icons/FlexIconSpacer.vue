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
  functional: true,

  props: {
    size: {
      type: [Number, String],
      default: undefined,
    },
  },

  render(h, { props }) {
    return h('span', {
      class: [
        'flex-icon ft-fw ft ft-spacer',
        props.size && 'ft-size-' + props.size,
      ],
      attrs: {
        'aria-hidden': 'true',
      },
    });
  },
};
</script>

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

  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @module mod_perform
-->

<script>
import FlexIcon from 'tui/components/icons/FlexIcon';

export default {
  functional: true,

  components: {
    FlexIcon,
  },

  props: {
    alt: String,
    customClass: [String, Object, Array],
    styleClass: Object,
    size: [String, Number],
    title: String,
  },

  render(h, { props }) {
    return h(FlexIcon, {
      props: Object.assign({}, props, {
        icon: 'mod_perform|reporting',
      }),
    });
  },
};
</script>

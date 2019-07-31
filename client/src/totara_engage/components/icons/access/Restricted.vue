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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module totara_engage
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
    customClass: {
      type: [String, Object, Array],
      default() {
        return [];
      },
    },
    styleClass: Object,
    size: [String, Number],
    title: String,
    coloring: {
      type: Boolean,
      default: true,
    },
  },

  render(h, { props }) {
    let customClasses = [];
    if ('string' === typeof props.customClass) {
      // Split by the space.
      customClasses = props.customClass.split(' ');
    } else if (Array.isArray(props.customClass)) {
      customClasses = props.customClass;
    } else {
      Object.keys(props.customClass).map(function(key) {
        customClasses.push(props.customClass[key]);
      });
    }

    // Pushing the default icon.
    customClasses.push('tui-totaraEngage-restricted');

    if (props.coloring) {
      customClasses.push('tui-totaraEngage-restricted--color');
    }

    let items = {
      alt: props.alt,
      customClass: customClasses,
      styleClass: props.styleClass,
      size: props.size,
      title: props.title,
      icon: 'totara_engage|restricted',
    };

    return h(FlexIcon, { props: items });
  },
};
</script>

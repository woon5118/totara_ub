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
 * @module tui
 */

import SvgIconWrap from 'tui/components/icons/implementation/SvgIconWrap';

export function createIconComponent([type, attrs, content], opts = {}) {
  // svgc (content string) is the only supported type
  if (type != 'svgc') throw new Error('Unsupported icon type');
  return {
    name: 'SvgIconGenerated',
    functional: true,
    components: { SvgIconWrap },
    render(h, { props }) {
      return h(SvgIconWrap, {
        class: opts.class,
        props: Object.assign(
          {
            htmlContent: content,
            viewBox: attrs.viewBox,
            rootFill: attrs.fill,
            state: opts.state,
            flipRtl: opts.flipRtl,
          },
          props
        ),
      });
    },
  };
}

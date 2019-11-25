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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @module editor_weka
 */

import BaseExtension from './Base';
import hashtag from '../plugins/hashtag';
import Hashtag from 'editor_weka/components/nodes/Hashtag';

class HashtagExtension extends BaseExtension {
  nodes() {
    return {
      hashtag: {
        schema: {
          group: 'inline',
          inline: true,
          attrs: {
            text: { default: '' },
          },
          parseDOM: [
            {
              tag: 'span.tui-editorWeka-node__hashtag',
              getAttrs(dom) {
                try {
                  return {
                    text: dom.textContent.trim().slice(1),
                  };
                } catch (e) {
                  return {};
                }
              },
            },
          ],
          toDOM(node) {
            return [
              'span',
              {
                class: 'tui-editorWeka-node__hashtag',
              },
              '#' + node.attrs.text,
            ];
          },
        },

        component: Hashtag,
      },
    };
  }

  plugins() {
    return [hashtag(this.editor)];
  }
}

export default opt => new HashtagExtension(opt);

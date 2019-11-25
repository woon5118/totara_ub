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

import { Plugin, PluginKey } from 'ext_prosemirror/state';
import HashtagSuggestion from 'editor_weka/components/suggestion/Hashtag';
import Suggestion from '../helpers/suggestion';

/**
 *
 * @param editor
 *
 * @return {Plugin}
 */
export default function(editor) {
  const key = new PluginKey('hashtags');
  let suggestion = new Suggestion(editor);
  let hashtag = suggestion.resetComponent();
  const regex = new RegExp(`#[A-Za-z0-9]+$`, 'g');

  let processHashtag = view => {
    const node = suggestion.getNode(view.state, 'hashtag', {
      text: hashtag.text.slice(1),
    });
    suggestion.convertToNode(hashtag, node);
    hashtag = suggestion.resetComponent();
  };

  return new Plugin({
    key: key,

    view() {
      return {
        /**
         *
         * @param {EditorView} view
         */
        update: view => {
          const { text, active, range } = this.key.getState(view.state);
          suggestion.destroyInstance();

          if (!text || !active) {
            return;
          }

          suggestion.showList({
            view,
            component: {
              name: 'hashtag',
              component: HashtagSuggestion,
              attrs: (id, name) => {
                return { text: name };
              },
            },
            state: {
              text: text.slice(1),
              active,
              range,
            },
            callback: () => {
              hashtag = suggestion.resetComponent();
            },
          });
        },
      };
    },

    state: {
      init() {
        return {
          active: false,
          range: {},
          text: null,
        };
      },

      /**
       *
       * @param {Transaction} transaction
       * @param {Object} oldState
       *
       * @return {Object}
       */
      apply(transaction, oldState) {
        return suggestion.apply(transaction, oldState, regex, hashtag);
      },
    },

    props: {
      /**
       * Some key strokes causes the node conversion to get ignored so we
       * want to monitor certain key strokes to apply the node transaction.
       *
       * @param {EditorView} view
       * @param {KeyboardEvent} event
       */
      handleKeyDown(view, event) {
        // Leave when user not busy constructing a hashtag.
        if (hashtag.text.length <= 0) {
          return;
        }

        // Moving away from the hashtag needs to reset the component as the user might
        // not want the process to convert the text to hashtag node.
        if (event.key.includes('Arrow')) {
          hashtag = suggestion.resetComponent();
          return;
        }

        hashtag.apply =
          [
            ' ',
            'Tab',
            'Enter',
            'PageUp',
            'PageDown',
            'Home',
            'End',
            'Escape',
          ].find(key => key === event.key) !== undefined;

        if (hashtag.apply) {
          processHashtag(view);
        }
      },

      /**
       *
       * @param {EditorView} view
       */
      handleClick(view) {
        // Leave when user not busy constructing a hashtag.
        if (hashtag.text.length <= 0) {
          return false;
        }

        // Leave when user is still on the hashtag.
        if (
          hashtag.from <= view.state.selection.from &&
          hashtag.to >= view.state.selection.to
        ) {
          return false;
        }

        // Convert the text to a hashtag node.
        processHashtag(view);
        return true;
      },
    },
  });
}

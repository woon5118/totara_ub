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
  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module totara_playlist
-->

<template>
  <div class="tui-playlistSummary">
    <InlineEditing
      v-if="!editing"
      :full-width="true"
      :update-able="updateAble"
      :button-aria-label="$str('adddescription', 'totara_playlist')"
      @click="editing = true"
    >
      <div
        v-if="!content.isEmpty"
        slot="content"
        ref="content"
        class="tui-playlistSummary__content"
        v-html="summary"
      />
      <div v-else slot="content" class="tui-playlistSummary__placeholder">
        {{ $str('adddescription', 'totara_playlist') }}
      </div>
    </InlineEditing>

    <Form v-else>
      <Weka
        component="totara_playlist"
        area="summary"
        :doc="content.doc"
        :placeholder="$str('adddescription', 'totara_playlist')"
        class="tui-playlistSummary__editor"
        @update="handleUpdate"
      />
      <DoneCancelGroup
        :loading="submitting"
        :disabled="submitting"
        @done="submit"
        @cancel="editing = false"
      />
    </Form>
  </div>
</template>

<script>
import { debounce } from 'tui/util';
import tui from 'tui/tui';
import { FORMAT_JSON_EDITOR } from 'tui/format';
import Weka from 'editor_weka/components/Weka';

import DoneCancelGroup from 'totara_engage/components/buttons/DoneCancelGroup';
import InlineEditing from 'totara_engage/components/form/InlineEditing';
import getPlaylist from 'totara_playlist/graphql/get_playlist_raw';
import updatePlaylist from 'totara_playlist/graphql/update_playlist_summary';

export default {
  components: {
    DoneCancelGroup,
    InlineEditing,
    Weka,
  },

  props: {
    instanceId: {
      type: [String, Number],
      required: true,
    },

    summary: {
      type: String,
      default: null,
    },

    updateAble: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      editing: false,
      submitting: false,
      content: {
        doc: null,
        isEmpty: true,
        summaryformat: null,
      },
    };
  },

  apollo: {
    playlist: {
      query: getPlaylist,
      fetchPolicy: 'network-only',

      variables() {
        return {
          id: this.instanceId,
        };
      },

      result({ data: { playlist } }) {
        if (playlist) {
          if (playlist.summary) {
            this.content.doc = JSON.parse(playlist.summary);
            if (this.content.doc.content[0].content) {
              this.content.isEmpty = false;
            }
          }

          if (playlist.summaryformat) {
            this.content.summaryformat = playlist.summaryformat;
          }
        }
      },
    },
  },
  mounted() {
    this.$_scan();
  },

  updated() {
    this.$_scan();
  },

  methods: {
    handleUpdate(opt) {
      this.$_readJson(opt);
    },

    $_readJson: debounce(
      /**
       *
       * @param {Object} opt
       */
      function(opt) {
        this.content.doc = opt.getJSON();
        this.content.isEmpty =
          opt.isEmpty() || !this.content.doc.content[0].content;
      },
      250,
      { perArgs: false }
    ),

    submit() {
      this.submitting = true;

      this.$apollo
        .mutate({
          mutation: updatePlaylist,
          variables: {
            id: this.instanceId,
            summary: JSON.stringify(this.content.doc),
            summary_format: FORMAT_JSON_EDITOR,
          },

          /**
           *
           * @param {DataProxy} proxy
           * @param {Object} data
           */
          updateQuery: (proxy, data) => {
            proxy.writeQuery({
              query: getPlaylist,
              variables: {
                id: this.instanceId,
              },

              data: data,
            });
          },
        })
        .finally(() => {
          this.editing = false;
          this.submitting = false;
        });
    },

    $_scan() {
      if (!this.$refs.content) {
        return;
      }

      let element = this.$refs.content;
      tui.scan(element);
    },
  },
};
</script>

<lang-strings>
  {
    "totara_playlist": [
      "adddescription"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-playlistSummary {
  margin: var(--gap-8) 0;

  > p {
    margin: 0;
  }

  &__placeholder {
    color: var(--color-state);
  }
}
</style>

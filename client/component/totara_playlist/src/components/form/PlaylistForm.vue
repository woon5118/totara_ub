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

  @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
  @module totara_playlist
-->

<template>
  <Form :vertical="true" input-width="full" class="tui-playlistForm">
    <FormRow
      v-slot="{ id }"
      :label="$str('playlisttitle', 'totara_playlist')"
      :hidden="true"
      :required="true"
      class="tui-playlistForm__title"
    >
      <InputText
        :id="id"
        v-model="playlist.name"
        name="playlist-title"
        :max="60"
        :placeholder="$str('entertitle', 'totara_playlist')"
        :disabled="submitting"
        :required="true"
      />
    </FormRow>

    <div class="tui-playlistForm__description">
      <FormRow
        v-slot="{ id }"
        :required="true"
        :hidden="true"
        :label="$str('playlistdescription', 'totara_playlist')"
        class="tui-playlistForm__description__formRow"
      >
        <Weka
          :id="id"
          component="totara_playlist"
          area="summary"
          :doc="summary.doc"
          :placeholder="$str('adddescription', 'totara_playlist')"
          class="tui-playlistForm__description__formRow__textArea"
          @update="handleUpdate"
        />
      </FormRow>

      <div class="tui-playlistForm__description__tip">
        <p>{{ $str('contributetip', 'totara_engage') }}</p>
        <Popover position="right">
          <template v-slot:trigger="{ isOpen }">
            <ButtonIcon
              :aria-expanded="isOpen.toString()"
              :aria-label="$str('info', 'moodle')"
              class="tui-playlistForm__description__iconButton"
              :styleclass="{
                primary: true,
                small: true,
                transparentNoPadding: true,
              }"
            >
              <InfoIcon />
            </ButtonIcon>
          </template>

          <p class="tui-playlistForm__description__tip__content">
            {{ $str('contributetip_help', 'totara_engage') }}
          </p>
        </Popover>
      </div>
    </div>

    <ButtonGroup class="tui-playlistForm__buttons">
      <Button
        :loading="submitting"
        :styleclass="{ primary: 'true' }"
        :disabled="disabled"
        :aria-label="$str('createplaylistshort', 'totara_playlist')"
        :text="$str('next', 'moodle')"
        @click="submit"
      />

      <CancelButton :disabled="submitting" @click="$emit('cancel')" />
    </ButtonGroup>
  </Form>
</template>

<script>
import InputText from 'tui/components/form/InputText';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import Button from 'tui/components/buttons/Button';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import CancelButton from 'tui/components/buttons/Cancel';
import Popover from 'tui/components/popover/Popover';
import FormRow from 'tui/components/form/FormRow';
import Form from 'tui/components/form/Form';
import InfoIcon from 'tui/components/icons/Info';
import Weka from 'editor_weka/components/Weka';
import { debounce } from 'tui/util';
import { FORMAT_JSON_EDITOR } from 'tui/format';

export default {
  components: {
    InputText,
    ButtonGroup,
    ButtonIcon,
    Button,
    CancelButton,
    Popover,
    InfoIcon,
    FormRow,
    Form,
    Weka,
  },

  props: {
    playlist: {
      type: Object,
      default() {
        return {
          name: '',
          summary: '',
        };
      },
    },
  },

  data() {
    return {
      description: this.$id('engageContribute-description'),
      submitting: false,
      summary: {
        // Default state of editor
        doc: null,
        isEmpty: true,
      },
    };
  },

  computed: {
    disabled() {
      return this.playlist.name.length === 0 || this.submitting;
    },
  },

  methods: {
    submit() {
      const params = {
        name: this.playlist.name,
        summary: JSON.stringify(this.summary.doc),
        summary_format: FORMAT_JSON_EDITOR,
      };

      this.$emit('next', params);
    },

    /**
     *
     * @param {Object} opt
     */
    handleUpdate(opt) {
      this.$_readJson(opt);
    },

    $_readJson: debounce(
      /**
       *
       * @param {Object} opt
       */
      function(opt) {
        this.summary.doc = opt.getJSON();
        this.summary.isEmpty = opt.isEmpty();
      },
      250,
      { perArgs: false }
    ),
  },
};
</script>

<lang-strings>
  {
    "moodle": [
      "info",
      "next"
    ],
    "totara_core": [
      "save"
    ],
    "totara_playlist": [
      "createplaylistshort",
      "entertitle",
      "adddescription",
      "playlisttitle",
      "playlistdescription"
    ],
    "totara_engage": [
      "contributetip",
      "contributetip_help"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-playlistForm {
  display: flex;
  flex: 1;
  flex-direction: column;
  margin-top: var(--gap-8);

  &__title {
    // Reset margin of form row.
    &.tui-formRow {
      margin-bottom: 0;
    }

    .tui-formRow__desc {
      // No description/label for this form row, so we just make its margin to be zero, save some spaces.
      margin: 0;
    }
  }

  &__description {
    display: flex;
    flex: 2;
    flex-direction: column;
    margin-top: var(--gap-8);

    &__tip {
      position: relative;
      display: flex;

      &__content {
        margin: 0;
      }
    }

    &__formRow {
      // Making the form row to be expanded
      flex: 1;

      .tui-formRow {
        &__desc {
          // Save some space here, as there are no description/label.
          margin: 0;
        }

        &__action {
          display: flex;
          flex: 1;
          flex-direction: column;
        }
      }

      &__textArea {
        flex: 1;
      }
    }
  }

  &__buttons {
    display: flex;
    justify-content: flex-end;
    margin-top: var(--gap-8);
  }
}
</style>

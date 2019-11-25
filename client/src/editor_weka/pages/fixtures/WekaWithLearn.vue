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
  @module editor_weka
-->
<template>
  <div class="tui-wekaWithLearn">
    <h3 class="tui-wekaWithLearn__title">
      {{ $str('pluginname', 'editor_weka') }}
    </h3>

    <Form
      method="POST"
      :action="
        $url('/lib/editor/weka/tests/fixtures/weka_with_learn.php', {
          sesskey: sesskey,
        })
      "
      class="tui-wekaWithLearn__form"
      input-width="full"
      :vertical="true"
    >
      <FormRow
        v-slot="{ id }"
        :label="$str('pluginname', 'editor_weka')"
        class="tui-wekaWithLearn__form__row"
      >
        <Weka
          :id="id"
          component="editor_weka"
          area="learn"
          :doc="doc"
          :instance-id="instanceId"
          :file-item-id="instanceId"
          @update="updateContent"
        />
      </FormRow>

      <FormRow
        v-show="false"
        v-slot="{ id }"
        :hidden="true"
        class="tui-wekaWithLearn__form__row"
      >
        <InputText
          :id="id"
          name="json_content"
          :value="documentValue"
          :hidden="true"
        />
      </FormRow>

      <FormRow
        v-show="false"
        v-slot="{ id }"
        :hidden="true"
        class="tui-wekaWithLearn__form__row"
      >
        <InputText :id="id" name="item_id" :value="itemId" :hidden="true" />
      </FormRow>

      <ButtonGroup class="tui-wekaWithLearn__form__buttonGroup">
        <Submit />
        <Button
          type="submit"
          name="cancel"
          value="1"
          :text="$str('cancel', 'moodle')"
        />
      </ButtonGroup>
    </Form>

    <hr />

    <pre class="tui-wekaWithLearn__code">{{ jsonPretty }}</pre>
  </div>
</template>

<script>
import Form from 'tui/components/form/Form';
import FormRow from 'tui/components/form/FormRow';
import Weka from 'editor_weka/components/Weka';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import InputText from 'tui/components/form/InputText';
import { debounce } from 'tui/util';
import Submit from 'tui/components/buttons/Submit';
import Button from 'tui/components/buttons/Button';

export default {
  components: {
    Form,
    FormRow,
    Weka,
    ButtonGroup,
    InputText,
    Submit,
    Button,
  },

  props: {
    defaultDoc: {
      type: String,
      default: '',
    },

    instanceId: [String, Number],
    sesskey: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      doc: null,
      itemId: this.instanceId,
    };
  },

  computed: {
    documentValue() {
      if (this.doc === null) {
        return '';
      }

      return JSON.stringify(this.doc);
    },

    jsonPretty() {
      return JSON.stringify(this.doc, undefined, 2);
    },
  },

  watch: {
    defaultDoc: {
      immediate: true,
      handler(value) {
        if (value) {
          this.doc = JSON.parse(value);
        }
      },
    },
  },

  methods: {
    $_readJSON: debounce(function(option) {
      this.doc = option.getJSON();
      this.itemId = option.getFileStorageItemId();
    }, 250),

    updateContent(option) {
      this.$_readJSON(option);
    },
  },
};
</script>

<lang-strings>
  {
    "editor_weka": [
      "pluginname"
    ],
    "moodle": [
      "cancel"
    ]
  }
</lang-strings>

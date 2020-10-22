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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module performelement_custom_rating_scale
-->

<template>
  <div class="tui-customRatingScaleAdminEdit">
    <PerformAdminCustomElementEdit
      v-if="ready"
      :initial-values="initialValues"
      :settings="settings"
      @cancel="$emit('display')"
      @update="$emit('update', $event)"
    >
      <FormRow
        v-slot="{ labelId }"
        :label="
          $str('custom_rating_options', 'performelement_custom_rating_scale')
        "
        :helpmsg="
          $str('custom_values_help', 'performelement_custom_rating_scale')
        "
        :required="true"
      >
        <FieldArray v-slot="{ items, push, remove }" path="options">
          <Repeater
            :rows="items"
            :min-rows="minRows"
            :delete-icon="true"
            :allow-deleting-first-items="false"
            :aria-labelledby="labelId"
            @add="push()"
            @remove="(item, i) => remove(i)"
          >
            <template v-slot:header>
              <InputSet split char-length="30">
                <InputSetCol units="5">
                  <Label
                    :label="$str('text', 'performelement_custom_rating_scale')"
                    subfield
                  />
                </InputSetCol>
                <InputSetCol>
                  <Label
                    :label="$str('score', 'performelement_custom_rating_scale')"
                    subfield
                  />
                </InputSetCol>
              </InputSet>
            </template>

            <template v-slot="{ index }">
              <InputSet split char-length="30">
                <InputSetCol units="5">
                  <FormText
                    :name="[index, 'value', 'text']"
                    :validations="v => [v.required(), v.maxLength(1024)]"
                    :aria-label="
                      $str(
                        'answer_text',
                        'performelement_custom_rating_scale',
                        {
                          index: index + 1,
                        }
                      )
                    "
                  />
                </InputSetCol>

                <InputSetCol>
                  <FormNumber
                    :name="[index, 'value', 'score']"
                    :validations="v => [v.required()]"
                    :aria-label="
                      $str(
                        'answer_score',
                        'performelement_custom_rating_scale',
                        {
                          index: index + 1,
                        }
                      )
                    "
                  />
                </InputSetCol>
              </InputSet>
            </template>
            <template v-slot:add>
              <ButtonIcon
                :aria-label="$str('add', 'core')"
                :styleclass="{ small: true }"
                class="tui-customRatingScaleAdminEdit__addOption"
                @click="push(createField())"
              >
                <AddIcon />
              </ButtonIcon>
            </template>
          </Repeater>
        </FieldArray>
      </FormRow>
    </PerformAdminCustomElementEdit>
  </div>
</template>

<script>
import AddIcon from 'tui/components/icons/Add';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import InputSet from 'tui/components/form/InputSet';
import InputSetCol from 'tui/components/form/InputSetCol';
import Label from 'tui/components/form/Label';
import PerformAdminCustomElementEdit from 'mod_perform/components/element/PerformAdminCustomElementEdit';
import Repeater from 'tui/components/form/Repeater';
import {
  FormRow,
  FieldArray,
  FormNumber,
  FormText,
} from 'tui/components/uniform';

export default {
  components: {
    AddIcon,
    ButtonIcon,
    FieldArray,
    FormRow,
    FormNumber,
    FormText,
    InputSet,
    InputSetCol,
    Label,
    PerformAdminCustomElementEdit,
    Repeater,
  },

  inheritAttrs: false,

  props: {
    data: Object,
    identifier: String,
    isRequired: Boolean,
    rawData: Object,
    rawTitle: String,
    settings: Object,
  },

  data() {
    return {
      initialValues: {
        identifier: this.identifier,
        options: [],
        rawTitle: this.rawTitle,
        responseRequired: this.isRequired,
      },
      minRows: 2,
      ready: false,
      responseRequired: this.isRequired,
    };
  },

  mounted() {
    // If no existing data
    if (!this.rawData.options) {
      this.initialValues.options.push(this.createField(), this.createField());
    } else {
      this.initialValues.options = this.rawData.options;
    }

    this.ready = 'true';
  },

  methods: {
    /**
     * Provide unique name for new repeater options
     *
     * @returns {Object}
     */
    createField() {
      const randomInt = Math.floor(Math.random() * Math.floor(10000000));
      return { name: 'option_' + randomInt, value: { text: '', score: null } };
    },
  },
};
</script>

<lang-strings>
{
  "performelement_custom_rating_scale": [
    "answer_score",
    "answer_text",
    "custom_rating_options",
    "custom_values_help",
    "score",
    "text"
  ]
}
</lang-strings>

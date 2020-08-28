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
  @module engage_survey
-->

<template>
  <Uniform
    class="tui-totaraEngage-surveyForm"
    :initial-values="initialValues"
    :vertical="true"
    input-width="full"
    @submit="submit"
    @change="change"
  >
    <div class="tui-totaraEngage-surveyForm__title">
      <FieldContextProvider>
        <FormText
          name="question"
          :validations="v => [v.required()]"
          :maxlength="60"
          :aria-label="$str('formtitle', 'engage_survey')"
          :placeholder="$str('formtitle', 'engage_survey')"
          :disabled="submitting"
        />
      </FieldContextProvider>
    </div>

    <FormRow :label="$str('formtypetitle', 'engage_survey')">
      <FormRadioGroup
        name="optionType"
        :validations="v => [v.required()]"
        :horizontal="true"
      >
        <Radio
          name="optionType"
          :value="singleChoice"
          :class="[
            `tui-totaraEngage-surveyForm__optionType`,
            `tui-totaraEngage-surveyForm__optionType--single`,
          ]"
        >
          {{ $str('optionsingle', 'engage_survey') }}
        </Radio>
        <Radio
          name="optionType"
          :value="multiChoice"
          :class="[
            `tui-totaraEngage-surveyForm__optionType`,
            `tui-totaraEngage-surveyForm__optionType--multiple`,
          ]"
        >
          {{ $str('optionmultiple', 'engage_survey') }}
        </Radio>
      </FormRadioGroup>
    </FormRow>

    <FormRow
      :label="$str('optionstitle', 'engage_survey')"
      class="tui-totaraEngage-surveyForm__answerTitle"
    >
      <FieldArray v-slot="{ items, push, remove }" path="options">
        <Repeater
          :rows="items"
          :min-rows="minOptions"
          :max-rows="maxOptions"
          :disabled="submitting"
          :delete-icon="true"
          :allow-deleting-first-items="false"
          class="tui-totaraEngage-surveyForm__repeater"
          @add="push(newOption())"
          @remove="(item, i) => remove(i)"
        >
          <template v-slot="{ row, index }">
            <div class="tui-totaraEngage-surveyForm__repeater__input">
              <FieldContextProvider>
                <FormText
                  :name="[index, 'text']"
                  :validations="v => [v.required()]"
                  :maxlength="80"
                  :aria-label="$str('option', 'engage_survey')"
                />
              </FieldContextProvider>
            </div>
          </template>
        </Repeater>
      </FieldArray>
    </FormRow>

    <ButtonGroup
      class="tui-totaraEngage-surveyForm__buttons"
      :class="{
        'tui-totaraEngage-surveyForm__buttons--right': showButtonRight,
        'tui-totaraEngage-surveyForm__buttons--left': showButtonLeft,
      }"
    >
      <LoadingButton
        class="tui-totaraEngage-surveyForm__button"
        type="submit"
        :loading="submitting"
        :primary="true"
        :disabled="disabled"
        :text="buttonText"
      />
      <CancelButton
        :disabled="submitting"
        class="tui-totaraEngage-surveyForm__cancelButton"
        @click="$emit('cancel')"
      />
    </ButtonGroup>
  </Uniform>
</template>

<script>
import {
  Uniform,
  FieldArray,
  FormRow,
  FormText,
  FormRadioGroup,
} from 'tui/components/uniform';
import FieldContextProvider from 'tui/components/reform/FieldContextProvider';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import CancelButton from 'tui/components/buttons/Cancel';
import LoadingButton from 'totara_engage/components/buttons/LoadingButton';
import Radio from 'tui/components/form/Radio';
import Repeater from 'tui/components/form/Repeater';
import { AnswerType } from 'totara_engage/index';

export default {
  components: {
    Uniform,
    FieldArray,
    FormRow,
    FormText,
    FieldContextProvider,
    FormRadioGroup,
    ButtonGroup,
    LoadingButton,
    CancelButton,
    Radio,
    Repeater,
  },

  props: {
    survey: {
      type: Object,
      default() {
        return {
          question: '',
          type: '',
          options: [],
          questionId: null,
        };
      },

      validator: prop =>
        'question' in prop && 'type' in prop && 'options' in prop,
    },

    submitting: {
      type: Boolean,
      default: false,
    },

    buttonContent: {
      type: String,
      default() {
        return this.$str('next', 'moodle');
      },
    },

    showButtonRight: {
      type: Boolean,
      default: true,
    },
    showButtonLeft: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    const minOptions = 2;

    const options = Array.isArray(this.survey.options)
      ? this.survey.options
      : [];
    while (options.length < minOptions) {
      options.push(this.newOption());
    }

    return {
      multiChoice: String(AnswerType.MULTI_CHOICE),
      singleChoice: String(AnswerType.SINGLE_CHOICE),
      minOptions,
      maxOptions: 10,
      disabled: true,

      initialValues: {
        question: this.survey.question,
        options,
        optionType: this.survey.type || String(AnswerType.MULTI_CHOICE),
      },
    };
  },
  computed: {
    buttonText() {
      return this.buttonContent;
    },
  },
  methods: {
    /**
     * @returns {object}
     */
    newOption() {
      return { text: '', id: 0 };
    },

    submit(values) {
      const params = {
        options: values.options,
        question: values.question,
        type: values.optionType,

        // If it is for creation, then this should be null.
        questionId: this.survey.questionId,
      };
      this.$emit('next', params);
    },

    change(values) {
      const { question, options } = values;
      this.disabled = true;
      if (question.length > 0) {
        const result = Array.prototype.slice
          .call(options, 0, 2)
          .filter(option => option.text !== '');

        if (result.length === 2) {
          this.disabled = false;
        }
      }
    },
  },
};
</script>

<lang-strings>
  {
    "moodle": [
      "next"
    ],

    "engage_survey": [
      "formtitle",
      "formtypetitle",
      "optionstitle",
      "optionsingle",
      "optionmultiple",
      "option"
    ]
  }
</lang-strings>

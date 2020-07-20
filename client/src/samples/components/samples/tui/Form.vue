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
  @module samples
-->

<template>
  <Form :class="'tui-testForm'">
    <FormRow
      v-slot="{ id, label }"
      :label="$str('username', 'moodle')"
      helpmsg="this field is for bla"
    >
      <InputText
        :id="id"
        v-model="name"
        :disabled="disabled"
        :placeholder="label"
        @submit="inputSubmit"
      />
    </FormRow>

    <FormRow
      v-slot="{ ariaDescribedby, ariaLabel, id, label }"
      :label="$str('imageurl', 'editor')"
      :hidden="true"
    >
      <InputUrl
        :id="id"
        v-model="image"
        :aria-describedby="ariaDescribedby"
        :aria-label="ariaLabel"
        :disabled="disabled"
        :placeholder="label"
        @submit="inputSubmit"
      />
    </FormRow>

    <FormRow v-slot="{ id, label }" :label="$str('password', 'moodle')">
      <InputPassword
        :id="id"
        autocomplete="off"
        :disabled="disabled"
        :placeholder="label"
        @submit="inputSubmit"
      />
    </FormRow>

    <FormRow v-slot="{ id, label }" :label="$str('search', 'moodle')">
      <InputSearch
        :id="id"
        v-model="search"
        :disabled="disabled"
        :placeholder="label"
        @submit="inputSubmit"
      />
    </FormRow>

    <FormRow v-slot="{ id, label }" :label="$str('email', 'moodle')">
      <InputEmail
        :id="id"
        v-model="email"
        :disabled="disabled"
        :placeholder="label"
        @submit="inputSubmit"
      />
    </FormRow>

    <FormRow v-slot="{ id, label }" :label="$str('phone', 'moodle')">
      <InputTel
        :id="id"
        v-model="phone"
        :disabled="disabled"
        :placeholder="label"
        @submit="inputSubmit"
      />
    </FormRow>

    <FormRow v-slot="{ id, label }" :label="$str('number', 'samples')">
      <InputNumber
        :id="id"
        v-model="number"
        :disabled="disabled"
        :placeholder="label"
        @submit="inputSubmit"
      />
    </FormRow>

    <FormRow v-slot="{ id, label }" :label="'Datalist example'">
      <Datalist
        :id="suggestionid + 'datalist'"
        :options="['aaa', 'bbb', 'ccc', 'ddd']"
      />
      <InputText
        :id="id"
        :disabled="disabled"
        :list="suggestionid + 'datalist'"
        :placeholder="label"
        @submit="inputSubmit"
      />
    </FormRow>

    <InputHidden v-model="hidden" :disabled="disabled" />

    <FormRow v-slot="{ labelId }" label="Favourite Battleship">
      <RadioGroup
        v-model="battleship"
        :aria-labelledby="labelId"
        :disabled="disabled"
      >
        <Radio value="hms-victory">HMS Victory</Radio>
        <Radio value="bismarck">Bismarck</Radio>
        <Radio value="uss-enterprise">USS Enterprise</Radio>
        <Radio value="yamato">Yamato</Radio>
      </RadioGroup>
    </FormRow>

    <FormRow v-slot="{ labelId }" label="City">
      <RadioGroup
        :aria-labelledby="labelId"
        :disabled="disabled"
        :horizontal="true"
        required
      >
        <Radio value="auckland">Auckland</Radio>
        <Radio value="wellington">Christchurch</Radio>
        <Radio value="dunedin">Dunedin</Radio>
        <Radio value="hamilton">Hamilton</Radio>
        <Radio value="wellington">Wellington</Radio>
      </RadioGroup>
    </FormRow>

    <FormRow v-slot="{ labelId }" label="Best orange">
      <RadioGroup
        :aria-labelledby="labelId"
        :disabled="disabled"
        :horizontal="true"
      >
        <Radio value="orange">Orange</Radio>
        <Radio value="mandarin">Mandarin</Radio>
        <Radio value="bergamot">Bergamot</Radio>
      </RadioGroup>
    </FormRow>

    <FormRow>
      <Checkbox v-model="terms" :disabled="disabled">
        I agree to the
        <a :href="$url('/terms.php')">Terms and Conditions</a>
      </Checkbox>
    </FormRow>

    <FormRow v-slot="{ id }" label="Select">
      <Select
        :id="id"
        v-model="select"
        :options="options"
        :disabled="disabled"
      />
    </FormRow>

    <FormRow label="Add new section">
      <Repeater
        :rows="rows"
        :min-rows="minRows"
        :max-rows="maxRows"
        :disabled="disabled"
        :delete-icon="!disabled"
        :allow-deleting-first-items="!disabled"
        class="tui-testForm__repeater"
        @add="addNewSection"
        @remove="deleteSection"
      >
        <template v-slot="{ row }">
          <div>
            <InputText
              v-model="row.value"
              aria-label="aria-label"
              :disabled="row.disabled"
              :placeholder="row.label"
            />
            <RadioGroup v-model="row.battleship" :disabled="disabled">
              <Radio value="hms-victory">HMS Victory</Radio>
              <Radio value="bismarck">Bismarck</Radio>
              <Radio value="uss-enterprise">USS Enterprise</Radio>
              <Radio value="yamato">Yamato</Radio>
            </RadioGroup>
          </div>
        </template>
        <template v-if="disabled" v-slot:add>
          <ButtonIcon
            :aria-label="$str('add', 'moodle')"
            :styleclass="{ small: true }"
            :disabled="disabled"
            :text="$str('add', 'moodle')"
            @click.stop="$emit('add')"
          >
            <AddIcon />
          </ButtonIcon>
        </template>
      </Repeater>
    </FormRow>

    <FormRow v-slot="{ id }" label="Comment">
      <Textarea :id="id" v-model="comment" :disabled="disabled" :rows="4" />
    </FormRow>

    <FormRowActionButtons @cancel="formCancel" @submit.prevent="formSubmit" />
  </Form>
</template>

<script>
// Components
import AddIcon from 'tui/components/icons/common/Add';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import Checkbox from 'tui/components/form/Checkbox';
import Datalist from 'tui/components/form/Datalist';
import Form from 'tui/components/form/Form';
import FormRow from 'tui/components/form/FormRow';
import FormRowActionButtons from 'tui/components/form/FormRowActionButtons';
import InputEmail from 'tui/components/form/InputEmail';
import InputHidden from 'tui/components/form/InputHidden';
import InputNumber from 'tui/components/form/InputNumber';
import InputPassword from 'tui/components/form/InputPassword';
import InputSearch from 'tui/components/form/InputSearch';
import InputTel from 'tui/components/form/InputTel';
import InputText from 'tui/components/form/InputText';
import InputUrl from 'tui/components/form/InputUrl';
import Radio from 'tui/components/form/Radio';
import RadioGroup from 'tui/components/form/RadioGroup';
import Repeater from 'tui/components/form/Repeater';
import Select from 'tui/components/form/Select';
import Textarea from 'tui/components/form/Textarea';

export default {
  components: {
    AddIcon,
    ButtonIcon,
    Checkbox,
    Datalist,
    Form,
    FormRow,
    FormRowActionButtons,
    InputEmail,
    InputHidden,
    InputNumber,
    InputPassword,
    InputSearch,
    InputTel,
    InputText,
    InputUrl,
    Radio,
    RadioGroup,
    Repeater,
    Select,
    Textarea,
  },

  data() {
    return {
      disabled: false,
      email: '',
      hidden: 'hiddenvalue',
      icon: 'core|i/group',
      image: '',
      name: '',
      number: '',
      phone: '',
      search: '',
      suggestionid: 'example',
      size: '300',
      battleship: 'hms-victory',
      terms: false,
      options: [
        'Option 1',
        { label: 'Option 2', id: 2 },
        {
          label: 'Group',
          options: ['Option 3', { label: 'Option 4', id: 4 }],
        },
      ],
      select: 2,
      rows: [
        {
          value: 'first value',
          battleship: 'hms-victory',
          battleshipLabel: 'HMS Victory',
          disabled: false,
          label: 'first label',
        },
        {
          value: '',
          battleship: 'bismarck',
          battleshipLabel: 'Bismarck',
          disabled: false,
          label: 'second label',
        },
        {
          value: 'third value',
          battleship: 'uss-enterprise',
          battleshipLabel: 'USS Enterprise',
          disabled: false,
          label: 'third label',
        },
      ],
      minRows: 1,
      maxRows: 5,
      comment: '',
    };
  },

  watch: {
    rows: {
      deep: true,
      handler(val) {
        this.rows = val;
        console.log(this.rows);
      },
    },
  },

  methods: {
    disableFormInputs() {
      this.disabled = true;
      this.changeAddNewRowAble(this.disabled);
    },

    enableFormInputs() {
      this.disabled = false;
      this.changeAddNewRowAble(this.disabled);
    },

    formCancel() {
      this.enableFormInputs();
    },

    formSubmit() {
      this.disableFormInputs();
    },

    inputSubmit() {
      this.disableFormInputs();
    },

    changeAddNewRowAble(disabled) {
      this.rows.map(v => (v.disabled = disabled));
    },
    addNewSection() {
      this.rows.push(
        Object.assign(
          {},
          {
            value: '',
            disabled: false,
            label: 'the placeholder for new row',
            battleship: '',
            battleshipLabel: 'the placeholder for battleshipLabel',
          }
        )
      );
    },
    deleteSection(row) {
      this.rows = this.rows.filter(v => v !== row);
    },
  },
};
</script>

<style lang="scss">
.tui-testForm {
  &__repeater {
    display: flex;
    flex-direction: column;
    flex-wrap: wrap;
    align-items: start;
    & > * {
      margin-bottom: var(--tui-gap-2);
      padding-bottom: var(--tui-gap-2);
      border-bottom: 2px solid var(--tui-color-neutral-5);
    }
  }
}
</style>

<lang-strings>
{
  "samples": [
    "number"
  ],
  "editor": [
    "imageurl"
  ],
  "moodle": [
    "add",
    "delete",
    "email",
    "password",
    "phone",
    "search",
    "switchroleto",
    "username"
  ]
}
</lang-strings>

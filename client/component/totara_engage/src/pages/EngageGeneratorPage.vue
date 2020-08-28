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
  @module container_workspace
-->
<template>
  <Responsive class="tui-engageGeneratorPage">
    <h1 class="tui-engageGeneratorPage__title">
      {{ $str('engagegenerator', 'totara_engage') }}
    </h1>

    <Form class="tui-engageGeneratorPage__form" :vertical="true">
      <FormRow
        :label="$str('component', 'totara_engage')"
        class="tui-engageGeneratorPage__form__formRow"
      >
        <Select
          :id="id"
          v-model="form.component"
          slot-scope="{ id, label, ariaLabel }"
          :options="componentOptions"
          :aria-labelledby="ariaLabel"
          :placeholder="label"
        />
      </FormRow>

      <FormRow
        :label="$str('numberofinstances', 'totara_engage')"
        class="tui-engageGeneratorPage__form__formRow"
      >
        <InputNumber
          :id="id"
          v-model="form.number"
          slot-scope="{ id, label, ariaLabel }"
          :aria-labelledby="ariaLabel"
          :placeholder="label"
        />
      </FormRow>

      <ButtonGroup class="tui-engageGeneratorPage__buttonGroup">
        <Button
          :text="$str('ok', 'moodle')"
          :disabled="disableForm"
          :styleclass="{ primary: true }"
          @click="submitForm"
        />
      </ButtonGroup>
    </Form>
  </Responsive>
</template>

<script>
import Responsive from 'tui/components/responsive/Responsive';
import FormRow from 'tui/components/form/FormRow';
import Form from 'tui/components/form/Form';
import InputNumber from 'tui/components/form/InputNumber';
import Select from 'tui/components/form/Select';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import Button from 'tui/components/buttons/Button';

export default {
  components: {
    Button,
    Responsive,
    FormRow,
    Form,
    InputNumber,
    Select,
    ButtonGroup,
  },

  props: {
    sessionKey: {
      type: String,
      required: true,
    },

    components: {
      type: Array,
      required: true,
      validator(components) {
        components = Array.prototype.filter.call(components, component => {
          return !('label' in component) || !('component' in component);
        });

        return 0 === components.length;
      },
    },

    selectedComponent: {
      type: String,
      default: '',
    },

    selectedNumber: {
      type: [String, Number],
      default: 20,
    },
  },

  data() {
    return {
      form: {
        component: this.selectedComponent,
        number: this.selectedNumber,
      },
    };
  },

  computed: {
    disableForm() {
      return 0 == this.form.number;
    },

    componentOptions() {
      let items = Array.prototype.map.call(
        this.components,
        ({ label, component }) => {
          return {
            label,
            id: component,
          };
        }
      );

      return [
        {
          id: '',
          label: this.$str('all', 'moodle'),
        },
      ].concat(items);
    },
  },

  methods: {
    submitForm() {
      document.location.href = this.$url('/totara/engage/dev_generator.php', {
        component: this.form.component,
        number: this.form.number,
        sesskey: this.sessionKey,
      });
    },
  },
};
</script>

<lang-strings>
  {
    "totara_engage": [
      "engagegenerator",
      "component",
      "numberofinstances"
    ],
    "moodle": [
      "cancel",
      "ok",
      "all"
    ]
  }
</lang-strings>

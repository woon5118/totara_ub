<template>
  <RadioGroup
    v-model="time"
    :name="name"
    :required="required"
    class="tui-timeViewSelector"
    :aria-labelledby="ariaLabelledby"
  >
    <Radio
      v-for="(option, index) in options"
      :key="index"
      :name="name"
      :value="option.value"
    >
      {{ option.label }}
    </Radio>
  </RadioGroup>
</template>

<script>
import RadioGroup from 'tui/components/form/RadioGroup';
import Radio from 'tui/components/form/Radio';

// GraphQL queries
import getTimeViewOptions from 'totara_engage/graphql/time_view_options';

export default {
  components: {
    RadioGroup,
    Radio,
  },

  props: {
    selectedTime: {
      type: String,
      default: null,
    },

    name: {
      type: String,
      default() {
        return this.$id('time-view-setting');
      },
    },

    required: Boolean,
    ariaLabelledby: String,
  },

  apollo: {
    options: {
      query: getTimeViewOptions,
    },
  },

  data() {
    return {
      options: [],
      time: this.selectedTime,
    };
  },

  watch: {
    /**
     * @param {String} value
     */
    time(value) {
      this.$emit('change', value);
    },
  },
};
</script>

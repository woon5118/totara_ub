<template>
  <span class="tui-ProficientStatus__text">
    <FlexIcon v-if="status" icon="check-circle-success" size="300" />
    <span v-if="status">{{ $str('proficient', 'totara_competency') }}</span>
    <span v-else>{{ $str('not_proficient', 'totara_competency') }}</span>
  </span>
</template>

<script>
import FlexIcon from 'totara_core/components/icons/FlexIcon';
export default {
  components: { FlexIcon },
  props: {
    status: {
      required: true,
      type: Boolean,
    },
  },
};
</script>
<style lang="scss">
.tui-ProficientStatus__ {
  &text {
    font-weight: bold;
  }
}
</style>

<lang-strings>
  {
    "totara_competency": [
      "not_proficient",
      "proficient"
    ]
  }
</lang-strings>

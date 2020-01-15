<template>
  <div v-if="display">
    <div class="alert alert-info alert-with-icon">
      <!-- TODO bootstrap alert -->
      <div class="alert-icon">
        <FlexIcon icon="notification-info" />
      </div>
      <div
        class="alert-message"
        v-text="$str('no_competencies_assigned', 'totara_competency')"
      />
    </div>
    <div class="tui-NoCompetencyAssignments__search-competencies">
      <ActionLink
        :href="selfAssignmentUrl"
        :text="$str('assign_competencies', 'totara_competency')"
        :styleclass="{ primary: true }"
      />
    </div>
  </div>
</template>

<script>
import FlexIcon from 'totara_core/components/icons/FlexIcon';
import ActionLink from 'totara_core/components/links/ActionLink';

export default {
  components: { FlexIcon, ActionLink },
  props: {
    display: {
      required: false,
      type: Boolean,
      default: true,
    },

    selfAssignmentUrl: {
      required: true,
      type: String,
    },
  },
};
</script>
<style lang="scss">
.tui-NoCompetencyAssignments {
  &__search-competencies {
    display: flex;
    flex-direction: row-reverse;
    margin-top: 35px;
  }
}
</style>
<lang-strings>
  {
    "totara_competency": ["assign_competencies", "no_competencies_assigned"]
  }
</lang-strings>

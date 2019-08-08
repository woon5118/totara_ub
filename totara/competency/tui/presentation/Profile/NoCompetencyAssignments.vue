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
      <a
        :href="selfAssignmentUrl"
        class="btn totara_style-btn"
        v-text="$str('assign_competencies', 'totara_competency')"
      />
    </div>
  </div>
</template>

<script>
import FlexIcon from 'totara_core/containers/icons/FlexIcon';
export default {
  components: { FlexIcon },
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

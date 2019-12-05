<template>
  <div class="tui-CompetencyProfileHeader">
    <ul>
      <li v-for="(item, key) in data" :key="key">
        <AssignmentProgress :progress="item" />
      </li>
    </ul>
    <div class="tui-CompetencyProfileHeader__user-details">
      <Avatar
        v-if="!isMine"
        :src="profilePicture"
        alt="userName"
        size="medium"
        class="tui-CompetencyProfileHeader__avatar"
      />
      <ActionLink
        :href="selfAssignmentUrl"
        :text="$str('assign_competencies', 'totara_competency')"
        :styleclass="{ primary: true }"
      />
      <div
        v-if="latestAchievement"
        class="tui-CompetencyProfileHeader__user-details-latest-achievement"
      >
        <div>
          <div>
            <FlexIcon
              icon="star"
              :alt="$str('latest_achievement', 'totara_competency')"
            />
          </div>
          <strong v-text="$str('latest_achievement', 'totara_competency')" />
        </div>
        <span v-text="latestAchievement" />
      </div>
    </div>
  </div>
</template>

<script>
import FlexIcon from 'totara_core/containers/icons/FlexIcon';
import AssignmentProgress from 'totara_competency/containers/AssignmentProgress';
import ActionLink from 'totara_core/presentation/links/ActionLink';
import Avatar from 'totara_core/presentation/avatar/Avatar';

export default {
  components: { AssignmentProgress, FlexIcon, ActionLink, Avatar },
  props: {
    data: {
      required: true,
      type: Array,
    },
    latestAchievement: {
      required: true,
      validator: prop => typeof prop === 'string' || prop === null, // String or null
    },
    isMine: {
      required: true,
      type: Boolean,
    },
    profilePicture: {
      required: true,
      type: String,
    },
    userName: {
      required: true,
      type: String,
    },
    selfAssignmentUrl: {
      required: true,
      type: String,
    },
  },

  computed: {},
};
</script>
<style lang="scss">
.tui-CompetencyProfileHeader {
  display: flex;

  flex-direction: column;

  @media (min-width: $tui-screen-sm) {
    flex-direction: row;
    justify-content: space-between;
  }

  & > ul {
    display: flex;
    flex-grow: 1;
    flex-wrap: wrap;
    margin: 0;

    padding: 0;
    list-style: none;

    @media (min-width: $tui-screen-sm) {
      max-width: 80%;
    }
  }

  &__avatar {
    margin: 0 auto;
  }

  &__user-details {
    display: flex;
    flex-direction: column;
    align-self: start;
    justify-content: center;
    max-width: 300px;
    margin: 0 auto;

    & > :not(:last-child) {
      margin-bottom: 15px;
    }

    &-latest-achievement {
      display: flex;
      flex-direction: column;
      justify-content: center;
      text-align: center;

      & > :not(:last-child) {
        margin-bottom: 10px;
      }
    }
  }
}
</style>
<lang-strings>
  {
    "totara_competency": ["assign_competencies", "latest_achievement"]
  }
</lang-strings>

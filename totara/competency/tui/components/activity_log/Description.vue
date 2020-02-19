<template>
  <span>
    <FlexIcon v-if="getIcon(data)" :icon="getIcon(data)" size="300" />
    <span :class="getClass(data)">{{ data.description }}</span>
  </span>
</template>

<script>
const ACTION_ASSIGNED = 'assigned';
const ACTION_UNASSIGNED_USER_GROUP = 'unassigned_usergroup';
const ACTION_UNASSIGNED_ARCHIVED = 'unassigned_archived';
const ACTION_TRACKING_START = 'tracking_started';
const ACTION_TRACKING_END = 'tracking_ended';
const TYPE_SYSTEM = 'system';

import FlexIcon from 'totara_core/components/icons/FlexIcon';
export default {
  components: { FlexIcon },
  props: {
    data: {
      required: true,
      type: Object,
    },
  },
  methods: {
    getIcon(data) {
      if (data.assignment && data.assignment.type === TYPE_SYSTEM) {
        return null;
      }
      return {
        [ACTION_ASSIGNED]: 'user-add',
        [ACTION_UNASSIGNED_ARCHIVED]: 'user-delete',
        [ACTION_UNASSIGNED_USER_GROUP]: 'user-delete',
      }[data.assignment_action];
    },
    getClass(data) {
      let prefix = 'tui-Description__';
      if (data.proficient_status != null) {
        return `${prefix}rating`;
      } else if (data.assignment && data.assignment.type === TYPE_SYSTEM) {
        return `${prefix}system`;
      } else if (
        [ACTION_TRACKING_START, ACTION_TRACKING_END].includes(
          data.assignment_action
        )
      ) {
        return `${prefix}tracking`;
      } else {
        return null;
      }
    },
  },
};
</script>
<style lang="scss">
.tui-Description__ {
  &rating {
    font-weight: bold;
  }
  &tracking {
    font-weight: bold;
    text-transform: uppercase;
  }
  &system {
    &::before {
      content: '* ';
    }
    &::after {
      content: ' *';
    }
  }
}
</style>

<lang-strings>
  {
  }
</lang-strings>

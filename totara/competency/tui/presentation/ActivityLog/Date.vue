<template>
  <div @mouseover="showTooltip = true" @mouseleave="showTooltip = false">
    <span>{{ data.date }}</span>
    <Tooltip :display="showTooltip">
      {{ data.datetime }}
    </Tooltip>
  </div>
</template>

<script>
import Tooltip from '../../container/Tooltip';
export default {
  components: { Tooltip },
  props: {
    data: {
      required: true,
      type: Object,
    },
  },

  data: function() {
    return {
      showTooltip: false,
    };
  },
};
</script>

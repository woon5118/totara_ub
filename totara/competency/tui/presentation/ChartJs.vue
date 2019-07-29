<template>
  <div data-chart="" class="chart">
    <canvas v-bind="canvasAttributes"></canvas>
  </div>
</template>

<script>
import Chart from 'chart.js';
import 'chartjs-plugin-doughnutlabel';

export default {
  name: 'ChartJs',

  props: {
    type: {
      required: true,
      type: String
    },
    data: {
      required: true,
      type: Object
    },
    options: {
      required: false,
      type: Object,
      default: function() {
        return {};
      }
    },
    canvasAttributes: {
      required: false,
      type: Object,
      default: function() {
        return {};
      }
    }
  },

  computed: {
    config: function() {
      return {
        type: this.type,
        data: this.data,
        options: this.options
      };
    }
  },

  watch: {
    config: {
      deep: true,
      handler: function() {
        this.render();
      }
    }
  },

  mounted: function() {
    this.render();
  },

  methods: {
    render() {
      const root = this.$el.querySelector('.chart canvas');

      if (!this.chart) {
        this.chart = new Chart(root, this.config);
      } else {
        this.chart.config.data = this.config.data;
        this.chart.update();
        console.log('updating with', this.config);
      }
    }
  }
};
</script>

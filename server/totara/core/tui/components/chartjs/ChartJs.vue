<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
  @package totara_core
-->

<template>
  <div>
    <div
      v-if="header && header.trim() !== ''"
      class="tui-chartJs__header"
      :title="header"
    >
      {{ header }}
    </div>
    <canvas
      ref="canvas"
      v-bind="canvasAttributes"
      :aria-label="label"
      role="img"
      @click="clicked"
      @mousemove="hovered"
    >
      <slot :props="{ type, data, options }"><p v-text="label"/></slot>
    </canvas>
  </div>
</template>

<script>
import Chart from 'chart.js';
import theme from 'totara_core/theme';
import 'chartjs-plugin-doughnutlabel';

const hasOwnProperty = Object.prototype.hasOwnProperty;

Chart.defaults.global.defaultColor = theme.getVar(
  'tui-color-chart-background-1'
);
Chart.defaults.global.defaultFontSize = parseInt(
  theme.getVar('tui-font-size-chart-default')
);

let defaultColors = [
  {
    // 01
    baseColor: theme.getVar('tui-color-chart-background-1'),
    transparentColor: theme.getVar('tui-color-chart-transparent-1'),
  },
  {
    // 02
    baseColor: theme.getVar('tui-color-chart-background-2'),
    transparentColor: theme.getVar('tui-color-chart-transparent-2'),
  },
  {
    // 03
    baseColor: theme.getVar('tui-color-chart-background-3'),
    transparentColor: theme.getVar('tui-color-chart-transparent-3'),
  },
  {
    // 04
    baseColor: theme.getVar('tui-color-chart-background-4'),
    transparentColor: theme.getVar('tui-color-chart-transparent-4'),
  },
  {
    // 05
    baseColor: theme.getVar('tui-color-chart-background-5'),
    transparentColor: theme.getVar('tui-color-chart-transparent-5'),
  },
  {
    // 06
    baseColor: theme.getVar('tui-color-chart-background-6'),
    transparentColor: theme.getVar('tui-color-chart-transparent-6'),
  },
  {
    // 07
    baseColor: theme.getVar('tui-color-chart-background-7'),
    transparentColor: theme.getVar('tui-color-chart-transparent-7'),
  },
];

const pickColor = function(index) {
  return defaultColors[index % defaultColors.length];
};

const applyUndefinedProperties = function(from, to) {
  for (let def in from) {
    if (typeof to[def] === 'undefined' && hasOwnProperty.call(from, def)) {
      to[def] = from[def];
    }
  }
};

// Since ChartJs doesn't support default colors, we have to implement this functionality manually
// as advised by ChartJs devs https://github.com/chartjs/Chart.js/issues/1618
Chart.pluginService.register({
  beforeUpdate(chart) {
    switch (chart.config.type) {
      case 'bar': {
        // We need to apply colors differently depending on whether the bars are stacked...
        chart.data.datasets.forEach((dataset, index) => {
          if (
            typeof chart.options.scales.xAxes[0].stacked === 'boolean' &&
            chart.options.scales.xAxes[0].stacked &&
            typeof chart.options.scales.yAxes[0].stacked === 'boolean' &&
            chart.options.scales.yAxes[0].stacked
          ) {
            const baseColor = pickColor(index).baseColor;
            let defaults = {
              backgroundColor: baseColor,
            };

            applyUndefinedProperties(defaults, dataset);
          } else {
            if (typeof dataset.backgroundColor !== 'undefined') {
              return;
            }

            let backgroundColors = [];

            dataset.data.forEach((value, index) => {
              backgroundColors.push(pickColor(index).baseColor);
            });

            dataset.backgroundColor = backgroundColors;
          }
        });
        break;
      }

      case 'radar': {
        chart.data.datasets.forEach((dataset, index) => {
          const baseColor = pickColor(index).baseColor;
          let transparentColor = pickColor(index).transparentColor;

          if (typeof transparentColor === 'undefined') {
            transparentColor = baseColor;
          }

          let defaults = {
            borderColor: baseColor,
            backgroundColor: transparentColor,
            pointBackgroundColor: baseColor,
            pointBorderColor: baseColor,
            fill: true,
          };

          applyUndefinedProperties(defaults, dataset);
        });
        break;
      }

      // Fallthrough intended
      case 'line':
      case 'scatter':
      case 'bubble': {
        chart.data.datasets.forEach((dataset, index) => {
          const baseColor = pickColor(index).baseColor;
          let transparentColor = pickColor(index).transparentColor;

          if (typeof transparentColor === 'undefined') {
            transparentColor = baseColor;
          }

          let defaults = {
            borderColor: baseColor,
            backgroundColor: transparentColor,
            pointBackgroundColor: baseColor,
            pointBorderColor: baseColor,
            fill: false,
          };

          applyUndefinedProperties(defaults, dataset);
        });

        break;
      }

      // Fallthrough intended
      case 'doughnut':
      case 'pie':
      case 'polarArea': {
        chart.data.datasets.forEach(dataset => {
          if (!dataset.backgroundColor) {
            dataset.backgroundColor = [];
            dataset.data.forEach((val, index) => {
              dataset.backgroundColor.push(pickColor(index).baseColor);
            });
          }
        });
      }
    }
  },
});

export default {
  name: 'ChartJs',

  props: {
    header: {
      type: String,
      default: '',
    },
    ariaLabel: {
      type: String,
      default: '',
    },
    type: {
      required: true,
      type: String,
      validator: value =>
        [
          'line',
          'bar',
          'radar',
          'pie',
          'doughnut',
          'polarArea',
          'bubble',
          'scatter',
        ].indexOf(value) !== -1,
    },
    data: {
      required: true,
      type: Object,
    },
    options: {
      type: Object,
      default: function() {
        return {};
      },
    },
    canvasAttributes: {
      type: Object,
      default() {
        return {};
      },
    },
  },

  computed: {
    config() {
      return {
        type: this.type,
        data: this.data,
        options: this.options,
      };
    },

    label() {
      return this.ariaLabel.trim() !== '' ? this.ariaLabel : this.header;
    },
  },

  watch: {
    config: {
      deep: true,
      handler() {
        this.renderChart();
      },
    },
    type() {
      this.renderChart();
    },
  },

  mounted() {
    this.renderChart();
  },

  beforeDestroy() {
    this.chart.destroy();
  },

  methods: {
    renderChart() {
      const root = this.$refs.canvas;

      if (this.chart) {
        this.chart.destroy();
      }

      this.chart = new Chart(root, this.config);
    },

    clicked(e) {
      this.$emit(
        'click',
        e,
        this.$refs.canvas.getContext('2d'),
        this.chart,
        Chart
      );
    },

    hovered(e) {
      this.$emit(
        'hover',
        e,
        this.$refs.canvas.getContext('2d'),
        this.chart,
        Chart
      );
    },
  },
};
</script>

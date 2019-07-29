<template>
  <div data-chart="" class="chart">
    <canvas v-bind="canvasAttributes"></canvas>
  </div>
</template>

<script>
import amd from 'totara_core/amd';

let chartJS;

let loadChartJS = function() {
  return new Promise(function(resolve, reject) {
    amd('core/chartjs')
      .then(function(chart) {
        chartJS = chart;

        // Let's add our extension to be able to render text in the middle of the chart
        chartJS.pluginService.register({
          beforeDraw: function(chart) {
            let centerElement = chart.config.options.elements.center;

            if (centerElement) {
              //Get ctx from string
              let ctx = chart.chart.ctx;

              let getTextConfig = function(config) {
                //Get options from the center object in options
                let fontStyle = config.fontStyle || 'Arial';
                let txt = config.text;
                let color = config.color || '#000';
                let sidePadding = config.sidePadding || 20;
                let sidePaddingCalculated =
                  (sidePadding / 100) * (chart.innerRadius * 2);

                return {
                  fontStyle: fontStyle,
                  txt: txt,
                  color: color,
                  sidePadding: sidePadding,
                  sidePaddingCalculated: sidePaddingCalculated
                };
              };

              let renderText = function(config) {
                //Start with a base font of 30px
                ctx.font = '30px ' + config.fontStyle;

                //Get the width of the string and also the width of the element minus 10 to give it 5px side padding
                let stringWidth = ctx.measureText(config.txt).width;
                let elementWidth =
                  chart.innerRadius * 2 - config.sidePaddingCalculated;

                // Find out how much the font can grow in width.
                let widthRatio = elementWidth / stringWidth;
                let newFontSize = Math.floor(30 * widthRatio);
                let elementHeight = chart.innerRadius * 2;

                // Pick a new font size so it will not be larger than the height of label.
                let fontSizeToUse = Math.min(newFontSize, elementHeight);

                //Set font settings to draw it correctly.
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                let centerX =
                  (chart.chartArea.left + chart.chartArea.right) / 2;
                let centerY =
                  (chart.chartArea.top + chart.chartArea.bottom) / 2;
                ctx.font = fontSizeToUse + 'px ' + config.fontStyle;
                ctx.fillStyle = config.color;

                //Draw text in center
                ctx.fillText(config.txt, centerX, centerY);
              };

              if (!Array.isArray(centerElement)) {
                centerElement = [centerElement];
              }

              centerElement.forEach(function(element) {
                renderText(getTextConfig(element));
              });
            }
          }
        });

        console.log('ChartJs has been loaded');

        if (typeof resolve !== 'undefined') {
          resolve(chart);
        }
      })
      .catch(function(error) {
        console.error('Error loading ChartJS wrapper library');
        if (typeof reject !== 'undefined') {
          reject(error);
        }
      });
  });
};

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
    // Let's load chartJS if it hasn't been loaded yet
    if (!chartJS) {
      loadChartJS().then(
        function() {
          this.render();
        }.bind(this)
      );
    } else {
      this.render();
    }
  },

  methods: {
    render() {
      if (chartJS) {
        const root = this.$el.querySelector('.chart canvas');

        if (!this.chart) {
          this.chart = new chartJS(root, this.config);
        } else {
          this.chart.config.data = this.config.data;
          this.chart.update();
          console.log('updating with', this.config);
        }
      } else {
        console.log('ChartJS has not loaded yet');
      }
    }
  }
};
</script>

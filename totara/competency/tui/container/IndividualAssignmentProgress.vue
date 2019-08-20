<template>
  <ChartJs :type="type" :data="data" :options="options" />
</template>

<script>
import ChartJs from '../presentation/ChartJs';

/**
 *  To get line chart working properly there is trickery involved.
 *  The idea is as follows, we are wrapping our data set (appending & prepending)
 *  with the null values for bar chart and the values matching the first and the last respectively.
 *
 *  To make it work there is also a trick with X-axis ticks required, we have to tell chartjs to ignore
 *  our appended and prepended columns, to do so we have to specify min & max ticks matching first and
 *  last label.
 *
 *  To complicate stuff more we are appending chart with the empty data to prevent it from looking
 *  giant when displaying data for one or two competencies. To do so we are manually appending dataset
 *  with empty values BEFORE wrapping it with data from the comments above, that requires adjusting max
 *  tick manually to our fake one.
 */
export default {
  components: { ChartJs },
  props: {
    assignmentProgress: {
      required: true,
      type: Object,
    },
  },

  computed: {
    options: function() {
      let options = {
        tooltips: {
          filter(tooltipItem, data) {
            // We are filtering out
            let label = data.labels[tooltipItem.index];
            return [''].indexOf(label.trim());
          },
        },
        legend: {
          position: 'top',
          display: true,
        },
        title: {
          display: true,
          text: this.assignmentProgress.name,
        },
      };

      if (this.type === 'radar') {
        options.scale = {
          ticks: {
            beginAtZero: true,
            display: false,
            max: 100,
          },
        };
      } else {
        // This is to make bar-line chart to expand line to the borders of the graph.
        // See the comment above and below.
        let min = this.assignmentProgress.items.slice(0, 1).pop();
        let max = this.assignmentProgress.items.slice(-1).pop();

        if (min) {
          min = min.competency.fullname;
        }

        if (max) {
          max = max.competency.fullname;

          // We also need this extra condition to make it work with our additional empty data to prevent
          // charts from being giant...
          if (this.assignmentProgress.items.length <= 2) {
            max = '   ';
          }
        }

        options.scales = {
          yAxes: [
            {
              ticks: {
                beginAtZero: true,
                display: false,
              },
            },
          ],
          xAxes: [
            {
              ticks: {
                min,
                max,
              },
            },
          ],
        };
      }

      options.tooltips.callbacks = {
        label(tooltipItem, data) {
          let label = data.datasets[tooltipItem.datasetIndex].label || '';

          if (
            label &&
            data.datasets[tooltipItem.datasetIndex].rawData[tooltipItem.index]
          ) {
            label +=
              ': ' +
              data.datasets[tooltipItem.datasetIndex].rawData[tooltipItem.index]
                .name;
          }

          return label;
        },
      };

      return options;
    },

    data: function() {
      let data = {
        labels: [],
        datasets: [
          {
            label: this.$str('my_rating', 'totara_competency'), // TODO String
            backgroundColor: '#3869b150',
            borderColor: '#3869b1',
            rawData: [],
            data: [],
            values: [],
          },
          {
            label: this.$str('proficient_value', 'totara_competency'),

            // For bar charts the area under the line should be transparent
            backgroundColor: this.type === 'bar' ? 'transparent' : '#cc242850',
            borderColor: '#cc2428',
            steppedLine: 'middle',
            rawData: [],
            data: [],
            values: [],
          },
        ],
      };

      this.assignmentProgress.items.forEach(
        function(item) {
          data.labels.push(this.shorten(item.competency.fullname, 50));
          data.datasets[0].data.push(item.my_value.percentage);
          data.datasets[1].data.push(item.min_value.percentage);
          data.datasets[0].values.push(item.my_value.name);
          data.datasets[1].values.push(item.min_value.name);
          data.datasets[0].rawData.push(item.my_value);
          data.datasets[1].rawData.push(item.min_value);
        }.bind(this)
      );

      if (this.type === 'bar') {
        data.datasets[1].type = 'line';

        // Tricking ChartJS into showing the line graph expanding to the size of first and last bars
        // https://stackoverflow.com/questions/3216013/get-the-last-item-in-an-array

        if (data.datasets[0].data.length) {
          const first = data.datasets[1].data.slice(0, 1).pop();
          const last = data.datasets[1].data.slice(-1).pop();

          // Appending extra empty "bars" to the chart to avoid it being giant when displayed for a single
          // competency.
          if (this.assignmentProgress.items.length <= 2) {
            for (let i = this.assignmentProgress.items.length; i <= 3; i++) {
              data.datasets[0].data.push(null);
              data.datasets[1].data.push(last);

              if (i === 3) {
                data.labels.push('   ');
              } else {
                data.labels.push(' ');
              }
            }
          }

          data.datasets[0].rawData.push(null);
          data.datasets[0].rawData.unshift(null);
          data.datasets[1].rawData.push(null);
          data.datasets[1].rawData.unshift(null);

          data.datasets[0].data.push(null);
          data.datasets[0].data.unshift(null);

          data.datasets[1].data.unshift(first);
          data.datasets[1].data.push(last);

          data.labels.unshift('');
          data.labels.push('');
        }
      }

      return data;
    },

    type: function() {
      return this.assignmentProgress.items.length <= 2 ||
        this.assignmentProgress.items.length >= 12
        ? 'bar'
        : 'radar';
    },
  },

  mounted: function() {},

  methods: {
    shorten: function(str, maxLen, separator = ' ') {
      if (str.length <= maxLen) return str;
      return str.substr(0, str.lastIndexOf(separator, maxLen));
    },
  },
};
</script>
<style lang="scss"></style>
<lang-strings>
    {
        "totara_competency" : [
            "proficient", "not_proficient", "proficient_value", "my_rating"
        ]
    }
</lang-strings>

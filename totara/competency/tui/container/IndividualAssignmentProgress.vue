<template>
  <ChartJs :type="type" :data="data" :options="options"></ChartJs>
</template>

<script>
import ChartJs from '../presentation/ChartJs';

export default {
  components: { ChartJs },
  props: {
    assignmentProgress: {
      required: true,
      type: Object
    }
  },

  computed: {
    options: function() {
      let options = {
        tooltips: {},
        legend: {
          position: 'top',
          display: true
        },
        title: {
          display: true,
          text: this.assignmentProgress.name
        }
      };

      if (this.type === 'radar') {
        options.scale = {
          ticks: {
            beginAtZero: true,
            display: false,
            max: 100
          }
        };
      } else {
        let min = this.assignmentProgress.items.slice(0, 1).pop();
        let max = this.assignmentProgress.items.slice(-1).pop();

        if (min) {
          min = min.competency.fullname;
        }

        if (max) {
          max = max.competency.fullname;
        }

        options.scales = {
          yAxes: [
            {
              ticks: {
                beginAtZero: true,
                display: false
              }
            }
          ],
          xAxes: [
            {
              ticks: {
                min,
                max
              }
            }
          ]
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

          console.log(tooltipItem, data, label);

          return label;
        }
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
            values: []
          },
          {
            label: this.$str('proficient_value', 'totara_competency'),

            backgroundColor: '#cc242850',
            borderColor: '#cc2428',
            steppedLine: 'middle',
            rawData: [],
            data: [],
            values: []
          }
        ]
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
    }
  },

  mounted: function() {},

  methods: {
    shorten: function(str, maxLen, separator = ' ') {
      if (str.length <= maxLen) return str;
      return str.substr(0, str.lastIndexOf(separator, maxLen));
    }
  }
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

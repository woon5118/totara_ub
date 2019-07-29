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
            max: 100
          }
        };
      }

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
            data: [],
            values: []
          },
          {
            label: this.$str('proficient_value', 'totara_competency'),

            backgroundColor: '#cc242850',
            borderColor: '#cc2428',
            data: [],
            values: []
          }
        ]
      };

      if (this.type === 'bar') {
        data.datasets[1].type = 'line';
      }

      this.assignmentProgress.items.forEach(
        function(item) {
          console.log(item);

          data.labels.push(this.shorten(item.competency.fullname, 50));
          data.datasets[0].data.push(item.my_value.percentage);
          data.datasets[1].data.push(item.min_value.percentage);
          data.datasets[0].values.push(item.my_value.name);
          data.datasets[1].values.push(item.min_value.name);
        }.bind(this)
      );

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

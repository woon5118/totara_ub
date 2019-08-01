<template>
  <ChartJs
    type="doughnut"
    :data="data"
    :options="options"
    :canvas-attributes="canvasAttributes"
    class="tui-AssignmentProgress__progress-card"
  ></ChartJs>
</template>

<script>
import ChartJs from '../presentation/ChartJs';

export default {
  components: { ChartJs },
  props: {
    progress: {
      required: true,
      type: Object
    }
  },

  computed: {
    options: function() {
      return {
        plugins: {
          doughnutlabel: {
            labels: [
              {
                text: this.progress.overall_progress + '%',
                font: {
                  size: '40',
                  weight: 'bold'
                },
                color: '#3f9852'
              },
              {
                text: this.$str('proficient', 'totara_competency'),
                font: {
                  size: '24'
                },
                color: '#3f9852'
              },
              {
                text: '',
                font: {
                  size: '5'
                }
              }
            ]
          }
        },
        tooltips: {
          enabled: false
        },
        cutoutPercentage: 75,
        legend: {
          display: false
        },
        title: {
          display: true,
          text: this.truncateTitle(this.progress.name)
        },
        layout: {
          padding: {
            left: 0,
            right: 0,
            top: 0,
            bottom: 0
          }
        }
      };
    },

    data: function() {
      return {
        labels: [
          this.$str('proficient', 'totara_competency'),
          this.$str('not_proficient', 'totara_competency')
        ],
        datasets: [
          {
            data: [
              this.progress.overall_progress,
              100 - this.progress.overall_progress
            ],
            backgroundColor: ['#3f9852', '#8c8c8c40']
          }
        ]
      };
    },

    canvasAttributes: function() {
      return {
        width: '100%',
        height: '100%'
      };
    }
  },

  mounted: function() {},

  methods: {
    truncateTitle: function(title) {
      let maxLen = 25;
      let separator = ' ';

      if (title.length <= maxLen) return title;

      title = title.substr(0, title.lastIndexOf(separator, maxLen));

      return title + '...';
    }
  }
};
</script>
<style lang="scss">
.tui-AssignmentProgress__ {
  &progress-card {
    width: 200px;
  }
}
</style>
<lang-strings>
    {
        "totara_competency" : [
            "proficient", "not_proficient"
        ]
    }
</lang-strings>

<template>
  <div>
    <h2 v-text="$str('activity_log', 'totara_competency')" />
    <br />
    <pre style="margin: 0 auto; width: 350px">
                       .
            \  :  /
             ' _ '
         -= ( (_) ) =-
             .   .
            /  :  \
        .-.    '
        |.|
      /)|`|(\
     (.(|'|)`)
  ~~~~`\`'./'~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  jgs   |.|           ~~
        |`|                            ~~
       ,|'|.      (_)          ~~
        "'"        \"\
             ~~     ^~^
    </pre>
  </div>
</template>

<script>
export default {
  props: {
    userId: {
      required: true,
      type: Number,
    },
    competencyId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {};
  },

  computed: {},

  mounted: function() {},

  methods: {},
};
</script>
<style lang="scss"></style>
<lang-strings>
  {
    "totara_competency": [
      "activity_log"
    ]
  }
</lang-strings>

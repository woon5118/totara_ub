<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module samples
-->

<template>
  <Layout class="tui-sampleLayoutOneColumnContentWithSidePanel">
    <template v-slot:header>
      <div>Header Slot Placeholder</div>
      <br />
    </template>
    <template v-slot:column>
      <div class="tui-sampleLayoutOneColumnContentWithSidePanel__content">
        <h3>
          Super Resizeable when you trying to reduce the width of the browser
        </h3>
        <h4>So does mobile view</h4>
        <h5>Isn't it fantastic?</h5>
        It is a long established fact that a reader will be distracted by the
        readable content of a page when looking at its layout. The point of
        using Lorem Ipsum is that it has a more-or-less normal distribution of
        letters, as opposed to using 'Content here, content here', making it
        look like readable English. Many desktop publishing packages and web
        page editors now use Lorem Ipsum as their default model text, and a
        search for 'lorem ipsum' will uncover many web sites still in their
        infancy. Various versions have evolved over the years, sometimes by
        accident, sometimes on purpose (injected humour and the like).
      </div>
    </template>
    <template v-slot:sidepanel>
      <div class="tui-sampleLayoutOneColumnContentWithSidePanel__sidepanel">
        <h2>Side Panel</h2>
        <br />
        <p>
          There are many variations of passages of Lorem Ipsum available, but
          the majority have suffered alteration in some form, by injected
          humour, or randomised words which don't look even slightly believable.
          If you are going to use a passage of Lorem Ipsum, you need to be sure
          there isn't anything embarrassing hidden in the middle of text. All
          the Lorem Ipsum generators on the Internet tend to repeat predefined
          chunks as necessary, making this the first true generator on the
          Internet. It uses a dictionary of over 200 Latin words, combined with
          a handful of model sentence structures, to generate Lorem Ipsum which
          looks reasonable. The generated Lorem Ipsum is therefore always free
          from repetition, injected humour, or non-characteristic words etc.
        </p>
      </div>
    </template>
  </Layout>
</template>

<script>
import Layout from 'tui/components/layouts/LayoutOneColumnContentWithSidePanel';

export default {
  components: {
    Layout,
  },
};
</script>

<style lang="scss">
.tui-sampleLayoutOneColumnContentWithSidePanel {
  &__content {
    min-height: 61vh;
  }

  &__sidepanel {
    padding: var(--tui-gap-8);
  }
}
</style>

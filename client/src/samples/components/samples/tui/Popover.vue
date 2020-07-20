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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module samples
-->

<template>
  <div class="tui-samplePopover">
    <div class="tui-samplePopover__collection">
      <h3>Popover</h3>
    </div>
    <div class="tui-samplePopover__collection">
      <h4>Activation</h4>
      <div class="tui-samplePopover__buttonrow">
        <Popover :triggers="['hover']">
          <template v-slot:trigger>
            <Button text="Hover" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
        <Popover :triggers="['click']">
          <template v-slot:trigger>
            <Button text="Click" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
        <Popover :triggers="['focus']">
          <template v-slot:trigger>
            <Button text="Focus" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
      </div>
    </div>

    <div class="tui-samplePopover__collection">
      <h4>Content</h4>
      <div class="tui-samplePopover__buttonrow">
        <Popover title="Title" :triggers="['hover', 'click']">
          <template v-slot:trigger>
            <Button text="Changing content" />
          </template>
          <div v-if="more">
            Content. Popovers have optional titles and form fields/action
            buttons. Action buttons should be small. Use popovers for supporting
            contextual information.
          </div>
          <template v-slot:buttons>
            <Button
              text="Primary"
              :styleclass="{ primary: true, small: true }"
              @click="toggleMore"
            />
          </template>
        </Popover>
        <Popover :triggers="['hover', 'click']">
          <template v-slot:trigger>
            <Button text="Content" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
        <Popover title="Title" :triggers="['hover', 'click']">
          <template v-slot:trigger>
            <Button text="Content + Title" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
        <Popover title="Title" :triggers="['hover', 'click']">
          <template v-slot:trigger>
            <Button text="Content + Title + Buttons" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
          <template v-slot:buttons="{ close }">
            <Button
              text="Primary"
              :styleclass="{ primary: true, small: true }"
              @click="close"
            />
            <Button text="Secondary" :styleclass="{ small: true }" />
          </template>
        </Popover>
        <Popover title="Title" :triggers="['hover', 'click']">
          <template v-slot:trigger>
            <Button text="Changing content" />
          </template>
          <div v-if="more">
            Content. Popovers have optional titles and form fields/action
            buttons. Action buttons should be small. Use popovers for supporting
            contextual information.
          </div>
          <template v-slot:buttons>
            <Button
              text="Primary"
              :styleclass="{ primary: true, small: true }"
              @click="toggleMore"
            />
          </template>
        </Popover>
      </div>
    </div>

    <div class="tui-samplePopover__collection">
      <h4>Position</h4>
      <div class="tui-samplePopover__buttonrow">
        <Popover :triggers="['hover', 'click']" position="bottom">
          <template v-slot:trigger>
            <Button text="Bottom" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
        <Popover :triggers="['hover', 'click']" position="top">
          <template v-slot:trigger>
            <Button text="Top" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
        <Popover :triggers="['hover', 'click']" position="left">
          <template v-slot:trigger>
            <Button text="Left" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
        <Popover :triggers="['hover', 'click']" position="right">
          <template v-slot:trigger>
            <Button text="Right" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
      </div>
      <div class="tui-samplePopover__buttonrow">
        <Popover :triggers="['hover', 'click']" position="bottom-left">
          <template v-slot:trigger>
            <Button text="Bottom Left" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
        <Popover :triggers="['hover', 'click']" position="bottom-right">
          <template v-slot:trigger>
            <Button text="Bottom Right" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
        <Popover :triggers="['hover', 'click']" position="top-left">
          <template v-slot:trigger>
            <Button text="Top Left" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
        <Popover :triggers="['hover', 'click']" position="top-right">
          <template v-slot:trigger>
            <Button text="Top Right" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
      </div>
      <div class="tui-samplePopover__buttonrow">
        <Popover :triggers="['hover', 'click']" position="left-top">
          <template v-slot:trigger>
            <Button text="Left Top" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
        <Popover :triggers="['hover', 'click']" position="left-bottom">
          <template v-slot:trigger>
            <Button text="Left Bottom" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
        <Popover :triggers="['hover', 'click']" position="right-top">
          <template v-slot:trigger>
            <Button text="Right Top" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
        <Popover :triggers="['hover', 'click']" position="right-bottom">
          <template v-slot:trigger>
            <Button text="Right Bottom" />
          </template>
          Content. Popovers have optional titles and form fields/action buttons.
          Action buttons should be small. Use popovers for supporting contextual
          information.
        </Popover>
      </div>
    </div>

    <div class="tui-samplePopover__collection">
      <h4>Inside a modal</h4>
      <div class="tui-samplePopover__buttonrow">
        <Button text="Show" @click="showModal" />
        <ModalPresenter :open="modalOpen" @request-close="modalRequestClose">
          <Modal size="normal" :aria-labelledby="$id('title')">
            <ModalContent
              title="Hello"
              :title-id="$id('title')"
              :close-button="true"
            >
              <div class="tui-samplePopover__buttonrow">
                <Popover :triggers="['hover', 'click']" position="bottom">
                  <template v-slot:trigger>
                    <Button text="Bottom" />
                  </template>
                  Content. Popovers have optional titles and form fields/action
                  buttons. Action buttons should be small. Use popovers for
                  supporting contextual information.
                </Popover>
                <Popover :triggers="['hover', 'click']" position="top">
                  <template v-slot:trigger>
                    <Button text="Top" />
                  </template>
                  Content. Popovers have optional titles and form fields/action
                  buttons. Action buttons should be small. Use popovers for
                  supporting contextual information.
                </Popover>
                <Popover :triggers="['hover', 'click']" position="left">
                  <template v-slot:trigger>
                    <Button text="Left" />
                  </template>
                  Content. Popovers have optional titles and form fields/action
                  buttons. Action buttons should be small. Use popovers for
                  supporting contextual information.
                </Popover>
                <Popover :triggers="['hover', 'click']" position="right">
                  <template v-slot:trigger>
                    <Button text="Right" />
                  </template>
                  Content. Popovers have optional titles and form fields/action
                  buttons. Action buttons should be small. Use popovers for
                  supporting contextual information.
                </Popover>
              </div>
            </ModalContent>
          </Modal>
        </ModalPresenter>
      </div>
    </div>
  </div>
</template>

<script>
import Button from 'tui/components/buttons/Button';
import Modal from 'tui/components/modal/Modal';
import ModalContent from 'tui/components/modal/ModalContent';
import ModalPresenter from 'tui/components/modal/ModalPresenter';
import Popover from 'tui/components/popover/Popover';

export default {
  components: {
    Button,
    Modal,
    ModalContent,
    ModalPresenter,
    Popover,
  },

  data() {
    return {
      more: false,
      modalOpen: false,
    };
  },

  methods: {
    toggleMore() {
      this.more = !this.more;
    },

    showModal() {
      this.modalOpen = true;
    },

    modalRequestClose() {
      this.modalOpen = false;
    },
  },
};
</script>

<style lang="scss">
.tui-samplePopover {
  &__collection {
    margin-bottom: var(--tui-gap-8);
  }

  &__buttonrow {
    display: flex;
    > * + * {
      margin-left: var(--tui-gap-1);
    }
  }
}
</style>

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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module mod_perform
-->
<template>
  <Card
    class="tui-performAdminCustomElement"
    :has-hover-shadow="!isActive"
    :class="{
      'tui-performAdminCustomElement--dragging': dragging,
    }"
  >
    <div v-if="draggable" class="tui-performAdminCustomElement__moveIcon">
      <DragHandleIcon />
    </div>

    <div
      v-if="sectionElement.element.element_plugin"
      class="tui-performAdminCustomElement__actions"
    >
      <Popover
        v-if="
          sectionElement.element.identifier &&
            !isActive &&
            sectionComponent.type !== 'editing'
        "
        class="tui-performAdminCustomElement__actions-reportingId"
      >
        <h2 class="tui-performAdminCustomElement__actions-reportingIdHeader">
          {{ $str('reporting_identifier', 'mod_perform') }}
        </h2>
        <div class="tui-performAdminCustomElement__actions-reportingIdContent">
          {{ sectionElement.element.identifier }}
        </div>
        <template v-slot:trigger>
          <ButtonIcon
            class="tui-performAdminCustomElement__reportingId"
            :aria-label="
              $str(
                'reporting_identifier_a11y',
                'mod_perform',
                sectionElement.element.title
                  ? sectionElement.element.title
                  : sectionElement.element.element_plugin.name
              )
            "
            :styleclass="{ transparent: true }"
          >
            <template>
              <ReportsIcon
                :alt="$str('reporting_identifier', 'mod_perform')"
                :title="$str('reporting_identifier', 'mod_perform')"
              />
            </template>
          </ButtonIcon>
        </template>
      </Popover>

      <EditButton
        v-if="!isActive && sectionComponent.type === 'view'"
        class="tui-performAdminCustomElement__edit"
        :aria-label="
          $str(
            'edit_element',
            'mod_perform',
            sectionElement.element.title
              ? sectionElement.element.title
              : sectionElement.element.element_plugin.name
          )
        "
        @click="edit"
      />
      <DeleteButton
        v-if="!isActive && sectionComponent.type === 'view'"
        :aria-label="
          $str(
            'delete_element',
            'mod_perform',
            sectionElement.element.title
              ? sectionElement.element.title
              : sectionElement.element.element_plugin.name
          )
        "
        @click="remove"
      />
      <ButtonIcon
        v-if="isActive && sectionComponent.type === 'view'"
        :aria-label="
          $str(
            'setting_element',
            'mod_perform',
            sectionElement.element.title
              ? sectionElement.element.title
              : sectionElement.element.element_plugin.name
          )
        "
        :styleclass="{ transparentNoPadding: true }"
        @click="displayRead()"
      >
        <SettingsIcon />
      </ButtonIcon>
    </div>

    <div class="tui-performAdminCustomElement__content">
      <h3
        v-if="sectionComponent.type == 'view' && sectionElement.element.title"
        class="tui-performAdminCustomElement__content-title"
      >
        <span
          v-if="sectionElement.element.element_plugin"
          class="tui-performAdminCustomElement__content-accessibleTitle"
        >
          {{
            $str(
              'element_type_heading_a11y',
              'mod_perform',
              sectionElement.element.element_plugin.name
            )
          }}
        </span>
        <span class="tui-performAdminCustomElement__content-titleText">
          {{ sectionElement.element.title }}
        </span>
        <span
          v-if="sectionElement.element.is_required"
          class="tui-performAdminCustomElement__content-titleRequired"
        >
          *
        </span>
      </h3>

      <component
        :is="sectionComponent.component"
        ref="sectionElements"
        :key="sectionElement.id"
        :activity-state="activityState"
        :aria-hidden="true"
        :data="sectionElement.element.data"
        :element-id="sectionElement.element.id"
        :identifier="sectionElement.element.identifier"
        :is-required="sectionElement.element.is_required"
        :raw-data="sectionElement.element.raw_data"
        :raw-title="sectionElement.element.raw_title"
        :section-id="sectionId"
        :settings="sectionComponent.settings"
        :title="sectionElement.element.title"
        @display="$emit('display', sectionElement)"
        @display-read="$emit('display-read', sectionElement)"
        @update="$emit('update', $event)"
      />
    </div>
  </Card>
</template>

<script>
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import Card from 'tui/components/card/Card';
import DeleteButton from 'tui/components/buttons/DeleteIcon';
import DragHandleIcon from 'tui/components/icons/DragHandle';
import EditButton from 'tui/components/buttons/EditIcon';
import Popover from 'tui/components/popover/Popover';
import ReportsIcon from 'tui/components/icons/Reports';
import SettingsIcon from 'tui/components/icons/Settings';

export default {
  components: {
    ButtonIcon,
    Card,
    DeleteButton,
    DragHandleIcon,
    EditButton,
    Popover,
    ReportsIcon,
    SettingsIcon,
  },

  props: {
    activityState: Object,
    draggable: Boolean,
    dragging: Boolean,
    errors: Object,
    sectionElement: Object,
    sectionId: String,
    sectionComponent: Object,
  },

  computed: {
    isActive() {
      return this.activityState.name === 'ACTIVE';
    },
  },

  methods: {
    edit() {
      this.$emit('edit');
    },
    remove() {
      this.$emit('remove');
    },
    displayRead() {
      this.$emit('display-read');
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "delete_element",
      "edit_element",
      "element_type_heading_a11y",
      "reporting_identifier",
      "reporting_identifier_a11y",
      "setting_element",
      "section_element_tag_required",
      "section_element_tag_optional"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-performAdminCustomElement {
  position: relative;
  display: flex;
  flex-direction: column;
  padding: var(--gap-4);
  background: var(--color-neutral-1);

  &__actions {
    display: flex;
    flex-direction: row;
    justify-content: flex-end;

    &-reportingId {
      display: flex;
    }

    &-reportingIdHeader {
      margin: 0;
      @include tui-font-heading-label();
    }

    &-reportingIdContent {
      margin-top: var(--gap-2);
    }
  }

  &__content {
    margin-top: var(--gap-1);

    & > * + * {
      margin-top: var(--gap-4);
    }

    &-accessibleTitle {
      @include sr-only();
    }

    &-title {
      margin: 0;
      @include tui-font-heading-x-small;
    }

    &-titleRequired {
      @include tui-font-heading-label();
      color: var(--color-prompt-alert);
    }
  }

  &__moveIcon {
    position: absolute;
    top: var(--gap-1);
    left: var(--gap-2);
    display: none;
  }

  &--dragging &__moveIcon,
  &:hover &__moveIcon {
    display: block;
  }
}
</style>

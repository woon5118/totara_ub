<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
  @module container_workspace
-->
<template>
  <Modal size="normal" :aria-labelledby="id">
    <ModalContent
      :close-button="true"
      :title="
        $str(
          usersFromAudiencesToAdd > 0
            ? 'bulk_add_audiences_modal_title'
            : 'bulk_add_audiences_modal_title_no_members',
          'container_workspace',
          usersFromAudiencesToAdd
        )
      "
      :title-id="id"
      @dismiss="$emit('cancel')"
    >
      <div class="tui-workspaceAddAudienceModal">
        <template v-if="usersFromAudiencesToAdd > 0">
          <p>
            {{
              $str('bulk_add_audiences_modal_content_1', 'container_workspace')
            }}
          </p>
          <ul class="tui-workspaceAddAudienceModal__list">
            <li>
              {{
                $str(
                  'bulk_add_audiences_modal_content_2',
                  'container_workspace'
                )
              }}
            </li>
            <li>
              {{
                $str(
                  'bulk_add_audiences_modal_content_3',
                  'container_workspace'
                )
              }}
            </li>
          </ul>
        </template>
        <template v-else>
          {{
            $str(
              'bulk_add_audiences_modal_content_no_members',
              'container_workspace'
            )
          }}
        </template>
      </div>
      <template v-slot:buttons>
        <ButtonGroup>
          <Button
            :styleclass="{ primary: 'true' }"
            :disabled="loading"
            :text="
              $str(
                usersFromAudiencesToAdd > 0
                  ? 'bulk_add_audiences_modal_confirm_button'
                  : 'bulk_add_audiences_modal_reselect_button',
                'container_workspace'
              )
            "
            @click="$emit('confirm')"
          />
          <ButtonCancel
            v-if="usersFromAudiencesToAdd > 0"
            :disabled="loading"
            @click="$emit('cancel')"
          />
        </ButtonGroup>
      </template>
    </ModalContent>
  </Modal>
</template>

<script>
import Button from 'tui/components/buttons/Button';
import ButtonCancel from 'tui/components/buttons/Cancel';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import Modal from 'tui/components/modal/Modal';
import ModalContent from 'tui/components/modal/ModalContent';

export default {
  components: {
    Button,
    ButtonCancel,
    ButtonGroup,
    Modal,
    ModalContent,
  },

  props: {
    usersFromAudiencesToAdd: {
      type: [Number],
      required: true,
    },
    loading: Boolean,
  },

  computed: {
    computed: {
      id() {
        return this.$id(this.title);
      },
    },
  },
};
</script>

<lang-strings>
{
  "container_workspace": [
    "bulk_add_audiences_modal_confirm_button",
    "bulk_add_audiences_modal_content_1",
    "bulk_add_audiences_modal_content_2",
    "bulk_add_audiences_modal_content_3",
    "bulk_add_audiences_modal_content_no_members",
    "bulk_add_audiences_modal_reselect_button",
    "bulk_add_audiences_modal_title",
    "bulk_add_audiences_modal_title_no_members"
  ]
}
</lang-strings>

<style lang="scss">
.tui-workspaceAddAudienceModal {
  padding-top: var(--gap-2);

  &__list {
    & > * + * {
      margin-top: var(--gap-2);
    }
  }
}
</style>

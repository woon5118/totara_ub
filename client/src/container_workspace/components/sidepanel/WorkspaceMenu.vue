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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module container_workspace
-->

<template>
  <SidePanelNav
    v-model="innerSelectedWorkspaceId"
    class="tui-workspaceMenu"
    :aria-label="$str('workspace_navigation', 'container_workspace')"
  >
    <SidePanelNavGroup
      :title="$str('your_spaces', 'container_workspace')"
      class="tui-workspaceMenu__group"
    >
      <ContributeWorkspace
        slot="heading-side"
        class="tui-workspaceMenu__group__contribute"
        :disabled="$apollo.queries.workspaces.loading"
        @create-workspace="addWorkspace"
      />

      <template v-if="!$apollo.queries.workspaces.loading">
        <SidePanelNavLinkItem
          v-for="({ url, name, id }, index) in workspaces"
          :id="id"
          :key="index"
          :text="name"
          :url="url"
          class="tui-workspaceMenu__group__link"
        />
      </template>
    </SidePanelNavGroup>

    <div class="tui-workspaceMenu__separator" />

    <SidePanelNavGroup class="tui-workspaceMenu__group">
      <SidePanelNavLinkItem
        :id="-1"
        :url="$url('/container/type/workspace/spaces.php')"
        :text="$str('find_spaces', 'container_workspace')"
        class="tui-workspaceMenu__group__link"
      />
    </SidePanelNavGroup>
  </SidePanelNav>
</template>

<script>
import ContributeWorkspace from 'container_workspace/components/create/ContributeWorkspace';
import apolloClient from 'tui/apollo_client';
import SidePanelNav from 'tui/components/sidepanel/SidePanelNav';
import SidePanelNavGroup from 'tui/components/sidepanel/SidePanelNavGroup';
import SidePanelNavLinkItem from 'tui/components/sidepanel/SidePanelNavLinkItem';

// GraphQL queries
import getWorkspaces from 'container_workspace/graphql/my_workspace_urls';

export default {
  components: {
    SidePanelNavGroup,
    ContributeWorkspace,
    SidePanelNav,
    SidePanelNavLinkItem,
  },

  props: {
    selectedWorkspaceId: [Number, String],
  },

  apollo: {
    workspaces: {
      query: getWorkspaces,
    },
  },

  computed: {
    innerSelectedWorkspaceId: {
      get() {
        return this.selectedWorkspaceId;
      },

      /**
       * @param {Number} value
       */
      set(value) {
        this.$emit('update-selected-workspace-id', value);
      },
    },
  },

  methods: {
    /**
     *
     * @param {Object} workspace
     */
    addWorkspace(workspace) {
      const workspaces = Array.prototype.slice.call(this.workspaces);

      apolloClient.writeQuery({
        query: getWorkspaces,
        data: {
          workspaces: Array.prototype.concat.call(workspaces, [workspace]),
        },
      });

      // Update the workspace-menu selection.
      this.innerSelectedWorkspaceId = workspace.id;

      // Chaining it up to the parent.
      this.$emit('create-workspace', workspace);
    },
  },
};
</script>

<lang-strings>
  {
    "container_workspace": [
      "your_spaces",
      "find_spaces",
      "workspace_navigation"
    ]
  }
</lang-strings>

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
  <Layout class="tui-workspacePage">
    <template v-slot:left="{ direction }">
      <SidePanel
        v-if="direction === 'horizontal'"
        :show-button-control="false"
        :sticky="false"
        :initially-open="true"
      >
        <WorkspaceMenu
          :selected-workspace-id="workspaceId"
          @create-workspace="redirectToWorkspace"
        />
      </SidePanel>
    </template>

    <template v-slot:right="{ units, direction }">
      <div v-if="workspace" class="tui-workspacePage__mainContent">
        <WorkspacePageHeader
          :show-navigation="direction === 'vertical'"
          :workspace-id="workspace.id"
          :workspace-image="workspace.image"
          :workspace-name="workspace.name"
          :workspace-access="workspace.access"
          :workspace-muted="workspace.interactor.muted"
          :show-mute-button="workspace.interactor.joined"
          class="tui-workspacePage__head"
          @update-mute-status="updateMuteStatus"
        />

        <div class="tui-workspacePage__primaryAction">
          <WorkspacePrimaryAction
            :workspace-id="workspace.id"
            :workspace-name="workspace.name"
            class="tui-workspacePage__primaryAction-action"
            @update-workspace="updateWorkspace"
            @request-to-join-workspace="reloadWorkspace"
            @cancel-request-to-join-workspace="reloadWorkspace"
            @added-member="reloadWorkspace"
            @deleted-workspace="redirectToSpacePage"
            @update-mute-status="updateMuteStatus"
          />
        </div>

        <Tabs
          v-model="innerSelectedTab"
          direction="horizontal"
          class="tui-workspacePage__tabs"
        >
          <Tab
            id="discussion"
            :name="$str('discuss_tab_label', 'container_workspace')"
          >
            <WorkspaceContentLayout
              :max-units="units"
              :grid-direction="direction"
              class="tui-workspacePage__tabs-discussionTab"
            >
              <template v-slot:content>
                <WorkspaceDiscussionTab
                  v-if="workspace.interactor.can_view_discussions"
                  :workspace-total-discussions="workspace.total_discussions"
                  :selected-sort="discussionSortOption"
                  :workspace-id="workspaceId"
                  :workspace-context-id="workspace.context_id"
                  @add-discussion="addDiscussion"
                />

                <p v-else class="tui-workspacePage__tabs-text">
                  {{ $str('visibility_help', 'container_workspace') }}
                </p>
              </template>

              <WorkspaceDescription
                v-if="workspace.description"
                slot="side"
                :time-description="workspace.time_description"
                :description="workspace.description"
              />
            </WorkspaceContentLayout>
          </Tab>

          <Tab
            v-if="showLibraryTab"
            id="library"
            :disabled="!workspace.interactor.can_view_library"
            :name="$str('library_tab_label', 'container_workspace')"
          >
            <WorkspaceLibraryTab
              :workspace-id="workspaceId"
              :units="units"
              :grid-direction="direction"
            />
          </Tab>

          <Tab
            id="members"
            :disabled="!workspace.interactor.can_view_members"
            :name="
              $str(
                'member_tab_label',
                'container_workspace',
                workspace.total_members
              )
            "
          >
            <WorkspaceContentLayout
              :grid-direction="direction"
              :max-units="units"
            >
              <WorkspaceMembersTab
                slot="content"
                :workspace-id="workspaceId"
                :can-view-member-requests="
                  workspace.interactor.can_view_member_requests
                "
                :total-member-requests="workspace.total_member_requests"
                :total-members="workspace.total_members"
                :selected-sort="memberSortOption"
              />
            </WorkspaceContentLayout>
          </Tab>
        </Tabs>
      </div>
    </template>
  </Layout>
</template>

<script>
import Layout from 'tui/components/layouts/LayoutTwoColumn';
import WorkspaceMenu from 'container_workspace/components/sidepanel/WorkspaceMenu';
import Tabs from 'tui/components/tabs/Tabs';
import Tab from 'tui/components/tabs/Tab';
import WorkspaceMembersTab from 'container_workspace/components/content/tabs/WorkspaceMembersTab';
import WorkspaceDiscussionTab from 'container_workspace/components/content/tabs/WorkspaceDiscussionTab';
import WorkspaceLibraryTab from 'container_workspace/components/content/tabs/WorkspaceLibraryTab';
import WorkspacePageHeader from 'container_workspace/components/head/WorkspacePageHeader';
import { notify } from 'tui/notifications';
import SidePanel from 'tui/components/sidepanel/SidePanel';
import WorkspaceDescription from 'container_workspace/components/sidepanel/WorkspaceDescription';
import WorkspaceContentLayout from 'container_workspace/components/content/WorkspaceContentLayout';
import WorkspacePrimaryAction from 'container_workspace/components/action/WorkspacePrimaryAction';
import apolloClient from 'tui/apollo_client';

// GraphQL queries
import getWorkspace from 'container_workspace/graphql/get_workspace';
import notifications from 'container_workspace/graphql/notifications';

export default {
  components: {
    WorkspacePrimaryAction,
    Layout,
    SidePanel,
    WorkspaceMenu,
    Tabs,
    Tab,
    WorkspaceMembersTab,
    WorkspaceDiscussionTab,
    WorkspaceLibraryTab,
    WorkspacePageHeader,
    WorkspaceDescription,
    WorkspaceContentLayout,
  },

  props: {
    workspaceId: {
      type: [Number, String],
      required: true,
    },

    memberSortOption: {
      type: String,
      required: true,
    },

    discussionSortOption: {
      type: String,
      required: true,
    },

    selectedTab: {
      type: String,
      default: 'discussion',
      validator(prop) {
        return (
          'discussion' === prop || 'members' === prop || 'library' === prop
        );
      },
    },

    showLibraryTab: {
      type: Boolean,
      required: true,
    },
  },

  apollo: {
    workspace: {
      query: getWorkspace,
      variables() {
        return {
          id: this.workspaceId,
        };
      },
    },

    notifications: {
      query: notifications,
      update() {
        return [];
      },

      result({ data: { notifications } }) {
        Array.prototype.forEach.call(notifications, ({ message, type }) => {
          notify({
            message: message,
            type: type,
          });
        });
      },
    },
  },

  data() {
    return {
      workspace: null,
      innerSelectedTab: this.selectedTab,
    };
  },

  methods: {
    /**
     * Update workspace cache data.
     * @param {Object} workspace
     */
    updateWorkspace(workspace) {
      apolloClient.writeQuery({
        query: getWorkspace,
        variables: {
          id: this.workspaceId,
        },
        data: {
          workspace: workspace,
        },
      });
    },

    reloadWorkspace() {
      this.$apollo.queries.workspace.refetch();
    },

    /**
     * Redirect to index page and let the index page resolve the
     * next workspace where user should be redirect to.
     */
    redirectToSpacePage() {
      document.location.href = this.$url(
        '/container/type/workspace/index.php',
        { hold_notification: 1 }
      );
    },

    addDiscussion() {
      let { workspace } = apolloClient.readQuery({
        query: getWorkspace,
        variables: {
          id: this.workspaceId,
        },
      });

      workspace = Object.assign({}, workspace, {
        total_discussions: workspace.total_discussions + 1,
      });

      apolloClient.writeQuery({
        query: getWorkspace,
        variables: {
          id: this.workspaceId,
        },
        data: { workspace },
      });
    },

    /**
     * Redirect to a newly created workspace.
     * @param {Number} id
     */
    redirectToWorkspace({ id }) {
      document.location.href = this.$url(
        '/container/type/workspace/workspace.php',
        { id: id }
      );
    },

    /**
     *
     * @param {Boolean} status
     */
    updateMuteStatus(status) {
      let { workspace } = apolloClient.readQuery({
        query: getWorkspace,
        variables: {
          id: this.workspaceId,
        },
      });

      workspace = Object.assign({}, workspace);
      workspace.interactor = Object.assign({}, workspace.interactor);

      workspace.interactor.muted = status;
      apolloClient.writeQuery({
        query: getWorkspace,
        variables: {
          id: this.workspaceId,
        },

        data: { workspace },
      });
    },
  },
};
</script>
<lang-strings>
  {
    "container_workspace": [
      "discuss_tab_label",
      "library_tab_label",
      "member_tab_label",
      "visibility_help"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-workspacePage {
  .tui-layoutTwoColumn {
    &__heading {
      display: none;
    }
  }

  &__mainContent {
    margin: 0;

    @media (min-width: $tui-screen-sm) {
      margin-top: var(--gap-8);
    }
  }

  &__primaryAction {
    display: flex;
    width: 100%;
    margin-top: var(--gap-4);

    @media (min-width: $tui-screen-sm) {
      justify-content: flex-end;
    }

    &-action {
      width: 100%;

      @media (min-width: $tui-screen-sm) {
        width: inherit;
      }
    }
  }

  &__tabs {
    margin-top: var(--gap-4);
    .tui-tabs__tabs {
      padding-left: var(--gap-4);
      @media (min-width: $tui-screen-sm) {
        padding: 0;
      }
    }

    &-text {
      @include tui-font-body();
    }

    &-discussionTab {
      margin-top: var(--gap-4);
    }
  }
}
</style>

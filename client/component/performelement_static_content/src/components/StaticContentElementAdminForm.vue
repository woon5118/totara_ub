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

  @author Angela Kuznetsova <angela.kuznetsova@totaralearning.com>
  @module performelement_static_content
-->
<template>
  <ElementAdminForm
    :type="type"
    :error="error"
    :activity-state="activityState"
    @remove="$emit('remove')"
  >
    <template v-slot:content>
      <div class="tui-elementEditStaticContent">
        <Uniform
          v-slot="{ getSubmitting }"
          :initial-values="initialValues"
          :vertical="true"
          validation-mode="submit"
          input-width="full"
          @submit="handleSubmit"
        >
          <FormRow :label="$str('title', 'performelement_static_content')">
            <FormText name="rawTitle" :validations="v => [v.maxLength(1024)]" />
          </FormRow>
          <FormRow
            v-slot="{ id }"
            :label="
              $str(
                'static_content_placeholder',
                'performelement_static_content'
              )
            "
            :required="true"
          >
            <Weka
              v-if="draftId"
              :id="id"
              :class="{
                'tui-elementEditStaticContent__weka-invalid': errorShow,
              }"
              component="performelement_static_content"
              area="content"
              :doc="wekaDoc"
              :file-item-id="draftId"
              :placeholder="
                $str('weka_enter_content', 'performelement_static_content')
              "
              @update="handleUpdate"
            />
            <FieldError
              v-if="errorShow"
              :error="$str('required', 'performelement_static_content')"
            />
          </FormRow>
          <FormRow>
            <div class="tui-elementEditStaticContent__action-buttons">
              <FormActionButtons
                :submitting="getSubmitting()"
                @cancel="cancel"
              />
            </div>
          </FormRow>
        </Uniform>
      </div>
    </template>
  </ElementAdminForm>
</template>

<script>
import { Uniform, FormRow, FormText } from 'tui/components/uniform';
import ElementAdminForm from 'mod_perform/components/element/ElementAdminForm';
import FieldError from 'tui/components/form/FieldError';
import FormActionButtons from 'mod_perform/components/element/admin_form/ActionButtons';
import AdminFormMixin from 'mod_perform/components/element/admin_form/AdminFormMixin';
import Weka from 'editor_weka/components/Weka';

// Utils
import { debounce } from 'tui/util';

// GraphQL queries
import fileDraftId from 'core/graphql/file_unused_draft_item_id';
import prepareDraftArea from 'performelement_static_content/graphql/prepare_draft_area';

export default {
  components: {
    ElementAdminForm,
    Uniform,
    FieldError,
    FormRow,
    FormText,
    FormActionButtons,
    Weka,
  },

  mixins: [AdminFormMixin],

  props: {
    sectionId: [Number, String],
    elementId: [Number, String],
    type: Object,
    title: String,
    rawTitle: String,
    data: Object,
    rawData: Object,
    error: String,
    activityState: {
      type: Object,
      required: true,
    },
  },

  data() {
    const initialValues = {
      title: this.title,
      rawTitle: this.rawTitle,
      data: this.data,
    };
    return {
      initialValues: initialValues,
      wekaDoc: null,
      draftId: null,
      isEmpty: true,
      errorShow: false,
    };
  },

  async mounted() {
    if (this.rawData && this.rawData.wekaDoc) {
      this.wekaDoc = JSON.parse(this.rawData.wekaDoc);
      this.isEmpty = false;
    }
    if (this.sectionId && this.elementId) {
      await this.$_loadExistingDraftId();
    } else {
      await this.$_loadNewDraftId();
    }
  },

  methods: {
    /**
     *
     * @param {Object} opt
     */
    handleUpdate(opt) {
      this.$_readJson(opt);
    },

    async $_loadNewDraftId() {
      const {
        data: { item_id },
      } = await this.$apollo.mutate({ mutation: fileDraftId });
      this.draftId = item_id;
    },

    async $_loadExistingDraftId() {
      const {
        data: { draft_id },
      } = await this.$apollo.mutate({
        mutation: prepareDraftArea,
        variables: {
          section_id: parseInt(this.sectionId),
          element_id: parseInt(this.elementId),
        },
      });
      this.draftId = draft_id;
    },

    $_readJson: debounce(
      /**
       *
       * @param {Object} opt
       */
      function(opt) {
        this.wekaDoc = opt.getJSON();
        this.isEmpty = opt.isEmpty();
      },
      250,
      { perArgs: false }
    ),

    handleSubmit(values) {
      this.errorShow = false;
      if (this.isEmpty) {
        this.errorShow = true;
      } else {
        this.$emit('update', {
          title: values.rawTitle,
          data: {
            wekaDoc: JSON.stringify(this.wekaDoc),
            draftId: this.draftId,
            format: 'HTML',
            docFormat: 'FORMAT_JSON_EDITOR',
          },
        });
      }
    },

    cancel() {
      this.$emit('display');
    },
  },
};
</script>

<lang-strings>
   {
     "performelement_static_content": [
       "title",
       "required",
       "static_content_placeholder",
       "weka_enter_content"
     ]
   }
   </lang-strings>
<style lang="scss">
.tui-elementEditStaticContent {
  &__weka {
    &-invalid {
      border-color: var(--form-input-border-color-invalid);
      box-shadow: var(--form-input-shadow-invalid);
    }
  }
}
</style>

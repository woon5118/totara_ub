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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module totara_core
-->

<script>
import * as flexIcons from 'totara_core/flex_icons';
import FlexIconStandard from 'totara_core/components/icons/flex_icons/FlexIcon';
import FlexIconPix from 'totara_core/components/icons/flex_icons/FlexIconPix';
import FlexIconMustacheFallback from 'totara_core/components/icons/FlexIconMustacheFallback';
import { memoize } from 'totara_core/util';

/**
 * Get rendering data for icon identified by "id"
 *
 * @private
 * @param {string} id
 * @returns {object}
 */
function getData(id) {
  let flexData;
  try {
    flexData = flexIcons.getFlexData(id);
  } catch (e) {
    // can error if flex icons failed to load
    console.error(e);
  }
  if (flexData) {
    const component = getIconComponent(flexData.template, id);
    return {
      id,
      data: flexData.data,
      component,
    };
  }

  // fall back to pix icon
  return {
    id,
    component: FlexIconPix,
  };
}

const getDataCached = memoize(getData);

const asyncIconComponent = memoize(id => {
  return () => ({
    component: tui.loadComponent(id).catch(() => FlexIconMustacheFallback),
  });
});

/**
 * Get component (name string to be passed to h() or <component :is="">)
 * to use to render icon.
 *
 * @private
 * @param {string} template
 *     Template declared in flex icon config, e.g. 'core/flex_icon'.
 * @param {string} id
 *     ID of icon, e.g. 'alarm'.
 * @returns {object}
 */
function getIconComponent(template, id) {
  if (!template) {
    return FlexIconStandard;
  }

  const componentId = getTemplateComponentId(template);
  if (!componentId) {
    if (process.env.NODE_ENV !== 'production') {
      console.error(
        `[FlexIcon] Unable to parse flex icon template "${template}" ` +
          `on flex icon "${id}". Make sure the template name ` +
          `starts with a frankenstyle component name followed by a ` +
          `slash.`
      );
    }
    return null;
  }
  try {
    if (tui.syncImportable(componentId)) {
      return tui.defaultExport(tui.require(componentId));
    } else {
      if (process.env.NODE_ENV !== 'production') {
        const i = componentId.indexOf('/');
        const totaraComp = i === -1 ? componentId : componentId.slice(0, i);
        console.warn(
          `[FlexIcon] Referencing an icon template "${template}" in an ` +
            `unloaded TUI bundle, loading the bundle asynchronously. To ` +
            `improve performance add the Totara component "${totaraComp}" as ` +
            `a "tuidependency" of the requesting component.`
        );
      }
      return asyncIconComponent(componentId);
    }
  } catch (e) {
    if (process.env.NODE_ENV !== 'production' && e.code == 'MODULE_NOT_FOUND') {
      console.warn(
        `[FlexIcon] Could not find a Vue component equivalent for ` +
          `flex icon template "${template}", falling back to ` +
          `rendering the icon using mustache. To improve rendering ` +
          `performance, either update the flex icon "${id}" to use ` +
          `a different template or add a Vue component at ` +
          `"${componentId}".`
      );
      return FlexIconMustacheFallback;
    } else {
      console.error(e);
    }
    return null;
  }
}

/**
 * Get component ID (to be passed to tui.require()) to render the
 * provided icon template.
 *
 * E.g.
 *   - 'core/flex_icon_stack' => 'totara_core/components/icons/flex_icons/FlexIconStack'
 *   - 'theme_foo/custom' => 'theme_foo/components/icons/flex_icons/Custom'
 *
 * @private
 * @param {string} template
 * @returns {?string} null if invalid template name
 */
function getTemplateComponentId(template) {
  const match = /^(.*?)\/(.*)$/.exec(template);
  if (!match) {
    return null;
  }
  let frankenstyle = match[1];
  // original mustache templates for icons were in core, but the new
  // Vue components are in totara_core
  if (frankenstyle == 'core') {
    frankenstyle = 'totara_core';
  }
  // convert 'foo_bar_baz' to 'flex_icons/FooBarBaz'
  const componentName =
    'components/icons/flex_icons/' +
    match[2]
      .replace(/[a-z]+/gi, x => x[0].toUpperCase() + x.slice(1))
      .replace(/_/g, '');
  return frankenstyle + '/' + componentName;
}

export default {
  functional: true,

  props: {
    icon: {
      type: String,
      required: true,
    },
    title: {
      type: String,
      default: undefined,
    },
    alt: {
      type: String,
      default: undefined,
    },
    size: {
      type: [Number, String],
      default: undefined,
    },
    customClass: {
      type: [String, Object, Array],
      default: undefined,
    },
    customData: {
      type: Object,
      default: undefined,
    },
    styleClass: {
      type: Object,
      default: undefined,
    },
  },

  render(h, { props }) {
    const { component, data, id } = getDataCached(props.icon);
    const { alt, customClass, customData, size, styleClass, title } = props;

    return h(component, {
      props: {
        icon: id,
        data,
        title: title !== undefined ? title : alt,
        alt,
        size,
        customClass,
        customData,
        styleClass,
      },
    });
  },
};
</script>

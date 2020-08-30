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

  @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
  @module theme_ventura
-->

<template>
  <div>
    <h2>{{ theme }}</h2>
    <h3>{{ $str('sitebranding', 'theme_ventura') }}</h3>
    <p>{{ $str('sitebrandinginformation', 'theme_ventura') }}</p>
    <ActionLink
      :href="configLink"
      :text="$str('editsitebranding', 'theme_ventura')"
      :styleclass="{
        primary: true,
      }"
    />

    <h3>{{ $str('tenantbranding', 'theme_ventura') }}</h3>
    <Table :data="tenants">
      <template v-slot:header-row>
        <HeaderCell>{{ $str('tenant', 'totara_tenant') }}</HeaderCell>
        <HeaderCell>{{ $str('tenantidnumber', 'totara_tenant') }}</HeaderCell>
        <HeaderCell>{{ $str('branding', 'theme_ventura') }}</HeaderCell>
        <HeaderCell>{{ $str('actions', 'core') }}</HeaderCell>
      </template>
      <template v-slot:row="{ row }">
        <Cell>
          {{ row.name }}
        </Cell>
        <Cell>
          {{ row.idnumber }}
        </Cell>
        <Cell>
          {{
            row.customBranding
              ? $str('custom', 'theme_ventura')
              : $str('site', 'theme_ventura')
          }}
        </Cell>
        <Cell>
          <a :href="tennantLink + row.id">
            <Edit :alt="$str('edittenantsetting', 'theme_ventura', row.name)" />
          </a>
        </Cell>
      </template>
    </Table>
  </div>
</template>

<script>
import ActionLink from 'tui/components/links/ActionLink';
import Table from 'tui/components/datatable/Table';
import HeaderCell from 'tui/components/datatable/HeaderCell';
import Cell from 'tui/components/datatable/Cell';
import Edit from 'tui/components/icons/Edit';

import { config } from 'tui/config';

export default {
  components: {
    ActionLink,
    Table,
    HeaderCell,
    Cell,
    Edit,
  },

  props: {
    theme: {
      type: String,
      required: true,
      validator: v => ['ventura', 'roots', 'basis'].some(t => t === v),
    },
    tenants: {
      type: Array,
      required: false,
      default: () => [],
    },
  },

  data() {
    return {
      configLink: config.wwwroot + '/theme/ventura/theme_settings.php',
      tennantLink:
        config.wwwroot + '/theme/ventura/theme_settings.php?tenant_id=',
    };
  },
};
</script>
<lang-strings>
{
  "core": ["actions"],
  "theme_ventura": [
    "branding",
    "custom",
    "editsitebranding",
    "edittenantsetting",
    "site",
    "sitebrandinginformation",
    "sitebranding",
    "tenantbranding"
  ],
  "totara_tenant": [
    "tenant",
    "tenantidnumber"
  ]
}
</lang-strings>

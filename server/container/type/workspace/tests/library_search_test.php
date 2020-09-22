<?php

use container_workspace\member\member;
use container_workspace\totara_engage\share\recipient\library;

/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_userstatus
 */

class totara_engage_library_search_testcase extends advanced_testcase {

    public function test_search_workspaces() {
        $this->setAdminUser();

        $owner = $this->getDataGenerator()->create_user();
        $member = $this->getDataGenerator()->create_user();

        /** @var container_workspace_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('container_workspace');

        $priv_workspace_member = $generator->create_private_workspace(
            'private workspace member',
            'description',
            null,
            $owner->id
        );
        $priv_workspace_non_member = $generator->create_private_workspace(
            'private workspace w/o member',
            'description',
            null,
            $owner->id
        );

        $pub_workspace_member = $generator->create_workspace(
            'public workspace member',
            'description',
            null,
            $owner->id
        );
        $pub_workspace_non_member = $generator->create_workspace(
            'public workspace w/o member',
            'description',
            null,
            $owner->id
        );

        member::added_to_workspace($priv_workspace_member, $member->id, false);
        member::added_to_workspace($pub_workspace_member, $member->id, false);

        // Admin user is not a member so shouldn't find anything
        $result = library::search('', null);
        $this->assertCount(0, $result);

        $this->setUser($member);

        $result = library::search('', null);
        $this->assertCount(2, $result);

        $recipient_ids = [];
        foreach ($result as $recipient) {
            $recipient_ids[] = $recipient->get_id();
        }

        $this->assertEqualsCanonicalizing([$priv_workspace_member->get_id(), $pub_workspace_member->get_id()], $recipient_ids);
    }

    public function test_search_workspaces_with_multi_tenancy() {
        /** @var totara_tenant_generator $tenant_generator */
        $tenant_generator = $this->getDataGenerator()->get_plugin_generator('totara_tenant');
        $tenant_generator->enable_tenants();

        $tenant1 = $tenant_generator->create_tenant();
        $tenant2 = $tenant_generator->create_tenant();

        $owner1 = $this->getDataGenerator()->create_user(['tenantid' => $tenant1->id]);
        $member1 = $this->getDataGenerator()->create_user(['tenantid' => $tenant1->id]);

        $owner2 = $this->getDataGenerator()->create_user(['tenantid' => $tenant2->id]);
        $member2 = $this->getDataGenerator()->create_user(['tenantid' => $tenant2->id]);

        /** @var container_workspace_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('container_workspace');

        $this->setUser($owner1);

        $priv_workspace_member1 = $generator->create_private_workspace(
            'private workspace member',
            'description',
            null,
            $owner1->id
        );
        $priv_workspace_non_member1 = $generator->create_private_workspace(
            'private workspace w/o member',
            'description',
            null,
            $owner1->id
        );

        $pub_workspace_member1 = $generator->create_workspace(
            'public workspace member',
            'description',
            null,
            $owner1->id
        );
        $pub_workspace_non_member1 = $generator->create_workspace(
            'public workspace w/o member',
            'description',
            null,
            $owner1->id
        );

        member::added_to_workspace($priv_workspace_member1, $member1->id, false);
        member::added_to_workspace($pub_workspace_member1, $member1->id, false);

        $this->setUser($owner2);

        $priv_workspace_member2 = $generator->create_private_workspace(
            'private workspace member',
            'description',
            null,
            $owner2->id
        );
        $priv_workspace_non_member2 = $generator->create_private_workspace(
            'private workspace w/o member',
            'description',
            null,
            $owner2->id
        );

        $pub_workspace_member2 = $generator->create_workspace(
            'public workspace member',
            'description',
            null,
            $owner2->id
        );
        $pub_workspace_non_member2 = $generator->create_workspace(
            'public workspace w/o member',
            'description',
            null,
            $owner2->id
        );

        member::added_to_workspace($priv_workspace_member2, $member2->id, false);
        member::added_to_workspace($pub_workspace_member2, $member2->id, false);

        $this->setUser($member1);

        $result = library::search('', null);
        $this->assertCount(2, $result);

        $recipient_ids = [];
        foreach ($result as $recipient) {
            $recipient_ids[] = $recipient->get_id();
        }

        $this->assertEqualsCanonicalizing([$priv_workspace_member1->get_id(), $pub_workspace_member1->get_id()], $recipient_ids);
    }

}
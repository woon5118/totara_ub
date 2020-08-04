<?php
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Kunle Odusan <kunle.odusan@totaralearning.com>
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use core\orm\entity\model;
use mod_perform\entities\activity\external_participant as external_participant_entity;
use moodle_page;

/**
 * Represents a single external participant.
 *
 * @property-read int $id
 * @property-read string $fullname
 * @property-read string $email
 * @property-read string $token
 * @property-read string $profileimageurlsmall
 * @property-read participant_instance|null $participant_instance
 *
 * @package mod_perform\models\activity
 */
class external_participant extends model {
    protected $entity_attribute_whitelist = [
        'id',
        'email',
        'token'
    ];

    protected $model_accessor_whitelist = [
        'fullname',
        'profileimageurlsmall',
        'participant_instance'
    ];

    /**
     * Default image filename.
     *
     * @var string
     */
    private $image_filename = 'u/f2';

    /**
     * {@inheritdoc}
     */
    protected static function get_entity_class(): string {
        return external_participant_entity::class;
    }

    /**
     * Creates a record for the given external participant name/email. Note:
     * it is possible to have multiple external participants with the same name
     * and email combination. The only way to distinguish between them is by the
     * id and/or token.
     *
     * @param string $fullname external participant name.
     * @param string $email external participant email.
     *
     * @return external_participant the newly created model.
     */
    public static function create(string $fullname, string $email): external_participant {
        $entity = new external_participant_entity();
        $entity->name = $fullname;
        $entity->email = $email;
        $entity->token = self::generate_token($fullname, $email);

        try {
            $entity->save();
        } catch (\dml_exception $exception) {
            // If the token does already exist (rare), we just wait for 1 microsecond
            // and try again as microtime() will return a new time value.
            usleep(1);
            return self::create($fullname, $email);
        }

        return new external_participant($entity);
    }

    /**
     * Generates a unique token hash.
     *
     * @param string $fullname external participant name.
     * @param string $email external participant email.
     *
     * @return string the generated token.
     */
    private static function generate_token(string $fullname, string $email): string {
        $prev_id = external_participant_entity::repository()
            ->select('max(id) as max_id')
            ->get()
            ->first()
            ->max_id;

        // It is possible for 2 independent users to nominate the same external
        // user on the same site within the same microsecond. However it is unlikely
        // the random number generator returns the same value in both cases -
        // especially since the random number is between 0 and at least 32767 on
        // Windows. So collisions are theoretically possible but very improbable.
        return hash(
            'sha256',
            get_site_identifier() . microtime() . $fullname . $email . $prev_id . rand()
        );
    }

    /**
     * Retrieves an external participant by its unique token.
     *
     * @param string $token token to look up.
     *
     * @return external_participant the participant.
     */
    public static function load_by_token(string $token): external_participant {
        $entity = external_participant_entity::repository()
            ->where('token', $token)
            ->one(true);

        return static::load_by_entity($entity);
    }

    /**
     * Get the external participant's name.
     *
     * @return string the name
    */

    public function get_fullname(): string {
        return $this->entity->name;
    }

    /**
     * Get the profile image of an external participant.
     *
     * @return string
    */
    public function get_profileimageurlsmall(): string {
        return $this->get_default_image();
    }

    /**
     * Get associated participant instance if any.
     *
     * @return participant_instance the participant instance. This can be null
     *         if it has not been created yet.
     */
    public function get_participant_instance(): ?participant_instance {
        return $this->entity->participant_instance ?
            participant_instance::load_by_entity($this->entity->participant_instance)
            : null;
    }

    /**
     * Deletes the external participant. Note: after this, the model is invalid.
     */
    public function delete(): void {
        $this->entity->delete();
    }

    /**
     * Get default image string.
     *
     * @return string
     */
    private function get_default_image(): string {
        return (new moodle_page())->get_renderer('core')->image_url($this->image_filename)->out(false);
    }
}

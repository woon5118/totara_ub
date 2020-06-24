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
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\response;

use \Closure;
use \stdClass;

use core\collection;

use mod_perform\models\activity\section;
use mod_perform\models\activity\subject_instance;
use mod_perform\models\activity\participant_instance;

/**
 * Holds participants for a specific section in an activity.
 */
class section_participants {
    /**
     * @var section parent section.
     */
    private $section;

    /**
     * @var collection|participant_instance[] participant instances.
     */
    private $participant_instances;

    /**
     * Formulates sections and their participants for a target activity subject
     * instance.
     *
     * @param subject_instance $subject_instance target subject instance.
     *
     * @return collection|section_participants[] section participant objects.
     */
    public static function create_from_subject_instance(subject_instance $subject_instance): collection {
        return $subject_instance
            ->participant_instances
            ->reduce(
                Closure::fromCallable([self::class, 'create_from_participant_instance']),
                collection::new([])
            )
            ->map(
                function (stdClass $raw): section_participants {
                    return new section_participants($raw->section, $raw->participant_instances);
                }
            );
    }

    /**
     * Formulates sections and their participants for a target activity participant
     * instance.
     *
     * @param collection|stdClass[] $by_sections mapping of section ids to section
     *        and participant details.
     * @param participant_instance $subject_instance target subject instance.
     *
     * @return collection|stdClass[] the updated section mappings.
     */
    private static function create_from_participant_instance(
        collection $by_sections,
        participant_instance $participant_instance
    ): collection {
        foreach ($participant_instance->participant_sections as $participant_section) {
            $section = $participant_section->section;

            $raw = $by_sections->item($section->id) ?? (object) [
                'section' => $section,
                'participant_instances' => collection::new([])
            ];
            $raw->participant_instances->append($participant_instance);

            $by_sections->set($raw, $section->id);
        }

        return $by_sections;
    }

    /**
     *  Default constructor.
     *
     * @param section $section parent section.
     * @param collection $participant_instances set of participant instances.
     */
    public function __construct(section $section, collection $participant_instances) {
        $this->section = $section;
        $this->participant_instances = $participant_instances;
    }

    /**
     * Returns the parent section.
     *
     * @return section the parent section.
     */
    public function get_section(): section {
        return $this->section;
    }

    /**
     * Returns the associated participant instances.
     *
     * @return collection|participant_instance[] participant instances.
     */
    public function get_participant_instances(): collection {
        return $this->participant_instances;
    }
}

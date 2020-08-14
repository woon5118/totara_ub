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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_comment
 */
namespace totara_comment\formatter;

use totara_comment\comment;
use totara_comment\resolver_factory;
use core\webapi\formatter\field\text_field_formatter;
use core\webapi\formatter\formatter;
use totara_comment\formatter\field\time_description_formatter;

/**
 * Class base_formatter
 * @package totara_comment\formatter
 */
abstract class base_formatter extends formatter {
    /**
     * base_formatter constructor.
     * @param comment $comment
     */
    public function __construct(comment $comment) {
        $resolver = resolver_factory::create_resolver($comment->get_component());
        $area = $comment->get_area();

        $context_id = $resolver->get_context_id($comment->get_instanceid(), $area);
        $context = \context::instance_by_id($context_id);

        $record = static::to_record($comment);
        parent::__construct($record, $context);
    }

    /**
     * Extract the comment to be a dummy data object.
     *
     * @param comment $comment
     * @return \stdClass
     */
    protected static function to_record(comment $comment): \stdClass {
        $record = new \stdClass();

        $record->user = $comment->get_user();
        $record->id = $comment->get_id();
        $record->content = $comment->get_content();
        $record->format = $comment->get_format();
        $record->timemodified = $comment->get_timemodified();
        $record->timecreated = $comment->get_timecreated();
        $record->deleted = $comment->is_soft_deleted();
        $record->reasondeleted = $comment->get_reason_deleted();

        $record->component = $comment->get_component();
        $record->area = $comment->get_area();

        $record->comment_area = comment::COMMENT_AREA;
        if ($comment->is_reply()) {
            $record->comment_area = comment::REPLY_AREA;
        }

        $record->edited = (null !== $record->timemodified);
        $record->totalreactions = $comment->get_total_reactions();
        return $record;
    }

    /**
     * @param string $field
     * @return mixed|null
     */
    protected function get_field(string $field) {
        if ('timedescription' == $field) {
            return $this->object->timecreated;
        }

        return parent::get_field($field);
    }

    /**
     * @param string $field
     * @return bool
     */
    protected function has_field(string $field): bool {
        if ('timedescription' == $field) {
            return true;
        }

        return parent::has_field($field);
    }

    /**
     * @return array
     */
    protected function get_map(): array {
        $that = $this;

        return [
            'user' => null,
            'id' => null,
            'updateable' => null,
            'deleteable' => null,
            'edited' => null,
            'deleted' => null,
            'reportable' => null,
            'totalreactions' => null,
            'content' => function(?string $content, text_field_formatter $formatter) use ($that): string {
                if (empty($content) && $that->object->deleted) {
                    $reason = $that->object->reasondeleted;
                    $reported_reasons = [comment::REASON_DELETED_REPORTED, comment::REASON_DELETED_PARENT_REPORTED];

                    // Different phrasing for removed versus user deleted comments
                    if (null !== $reason && in_array($reason, $reported_reasons)) {
                        return get_string('removedcomment', 'totara_comment');
                    }
                    return get_string('deletedcomment', 'totara_comment');
                } else if (null === $content) {
                    debugging("Content is empty, even though the comment was not deleted yet", DEBUG_DEVELOPER);
                    return '';
                }
                $textformat = $that->object->format;

                $formatter->set_additional_options(['formatter' => 'totara_tui']);
                $formatter->set_pluginfile_url_options(
                    $that->context,
                    'totara_comment',
                    $that->object->comment_area,
                    $that->object->id
                );

                $formatter->set_text_format($textformat);
                return $formatter->format($content);
            },

            'timedescription' => time_description_formatter::class,
            'reasondeleted' => null,
        ];
    }
}
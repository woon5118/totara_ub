<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Michael Dunstan <michael.dunstan@androgogic.com>
 * @package contentmarketplace_goone
 */

namespace contentmarketplace_goone;

use totara_contentmarketplace\local;
use totara_contentmarketplace\local\contentmarketplace\search_results;

defined('MOODLE_INTERNAL') || die();

final class search extends \totara_contentmarketplace\local\contentmarketplace\search {

    // Max API limit for search results is 50. However 48 happens to be a better fit for a grid of search results.
    // 48 divides by 4, 3, and 2 so then a full page of results will always finishes with a complete row.
    const SEARCH_PAGE_SIZE = 48;

    /**
     * List sorting options available for the search results.
     *
     * @return string[]
     */
    public function sort_options(): array {
        $options = array(
            'created:desc',
            'relevance',
            'popularity',
            'price',
            'price:desc',
            'title',
        );
        return $options;
    }

    /**
     * @param string $keyword
     * @param string $sort
     * @param array $filter
     * @param int $page
     * @param bool $isfirstquerywithdefaultsort
     * @param string $mode
     * @param \context $context
     * @return search_results
     */
    public function query(string $keyword, string $sort, array $filter, int $page, bool $isfirstquerywithdefaultsort, string $mode, \context $context): search_results {
        $api = new api();
        $hits = array();
        $filterid = 0;

        if ($isfirstquerywithdefaultsort) {
            $sort = 'relevance';
        }

        $params = array(
            "keyword" => $keyword,
            "sort" => $sort,
            "offset" => $page * self::SEARCH_PAGE_SIZE,
            "limit" => self::SEARCH_PAGE_SIZE,
            "facets" => "tag,language,instance",
        );
        foreach (array("tags", "language", "provider") as $name) {
            if (key_exists($name, $filter)) {
                $params[$name] = $filter[$name];
            }
        }
        $availability_selection = $this->availability_selection($filter, $mode, $context);
        $params += $this->availability_query($availability_selection);

        $response = $api->get_learning_objects($params);
        foreach ($response->hits as $hit) {

            $delivery = array();
            if ($hit->delivery->duration > 0) {
                $title = self::duration($hit);
                $delivery[] = array("title" => $title);
            }
            if ($hit->delivery->mode) {
                $title = $hit->delivery->mode;
                $delivery[] = array("title" => $title);
            }
            if (!empty($delivery)) {
                $delivery[count($delivery) - 1]["last"] = true;
            }

            $hits[] = array(
                "id" => $hit->id,
                "title" => $hit->title,
                "selectlabel" => get_string('selectcontent', 'contentmarketplace_goone', $hit->title),
                "image" => $hit->image,
                "provider" => array(
                    "name" => $hit->provider->name,
                ),
                "delivery" => $delivery,
                "delivery_has_items" => !empty($delivery),
                "price" => self::price($hit),
                "is_in_collection" => $hit->portal_collection,
            );
        }

        $results = new search_results();
        $results->hits = $hits;

        $results->filters = array();

        $availability_filter = $this->availability_filter($availability_selection, $params, $context);
        if (isset($availability_filter)) {
            $results->filters[] = $availability_filter;
        }

        $tags = array();
        $nonzerofilters = array();
        foreach ($response->facets->tag->buckets as $bucket) {
            $checked = (key_exists("tags", $filter) and in_array($bucket->key, $filter["tags"]));
            if ($checked) {
                $nonzerofilters[] = $bucket->key;
            }
            $tags[] = array(
                "htmlid" => 'tag' . $filterid++,
                "value" => $bucket->key,
                "label" => $bucket->key,
                "formatcount" => local::format_integer($bucket->doc_count),
                "count" => $bucket->doc_count,
                "checked" => $checked,
            );
        }
        if (key_exists("tags", $filter)) {
            foreach ($filter["tags"] as $value) {
                if (!in_array($value, $nonzerofilters)) {
                    $tags[] = array(
                        "htmlid" => 'tag' . $filterid++,
                        "value" => $value,
                        "label" => $value,
                        "formatcount" => local::format_integer(0),
                        "count" => 0,
                        "checked" => true,
                    );
                }
            }
        }
        $tag_filter = array(
            "name" => "tags",
            "label" => get_string('filter:tags', 'contentmarketplace_goone'),
            "template" => "totara_contentmarketplace/filter_checkboxes",
            "paginated_options" => self::paginate(self::sort($tags)),
        );
        $results->filters[] = $tag_filter;

        $providers = array();
        $nonzerofilters = array();
        $filterid = 0;
        foreach ($response->facets->instance->buckets as $bucket) {
            $checked = (key_exists("provider", $filter) and in_array($bucket->key, $filter["provider"]));
            if ($checked) {
                $nonzerofilters[] = $bucket->key;
            }
            $providers[] = array(
                "htmlid" => 'provider' . $filterid++,
                "value" => $bucket->key,
                "label" => $bucket->name,
                "formatcount" => local::format_integer($bucket->doc_count),
                "count" => $bucket->doc_count,
                "checked" => $checked,
            );
        }
        if (key_exists("provider", $filter)) {
            foreach ($filter["provider"] as $value) {
                if (!in_array($value, $nonzerofilters)) {
                    $providers[] = array(
                        "htmlid" => 'provider' . $filterid++,
                        "value" => $value,
                        "label" => $value,
                        "formatcount" => local::format_integer(0),
                        "count" => 0,
                        "checked" => true,
                    );
                }
            }
        }
        $provider_filter = array(
            "name" => "provider",
            "label" => get_string('filter:provider', 'contentmarketplace_goone'),
            "template" => "totara_contentmarketplace/filter_checkboxes",
            "paginated_options" => self::paginate(self::sort($providers)),
        );
        $results->filters[] = $provider_filter;

        $languages = array();
        $nonzerofilters = array();
        $stringmanager = new string_manager();
        $filterid = 0;
        foreach ($response->facets->language->buckets as $bucket) {
            $checked = (key_exists("language", $filter) and in_array($bucket->key, $filter["language"]));
            if ($checked) {
                $nonzerofilters[] = $bucket->key;
            }
            $label = $stringmanager->get_language($bucket->key);
            $languages[] = array(
                "htmlid" => 'language' . $filterid++,
                "value" => $bucket->key,
                "label" => $label,
                "formatcount" => local::format_integer($bucket->doc_count),
                "count" => $bucket->doc_count,
                "checked" => $checked,
            );
        }
        if (key_exists("language", $filter)) {
            foreach ($filter["language"] as $value) {
                if (!in_array($value, $nonzerofilters)) {
                    $label = $stringmanager->get_language($value);
                    $languages[] = array(
                        "htmlid" => 'language' . $filterid++,
                        "value" => $value,
                        "label" => $label,
                        "formatcount" => local::format_integer(0),
                        "count" => 0,
                        "checked" => true,
                    );
                }
            }
        }
        $language_filter = array(
            "name" => "language",
            "label" => get_string('filter:language', 'contentmarketplace_goone'),
            "template" => "totara_contentmarketplace/filter_checkboxes",
            "paginated_options" => self::paginate(self::sort($languages)),
        );
        $results->filters[] = $language_filter;

        $results->total = $response->total;

        $results->more = $response->total > ($page + 1) * self::SEARCH_PAGE_SIZE;
        $results->sort = $sort;

        if (!empty($params['collection'])) {
            $results->selectionmode = 'remove';
        } else {
            $results->selectionmode = 'add';
        }

        return $results;
    }

    /**
     * @param string $selection
     * @param array $params
     * @param \context $context
     * @return array|null
     */
    public function availability_filter($selection, array $params, \context $context) {
        $api = new api();

        $options = [];
        $availablityoptions = contentmarketplace::content_availability_options($context);
        if (in_array('all', $availablityoptions)) {
            $options[] = [
                "value" => "all",
                "label" => get_string("availability-filter:all", "contentmarketplace_goone"),
                "formatcount" => local::format_integer($api->get_learning_objects_total_count($params)),
                "checked" => $selection === "all",
                "htmlid" => "all",
            ];
        }

        if (in_array('subscribed', $availablityoptions)) {
            $options[] = [
                "value" => "subscribed",
                "label" => get_string("availability-filter:subscription", "contentmarketplace_goone"),
                "formatcount" => local::format_integer($api->get_learning_objects_subscribed_count($params)),
                "checked" => $selection === "subscribed",
                "htmlid" => "subscribed",
            ];
        }

        if (in_array('collection', $availablityoptions)) {
            $options[] = [
                "value" => "collection",
                "label" => get_string("availability-filter:collection", "contentmarketplace_goone"),
                "formatcount" => local::format_integer($api->get_learning_objects_collection_count($params)),
                "checked" => $selection === "collection",
                "htmlid" => "collection",
            ];
        }

        if (!empty($options)) {
            $filter = array(
                "name" => "availability",
                "label" => get_string('filter:availability', 'contentmarketplace_goone'),
                "template" => "totara_contentmarketplace/filter_radios",
                "options" => $options,
            );
            return $filter;
        } else {
            return null;
        }
    }

    /**
     * @param array $filter
     * @param string $mode
     * @param \context $context
     * @return null|string
     */
    public function availability_selection(array $filter, string $mode, \context $context) {
        if (key_exists("availability", $filter)) {
            $selection = $filter["availability"];
            if (!in_array($selection, array("all", "subscribed", "collection"))) {
                $selection = null;
            }
        } else if ($mode == 'explore-collection') {
            $selection = 'collection';
        } else {
            $selection = null;
        }

        if (has_capability('totara/contentmarketplace:config', $context)) {
            if (!isset($selection)) {
                $selection = "all";
            }
        } else if (has_capability('totara/contentmarketplace:add', $context)) {
            if (!isset($selection)) {
                $selection = "all";
            }
            $contentsettingscreators = get_config('contentmarketplace_goone', 'content_settings_creators');
            switch ($contentsettingscreators) {
                case "subscribed":
                    if ($selection === "all") {
                        $selection = "subscribed";
                    }
                    break;
                case "collection":
                    $selection = "collection";
                    break;
            }
        } else {
            $selection = null;
        }

        return $selection;
    }

    /**
     * @param string $selection
     * @return array
     */
    public function availability_query($selection) {
        switch ($selection) {
            case 'subscribed':
                $query = ["subscribed" => "true"];
                break;
            case 'collection':
                $query = ["collection" => "default"];
                break;
            default:
                $query = [];
        }
        return $query;
    }

    /**
     * @param string $query
     * @param array $filter
     * @param string $mode
     * @param \context $context
     * @return array
     */
    public function select_all($query, array $filter, string $mode, \context $context) {
        $params = array(
            "keyword" => $query,
        );
        foreach (array("tags", "language", "provider") as $name) {
            if (key_exists($name, $filter)) {
                $params[$name] = $filter[$name];
            }
        }
        $availability_selection = $this->availability_selection($filter, $mode, $context);
        $params += $this->availability_query($availability_selection);

        $api = new api();
        return $api->list_ids_for_all_learning_objects($params);
    }

    /**
     * @param \stdClass $course
     * @return string
     */
    public static function price($course) {
        if (!is_null($course->subscription->licenses) and ($course->subscription->licenses === -1 or $course->subscription->licenses > 0)) {
            return get_string('price:included', 'contentmarketplace_goone');
        }
        if ($course->pricing->price === 0) {
            return get_string('price:free', 'contentmarketplace_goone');
        }
        if (empty($course->pricing->price) || empty($course->pricing->currency)) {
            return '';
        }
        $price = local::format_money($course->pricing->price, $course->pricing->currency);
        if (!$course->pricing->tax_included and $course->pricing->tax > 0) {
            $a = new \stdClass();
            $a->baseprice = $price;
            $a->tax = $course->pricing->tax;
            return get_string('pricewithtax', 'contentmarketplace_goone', $a);
        } else {
            return $price;
        }
    }

    /**
     * @param \stdClass $course
     * @return string
     */
    public static function duration($course) {
        if (empty($course->delivery) || empty($course->delivery->duration)) {
            return '';
        }
        return get_string('duration', 'contentmarketplace_goone', $course->delivery->duration);
    }

    /**
     * @param int $id
     * @return \stdClass|null
     */
    public function get_details(int $id) {
        try {
            $api = new api();
            $learningobject = $api->get_learning_object($id);
        } catch (\Exception $ex) {
            debugging($ex->getMessage(), DEBUG_DEVELOPER);
            return null;
        }

        return $learningobject;
    }
}

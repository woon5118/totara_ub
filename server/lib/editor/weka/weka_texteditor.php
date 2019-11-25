<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package editor_weka
 */
use editor_weka\config\config_item;
use editor_weka\config\factory;
use editor_weka\extension\extension;
use editor_weka\extension\link;
use editor_weka\extension\text;
use editor_weka\extension\ruler;
use editor_weka\extension\emoji;
use editor_weka\extension\hashtag;
use editor_weka\extension\list_extension;
use editor_weka\extension\mention;

final class weka_texteditor extends texteditor {
    /**
     * The context's id where this editor is used.
     * @var int
     */
    private $contextid;

    /**
     * @var factory
     */
    private $factory;

    /**
     * weka_texteditor constructor.
     * @param factory|null $factory
     */
    public function __construct(?factory $factory = null) {
        if (null === $factory) {
            $factory = new factory();
        }

        $this->contextid = null;
        $this->factory = $factory;
    }

    /**
     * @param int $value
     * @return void
     */
    public function set_contextid(int $value): void {
        $this->contextid = $value;
    }

    /**
     * @return int|null
     */
    public function get_contextid(): ?int {
        return $this->contextid;
    }

    /**
     * {@inheritdoc}
     */
    public function supported_by_browser() {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get_supported_formats() {
        return array(FORMAT_JSON_EDITOR => FORMAT_JSON_EDITOR);
    }

    /**
     * {@inheritdoc}
     */
    public function get_preferred_format() {
        return FORMAT_JSON_EDITOR;
    }

    /**
     * {@inheritdoc}
     */
    public function supports_repositories() {
        return true;
    }

    /**
     * @param string|null $component
     * @param string|null $area
     *
     * @return config_item|null
     */
    protected function get_config(?string $component = null, ?string $area = null): ?config_item {
        $config = null;

        if (null !== $component && '' !== $component) {
            // Component is being given, we should be able to get the config item, unless the configuration
            // was not set.
            if (null === $area || '' === $area) {
                $area = config_item::AREA_DEFAULT;
            }

            $config = $this->factory->get_configuration($component, $area);

            if (null === $config) {
                // Still no config found, we will have to debug here.
                debugging(
                    "The configuration for editor within component '{$component}' " .
                    "at area '{$area}' does not exist. Please setup your configuration properly.",
                    DEBUG_DEVELOPER
                );
            }
        }

        return $config;
    }

    /**
     * This function will try to invoke {@see extension::__construct}
     *
     * @param string|null $component
     * @param string|null $area
     *
     * @return extension[]
     */
    public function get_extensions(string $component = 'editor_weka', string $area = 'learn'): array {
        $config = $this->get_config($component, $area);

        // Default to have link, text and ruler only. The component that wants to use the
        // editor will need to define the extensions that they want to use.
        $classes = [link::class, text::class, ruler::class];

        if (null !== $config) {
            $classes = array_merge(
                $classes,
                $config->get_extensions()
            );
        } else {
            $classes = array_merge($classes, [
                emoji::class,
                hashtag::class,
                list_extension::class,
                mention::class,
            ]);
        }

        $classes = array_unique($classes);
        $extensions = [];

        foreach ($classes as $cls) {
            /** @var extension $extension */
            $extension = new $cls(
                $component,
                $area,
                $this->contextid
            );

            if (null !== $config) {
                $options = $config->get_options_for_extension($cls);
                $extension->set_options($options);
            }

            $extensions[] = $extension;
        }

        return $extensions;
    }

    /**
     * @param string|null $component
     * @param string|null $area
     *
     * @return bool
     */
    public function show_toolbar(?string $component = null, ?string $area = null): bool {
        // Default to true most of the time.
        $result = true;
        $cfg = $this->get_config($component, $area);

        if (null != $cfg) {
            $result = $cfg->show_toolbar();
        }

        return $result;
    }

    /**
     * @inheritDoc
     * @param string     $elementid
     * @param array|null $options
     * @param array|null $fpoptions
     *
     * @return void
     */
    public function use_editor($elementid, array $options = null, $fpoptions = null): void {
        global $PAGE;

        if (null === $this->contextid) {
            if (isset($options['context'])) {
                $context = $options['context'];
                $this->set_contextid($context->id);
            }
        }

        if (null === $options) {
            // Set options to empty array. So that it is safe to set props down this code.
            $options = [];
        }

        // Start finding the draft_item_id within file picker options.
        if (is_array($fpoptions)) {
            $draft_item_id = null;
            if (isset($fpoptions['image'])) {
                $image_option = $fpoptions['image'];
                $draft_item_id = $image_option->itemid;
            } else if (isset($fpoptions['media'])) {
                $media_option = $fpoptions['media'];
                $draft_item_id = $media_option->itemid;
            } else if (isset($fpoptions['link'])) {
                $link_option = $fpoptions['link'];
                $draft_item_id = $link_option->itemid;
            } else if (isset($fpoptions['subtitle'])) {
                $subtitle_option = $fpoptions['subtitle'];
                $draft_item_id = $subtitle_option->itemid;
            }

            if (!empty($draft_item_id)) {
                $options['item_id'] = $draft_item_id;
            }
        }

        $params = $this->prepare_editor_options($elementid, $options);
        $jscode = $this->get_js_import_code($params);

        $PAGE->requires->js_init_code($jscode);
    }

    /**
     * Only returning files - without the directories.
     * @param string $component
     * @param string $area
     * @param int $item_id
     *
     * @return stored_file[]
     */
    public function get_files(string $component, string $area, int $item_id): array {
        global $CFG;

        if (empty($this->contextid)) {
            // There is no chances to fetch the file without context.
            return [];
        }

        require_once("{$CFG->dirroot}/lib/filelib.php");
        $fs = get_file_storage();


        $files = $fs->get_area_files(
            $this->contextid,
            $component,
            $area,
            $item_id
        );

        if (empty($files)) {
            return [];
        }

        return array_filter(
            $files,
            function (\stored_file $file): bool {
                return !$file->is_directory();
            }
        );
    }

    /**
     * @param string     $elementid
     * @param array|null $options
     *
     * @return array
     */
    private function prepare_editor_options(string $elementid, ?array $options = null): array {
        $params = [
            'id' => (string)$elementid,
            'extensions' => [],

            // Always shows the toolbar.
            'showtoolbar' => true,
            'file_item_id' => $options['item_id'] ?? null,
            'context_id' => $this->contextid ?? context_system::instance()->id
        ];

        $component = $options['component'] ?? 'editor_weka';
        $area = $options['area'] ?? 'learn';

        // Build up the extensions metadata for the editor. Note that the extension metadata has to match with the
        // type declared in the schema.graphqls for the editor's extension type.
        $extensions = $this->get_extensions($component, $area);

        foreach ($extensions as $extension) {
            $opt = $extension->get_js_parameters();
            $json = null;
            if (!empty($opt)) {
                $json = json_encode($opt);

                if (JSON_ERROR_NONE !== json_last_error()) {
                    $message = json_last_error_msg();
                    debugging("Error when encoding the json: {$message}");

                    // Reset to null if there is any error.
                    $json = null;
                }
            }

            $params['extensions'][] = [
                'name' => $extension->get_extension_name(),
                'tuicomponent' => $extension->get_js_path(),

                // It is an json_encoded string, as same as the property declared for
                // editor's extension type in graphql
                'options' => $json
            ];
        }

        return $params;
    }

    /**
     * @param array $params
     * @return string
     */
    private function get_js_import_code(array $params): string {
        $component = new \totara_tui\output\component('editor_weka/pages/WekaIntegration', [
            'params' => $params
        ]);
        $html_encoded = json_encode($component->out_html());
        $id_encoded = json_encode($params['id']);

        if (JSON_ERROR_NONE != json_last_error()) {
            throw new \coding_exception("Cannot encode JSON parameters");
        }

        return /** @lang javascript */"
            ;(function(){
                var textarea = document.getElementById({$id_encoded});
                textarea.style.display = 'none';
                var temp = document.createElement('div');
                temp.innerHTML = {$html_encoded};
                textarea.parentNode.insertBefore(temp.firstElementChild, textarea);
                document.dispatchEvent(new CustomEvent('nodes-updated', {
                    detail: { nodes: [temp.firstElementChild], }
                }));
            })();
        ";
    }
}

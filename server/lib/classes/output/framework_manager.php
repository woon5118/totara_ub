<?php

namespace core\output;

interface framework_manager {

    public static function new_instance(): framework_manager;

    public function initialise(): void;
    public function inject_css_urls(array &$urls);
    public function inject_js_urls(array &$urls, bool $initialiseamd);

    public function hook_get_head_code(\moodle_page $page, \core_renderer $renderer);

}
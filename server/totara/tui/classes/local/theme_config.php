<?php

namespace totara_tui\local;

final class theme extends \theme_config {

    /**
     * Get CSS content for type and subtype. Called by styles.php.
     *
     * @param string $type
     * @return string
     */
    public function get_css_content_by($type) {
        $csscontent = $this->get_tui_css_content($type);
        $csscontent = $this->post_process($csscontent);
        $csscontent = \core_minify::css($csscontent);
        return $csscontent;
    }

    /**
     * Get the compiled TUI CSS content for the provided Totara component
     *
     * @param string $component
     * @return string Compiled CSS
     * @throws \Exception if SCSS is invalid
     */
    private function get_tui_css_content($component) {
        $scss_options = new \totara_tui\local\scss\scss_options();
        $scss_options->set_themes($this->get_tui_theme_chain());
        $scss_options->set_legacy($this->legacybrowser);
        $scss_options->set_sourcemap_enabled(false);

        $tui_scss = new \totara_tui\local\scss\scss($scss_options);
        $result = $tui_scss->get_compiled_css($component);

        return $result;
    }

    /**
     * Get theme chain (e.g. ['base', 'roots', 'basis']) for TUI CSS.
     *
     * Themes are only included if they have `$THEME->tui = true` in config.php.
     *
     * @return string[]
     */
    private function get_tui_theme_chain() {
        $themes = [];

        // Find out wanted parent sheets.
        $excludes = $this->resolve_excludes('parents_exclude_sheets');
        if ($excludes !== true) {
            // Base first, the immediate parent last.
            foreach (array_reverse($this->parent_configs) as $parent_config) {
                $parent = $parent_config->name;
                if (!empty($excludes[$parent]) and $excludes[$parent] === true) {
                    continue;
                }
                $themes[] = $parent;
            }
        }

        $themes[] = $this->name;

        return $themes;
    }

}
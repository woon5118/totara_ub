<?php

namespace totara_tui\output;

trait render_component {

    /**
     * Render a TUI component.
     *
     * If this method is being called after the header has been sent, you should
     * also call `$PAGE->requires->tui_bundle('...')` before the header is sent
     * with the name of the component to ensure required bundles are sent with
     * the page.
     * If you do not call that method, the bundle may fall back to being loaded
     * at runtime by JS instead, which is a little slower.
     *
     * @param component $component Component to render.
     * @return string HTML output.
     */
    final protected function render_component(component $component): string {
        $component->register($this->page);

        $attributes = [
            'data-tui-component' => $component->get_name(),
        ];
        if ($component->has_props()) {
            $attributes['data-tui-props'] = $component->get_props_encoded();
        }

        array_walk($attributes, function (&$value, $key) {
            // TL-22100: use htmlspecialchars() rather than s() as s() will unencode some double encoded HTML entities, resulting
            // in prop injection and potential XSS. This is not a standard approach, you should be using s() normally.
            $value = htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
        });

        return '<span ' . join(' ', $attributes) . '></span>';
    }
}

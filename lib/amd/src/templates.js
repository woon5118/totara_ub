// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Template renderer for Moodle. Load and render Moodle templates with Mustache.
 *
 * @module     core/templates
 * @package    core
 * @class      templates
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
define([ 'core/mustache',
         'jquery',
         'core/ajax',
         'core/str',
         'core/notification',
         'core/url',
         'core/config',
         'core/localstorage',
         'core/event'
       ],
       function(mustache, $, ajax, str, notification, coreurl, config, storage, event) {

    // Private variables and functions.

    /** @var {string[]} templateCache - Cache of already loaded templates */
    var templateCache = {};

   /** @var {string[]} flexIconCache - Cache of already loaded flex icon maps */
    var flexIconCache = {};

    /** @var {string[]} requiredStrings - Collection of strings found during the rendering of one template */
    var requiredStrings = [];

    /** @var {string[]} requiredJS - Collection of js blocks found during the rendering of one template */
    var requiredJS = [];

    /** @var {Number} uniqid Incrementing value that is changed for every call to render */
    var uniqid = 1;

    /** @var {String} themeName for the current render */
    var currentThemeName = '';

    /**
     * Create a legacy flex icon identifier from pix icon path and component.
     *
     * TOTARA flex_icon API.
     *
     * @param {String} pixpath
     * @param {String} component
     * @return {String}
     */
    var legacyIdentifierFromPixData = function(pixpath, component) {

        // Normalise the component to 'core' where required.
        if (typeof component === 'undefined' || !component || component == '' || component === 'moodle' || component === 'core') {
            component = 'core';
        }

        return component + '-' + pixpath;

    };

    /**
     * Render image icons.
     *
     * @method pixHelper
     * @private
     * @param {string} sectionText The text to parse arguments from.
     * @param {function} helper Used to render the alt attribute of the text.
     * @return {string}
     */
    var pixHelper = function(sectionText, helper) {
        var parts = sectionText.split(',');
        var key = '';
        var component = '';
        var text = '';
        var result;

        if (parts.length > 0) {
            key = parts.shift().trim();
        }
        if (parts.length > 0) {
            component = parts.shift().trim();
        }
        if (parts.length > 0) {
            text = parts.join(',').trim();
        }

        // TOTARA check if we should use a flex icon.
        var flexIdentifier = legacyIdentifierFromPixData(key, component);
        var useFlexIcons = flexIconShouldReplacePixIcon(config.theme, flexIdentifier);
        if (useFlexIcons === true) {
            return flexIconHelper(flexIdentifier, partialHelper);
        }

        var url = coreurl.imageUrl(key, component);

        var templatecontext = {
            attributes: [
                { name: 'src', value: url},
                { name: 'alt', value: helper(text)},
                { name: 'class', value: 'smallicon'}
            ]
        };
        // We forced loading of this early, so it will be in the cache.
        var template = templateCache[currentThemeName + '/core/pix_icon'];
        result = mustache.render(template, templatecontext, partialHelper);
        return result.trim();
    };

    /**
     * Return whether a pix icon should be replaced with a flex icon.
     *
     * TOTARA flex_icon API.
     *
     * @param {String} identifier
     * @param {Object} iconsCache
     * @return {Boolean}
     */
    var flexIconResolveTemplate = function(identifier, iconsCache) {

        if (typeof iconsCache.map[identifier] !== 'undefined') {
            if (typeof iconsCache.map[identifier].template !== 'undefined') {
                return iconsCache.map[identifier].template;
            }
        }

        if (typeof iconsCache.defaults !== 'undefined') {
            if (typeof iconsCache.defaults.template !== 'undefined') {
                return iconsCache.defaults.template;
            }
        }

        var err = new Error('core/templates: Template not defined for icon "' + identifier + '"');
        notification.exception(err);

    };

    /**
     * Return whether a pix icon should be replaced with a flex icon.
     *
     * TOTARA flex_icon API.
     *
     * @param {String} themeName
     * @param {String} identifier
     * @return {Boolean}
     */
    var flexIconShouldReplacePixIcon = function(themeName, identifier) {

        if (isLegacyIdentifier(identifier) === false) {
            return false;
        }

        if (flexIconHasMapData(themeName, identifier) === false) {
            return false;
        }

        return true;

    };

    /**
     * Does a given identifier have icon data in a given theme.
     *
     * TOTARA flex_icon API.
     *
     * @param {String} identifier
     * @return {Boolean}
     */
    var isLegacyIdentifier = function(identifier) {

        return identifier.match(/^[a-zA-Z]+(_[a-zA-Z]+)*-(\w+\/)*\w+$/) !== null;

    };

    /**
     * Does a given identifier have icon data in a given theme.
     *
     * TOTARA flex_icon API.
     *
     * @param {String} themeName
     * @param {String} identifier
     * @return {Boolean}
     */
    var flexIconHasMapData = function(themeName, identifier) {

        flexIconGetCache(false).done(function(iconsCache) {
            return typeof flexIconResolveTemplate(identifier, iconsCache) !== 'undefined';
        }).fail(notification.exception);

    };

    /**
     * Build data for an icon based on given identifier and given cache object.
     *
     * TOTARA flex_icon API.
     *
     * @param {String} identifier
     * @param {Object} iconsCache
     * @return {Object}
     */
    var flexIconResolveData = function(identifier, iconsCache) {

        var data = {};

        // TODO review this - is it still required? Could it help with cache file size?
        if (typeof iconsCache.defaults !== 'undefined') {
            if (typeof iconsCache.defaults.data !== 'undefined') {
                $.extend(data, iconsCache.defaults.data);
            }
        }

        if (typeof iconsCache.map[identifier] !== 'undefined') {
            if (typeof iconsCache.map[identifier].data !== 'undefined') {
                $.extend(data, iconsCache.map[identifier].data);
            }
        }

        return data;

    };

   /**
    * Render flexible icons.
    *
    * @method flexIconHelper
    * @private
    * @param {String} sectionText
    * @param {Function} partialHelper Used to render partials.
    * @return {String}
    */
    var flexIconHelper = function(sectionText, partialHelper) {

        var params = sectionText.split(',').map(function(fragment) {
            return fragment.trim();
        });

        var identifier = params[0];
        var customdata = {};
        if (typeof params[1] !== 'undefined') {
            customdata = JSON.parse(params[1]);
        }

        var compiledFlexIcon = '';

        // TODO this is not async mirroring the way upstream code works.
        flexIconGetCache(false).done(function(iconsCache) {

            var templateName = flexIconResolveTemplate(identifier, iconsCache);
            var templateContext = $.extend(flexIconResolveData(identifier, iconsCache), { "customdata": customdata });

            getTemplate(templateName, false).done(function(templateSource) {
                compiledFlexIcon = mustache.render(templateSource, templateContext, partialHelper);
            }).fail(notification.exception);

        }).fail(notification.exception);

        return compiledFlexIcon.trim();

    };

    /**
     * Load a partial from the cache or ajax.
     *
     * @method partialHelper
     * @private
     * @param {string} name The partial name to load.
     * @return {string}
     */
    var partialHelper = function(name) {
        var template = '';

        getTemplate(name, false).done(
            function(source) {
                template = source;
            }
        ).fail(notification.exception);

        return template;
    };

    /**
     * Render blocks of javascript and save them in an array.
     *
     * @method jsHelper
     * @private
     * @param {string} sectionText The text to save as a js block.
     * @param {function} helper Used to render the block.
     * @return {string}
     */
    var jsHelper = function(sectionText, helper) {
        requiredJS.push(helper(sectionText, this));
        return '';
    };

    /**
     * String helper used to render {{#str}}abd component { a : 'fish'}{{/str}}
     * into a get_string call.
     *
     * @method stringHelper
     * @private
     * @param {string} sectionText The text to parse the arguments from.
     * @param {function} helper Used to render subsections of the text.
     * @return {string}
     */
    var stringHelper = function(sectionText, helper) {
        var parts = sectionText.split(',');
        var key = '';
        var component = '';
        var param = '';
        if (parts.length > 0) {
            key = parts.shift().trim();
        }
        if (parts.length > 0) {
            component = parts.shift().trim();
        }
        if (parts.length > 0) {
            param = parts.join(',').trim();
        }

        if (param !== '') {
            // Allow variable expansion in the param part only.
            param = helper(param, this);
        }
        // Allow json formatted $a arguments.
        if ((param.indexOf('{') === 0) && (param.indexOf('{{') !== 0)) {
            param = JSON.parse(param);
        }

        var index = requiredStrings.length;
        requiredStrings.push({key: key, component: component, param: param});
        return '{{_s' + index + '}}';
    };

    /**
     * Quote helper used to wrap content in quotes, and escape all quotes present in the content.
     *
     * @method quoteHelper
     * @private
     * @param {string} sectionText The text to parse the arguments from.
     * @param {function} helper Used to render subsections of the text.
     * @return {string}
     */
    var quoteHelper = function(sectionText, helper) {
        var content = helper(sectionText.trim(), this);

        // Escape the {{ and the ".
        // This involves wrapping {{, and }} in change delimeter tags.
        content = content
            .replace('"', '\\"')
            .replace(/([\{\}]{2,3})/g, '{{=<% %>=}}$1<%={{ }}=%>')
            ;
        return '"' + content + '"';
    };

    /**
     * Add some common helper functions to all context objects passed to templates.
     * These helpers match exactly the helpers available in php.
     *
     * @method addHelpers
     * @private
     * @param {Object} context Simple types used as the context for the template.
     * @param {String} themeName We set this multiple times, because there are async calls.
     */
    var addHelpers = function(context, themeName) {
        currentThemeName = themeName;
        requiredStrings = [];
        requiredJS = [];
        context.uniqid = uniqid++;
        context.str = function() { return stringHelper; };
        context.pix = function() { return pixHelper; };
        context.flex_icon = function() { return flexIconHelper; };
        context.js = function() { return jsHelper; };
        context.quote = function() { return quoteHelper; };
        context.globals = { config : config };
        context.currentTheme = themeName;
    };

    /**
     * Get all the JS blocks from the last rendered template.
     *
     * @method getJS
     * @private
     * @param {string[]} strings Replacement strings.
     * @return {string}
     */
    var getJS = function(strings) {
        var js = '';
        if (requiredJS.length > 0) {
            js = requiredJS.join(";\n");
        }

        var i = 0;

        for (i = 0; i < strings.length; i++) {
            js = js.replace('{{_s' + i + '}}', strings[i]);
        }
        // Re-render to get the final strings.
        return js;
    };

    /**
     * Render a template and then call the callback with the result.
     *
     * @method doRender
     * @private
     * @param {string} templateSource The mustache template to render.
     * @param {Object} context Simple types used as the context for the template.
     * @param {String} themeName Name of the current theme.
     * @return {Promise} object
     */
    var doRender = function(templateSource, context, themeName) {
        var deferred = $.Deferred();

        currentThemeName = themeName;

        // Make sure we fetch this first.
        var loadPixTemplate = getTemplate('core/pix_icon', true);

        loadPixTemplate.done(
            function() {
                addHelpers(context, themeName);
                var result = '';
                try {
                    result = mustache.render(templateSource, context, partialHelper);
                } catch (ex) {
                    deferred.reject(ex);
                }

                if (requiredStrings.length > 0) {
                    str.get_strings(requiredStrings).done(
                        function(strings) {
                            var i;

                            // Why do we not do another call the render here?
                            //
                            // Because that would expose DOS holes. E.g.
                            // I create an assignment called "{{fish" which
                            // would get inserted in the template in the first pass
                            // and cause the template to die on the second pass (unbalanced).
                            for (i = 0; i < strings.length; i++) {
                                result = result.replace('{{_s' + i + '}}', strings[i]);
                            }
                            deferred.resolve(result.trim(), getJS(strings));
                        }
                    ).fail(
                        function(ex) {
                            deferred.reject(ex);
                        }
                    );
                } else {
                    deferred.resolve(result.trim(), getJS([]));
                }
            }
        ).fail(
            function(ex) {
                deferred.reject(ex);
            }
        );
        return deferred.promise();
    };

    /**
     * Load a template from the cache or local storage or ajax request.
     *
     * @method getTemplate
     * @private
     * @param {string} templateName - should consist of the component and the name of the template like this:
     *                              core/menu (lib/templates/menu.mustache) or
     *                              tool_bananas/yellow (admin/tool/bananas/templates/yellow.mustache)
     * @return {Promise} JQuery promise object resolved when the template has been fetched.
     */
    var getTemplate = function(templateName, async) {
        var deferred = $.Deferred();
        var parts = templateName.split('/');
        var component = parts.shift();
        var name = parts.shift();

        var searchKey = currentThemeName + '/' + templateName;

        // First try request variables.
        if (searchKey in templateCache) {
            deferred.resolve(templateCache[searchKey]);
            return deferred.promise();
        }

        // Now try local storage.
        var cached = storage.get('core_template/' + searchKey);

        if (cached) {
            deferred.resolve(cached);
            templateCache[searchKey] = cached;
            return deferred.promise();
        }

        // Oh well - load via ajax.
        var promises = ajax.call([{
            methodname: 'core_output_load_template',
            args:{
                component: component,
                template: name,
                themename: currentThemeName
            }
        }], async, false);

        promises[0].done(
            function (templateSource) {
                storage.set('core_template/' + searchKey, templateSource);
                templateCache[searchKey] = templateSource;
                deferred.resolve(templateSource);
            }
        ).fail(
            function (ex) {
                deferred.reject(ex);
            }
        );
        return deferred.promise();
    };

    /**
     * Loads the flex icon cache.
     *
     * @param {Boolean} async
     * @returns {Promise}
     */
    var flexIconGetCache = function(async) {

        var deferred = $.Deferred();

        var searchKey = currentThemeName;

        // Static cache.
        if (searchKey in flexIconCache) {
            deferred.resolve(flexIconCache[searchKey]);
            return deferred.promise();
        }

        // Local storage.
        var cached = JSON.parse(storage.get('core_flex_icon/' + searchKey));

        if (cached) {
            deferred.resolve(cached);
            flexIconCache[searchKey] = cached;
            return deferred.promise();
        }

        // Retrieve from back-end.
        var promises = ajax.call([{
            methodname: 'core_output_load_flex_icons_cache',
            args: {
                themename: currentThemeName
            }
        }], async, false);

        promises[0].done(
            function (iconsCacheSource) {
                storage.set('core_flex_icon/' + searchKey, JSON.stringify(iconsCacheSource));
                flexIconCache[searchKey] = iconsCacheSource;
                deferred.resolve(iconsCacheSource);
            }
        ).fail(
            function (err) {
                deferred.reject(err);
            }
        );

        return deferred.promise();

    };

    /**
     * Execute a block of JS returned from a template.
     * Call this AFTER adding the template HTML into the DOM so the nodes can be found.
     *
     * @method runTemplateJS
     * @param {string} source - A block of javascript.
     */
    var runTemplateJS = function(source) {
        if (source.trim() !== '') {
            var newscript = $('<script>').attr('type','text/javascript').html(source);
            $('head').append(newscript);
        }
    };

    /**
     * Do some DOM replacement and trigger correct events and fire javascript.
     *
     * @method domReplace
     * @private
     * @param {JQuery} element - Element or selector to replace.
     * @param {String} newHTML - HTML to insert / replace.
     * @param {String} newJS - Javascript to run after the insertion.
     * @param {Boolean} replaceChildNodes - Replace only the childnodes, alternative is to replace the entire node.
     */
    var domReplace = function(element, newHTML, newJS, replaceChildNodes) {
        var replaceNode = $(element);
        if (replaceNode.length) {
            // First create the dom nodes so we have a reference to them.
            var newNodes = $(newHTML);
            // Do the replacement in the page.
            if (replaceChildNodes) {
                replaceNode.empty();
                replaceNode.append(newNodes);
            } else {
                replaceNode.replaceWith(newNodes);
            }
            // Run any javascript associated with the new HTML.
            runTemplateJS(newJS);
            // Notify all filters about the new content.
            event.notifyFilterContentUpdated(newNodes);
        }
    };


    return /** @alias module:core/templates */ {
        // Public variables and functions.
        /**
         * Load a template and call doRender on it.
         *
         * @method render
         * @private
         * @param {string} templateName - should consist of the component and the name of the template like this:
         *                              core/menu (lib/templates/menu.mustache) or
         *                              tool_bananas/yellow (admin/tool/bananas/templates/yellow.mustache)
         * @param {Object} context - Could be array, string or simple value for the context of the template.
         * @param {string} themeName - Name of the current theme.
         * @return {Promise} JQuery promise object resolved when the template has been rendered.
         */
        render: function(templateName, context, themeName) {
            var deferred = $.Deferred();

            if (typeof (themeName) === "undefined") {
                // System context by default.
                themeName = config.theme;
            }

            currentThemeName = themeName;

            var loadTemplate = getTemplate(templateName, true);

            loadTemplate.done(
                function(templateSource) {
                    var renderPromise = doRender(templateSource, context, themeName);

                    renderPromise.done(
                        function(result, js) {
                            deferred.resolve(result, js);
                        }
                    ).fail(
                        function(ex) {
                            deferred.reject(ex);
                        }
                    );
                }
            ).fail(
                function(ex) {
                    deferred.reject(ex);
                }
            );
            return deferred.promise();
        },

        /**
         * Execute a block of JS returned from a template.
         * Call this AFTER adding the template HTML into the DOM so the nodes can be found.
         *
         * @method runTemplateJS
         * @param {string} source - A block of javascript.
         */
        runTemplateJS: runTemplateJS,

        /**
         * Replace a node in the page with some new HTML and run the JS.
         *
         * @method replaceNodeContents
         * @param {string} source - A block of javascript.
         */
        replaceNodeContents: function(element, newHTML, newJS) {
            return domReplace(element, newHTML, newJS, true);
        },

        /**
         * Insert a node in the page with some new HTML and run the JS.
         *
         * @method replaceNode
         * @param {string} source - A block of javascript.
         */
        replaceNode: function(element, newHTML, newJS) {
            return domReplace(element, newHTML, newJS, false);
        },

        /**
         * Replaces the
         *
         * @param {JQuery} element
         * @param {String} icon
         * @param {String} component
         * @param {String} alt
         */
        replacePix: function(element, icon, component, alt) {
            var sectionText = icon + ',' + component + ',' + alt;
            var html = pixHelper(sectionText, function (text) {
                return text;
            });
            return domReplace(element, html, '', false);
        },

        /**
         * Renders a flex icon and returns
         *
         * @param {String} iconName
         * @param {String} alt
         * @param {String} cssclasses
         * @returns {Promise}
         */
        renderFlexIcon: function (iconName, alt, cssclasses) {
            var templates = this;
            var iconhtml = $.Deferred();
            flexIconGetCache(true).done(function (cache) {
                var templatedata = {};
                $.extend(templatedata, cache.defaults, cache.map[iconName]);
                if (templatedata.data === undefined) {
                    templatedata.data = {};
                } else {
                    templatedata.data.customdata = {
                        alt: alt
                    };
                }

                if (cssclasses !== undefined && cssclasses !== '') {
                    templatedata.data.customdata.classes = cssclasses;
                }

                templates.render(templatedata.template, templatedata.data).done(function (html) {
                    iconhtml.resolve(html);
                });
            });

            return iconhtml.promise();
        }
    };
});

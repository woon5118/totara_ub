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
         'core/event',
         'core/flex_icon'
       ],
       function(mustache, $, ajax, str, notification, coreurl, config, storage, event, flexicon) {

    // Private variables and functions.

    /** @var {string[]} templateCache - Cache of already loaded templates */
    var templateCache = {};

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
     * Render a template and then call the callback with the result.
     *
     * @method doRender
     * @private
     * @param {string} templateSource The mustache template to render.
     * @param {Object} context Simple types used as the context for the template.
     * @param {String} themeName ignored!
     * @return {Promise} object
     */
    var doRender = function(templateSource, context, themeName) {
        var deferred = $.Deferred(),
            js = [],
            promises = [],
            result = '',
            requiredStrings = [];

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
         * Render flexible icons.
         *
         * @method flexIconHelper
         * @private
         * @param {String} sectionText
         * @return {String}
         */
        var flexIconHelper = function(sectionText) {
            var identifier = '';
            var data = '';
            var promise = $.Deferred();
            var customdata = {};

            var params = sectionText.split(',').map(function(fragment) {
                return fragment.trim();
            });
            if (params.length > 0) {
                identifier = params.shift().trim();
            }
            if (params.length > 0) {
                data = params.join(',').trim();
            }

            if (data !== '') {
                customdata = JSON.parse(data);
            }

            var alt = '';
            var classes = '';
            var index = promises.length;

            if (typeof customdata.alt !== 'undefined') {
                alt = customdata.alt;
            }
            if (customdata.classes) {
                classes = customdata.cssclasses;
            }

            if (alt.indexOf('{{#str}}') !== -1) {
                alt = alt.replace('{{#str}}', '');
                alt = alt.replace('{{/str}}', '');

                var stringparams = alt.split(',').map(function(fragment) {
                    return fragment.trim();
                });

                str.get_string(stringparams[0], stringparams[1], stringparams[2]).done(function (string) {
                    renderIcon(identifier, string, classes).done(promise.resolve);
                });
            } else {
                promise = renderIcon(identifier, alt, classes);
            }

            promises.push(promise);

            return '{{_f' + index + '}}';
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

            if (parts.length > 0) {
                key = parts.shift().trim();
            }
            if (parts.length > 0) {
                component = parts.shift().trim();
            }
            if (parts.length > 0) {
                text = parts.join(',').trim();
            }

            var flexstring = component + '|' + key + ',' + '{"alt":"' + text + '"}';

            return flexIconHelper.apply(this, [flexstring, helper]);
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
            js.push(helper(sectionText, this));
            return '';
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

        context.str = function() { return stringHelper; };
        context.pix = function() { return pixHelper; };
        context.flex_icon = function() { return flexIconHelper; };
        context.js = function() { return jsHelper; };
        context.quote = function() { return quoteHelper; };
        context.globals = { config : config };

        var complete = [];
        try {
            result = mustache.render(templateSource, context, partialHelper);
        } catch (ex) {
            deferred.reject(ex);
        }

        // Load the flex icons template.
        for (var i = 0; i < promises.length; i++) {
            // A closure is needed here otherwise i is highly likely to be promises.length
            promises[i].done((function (index) {return function (html) {
                result = result.replace('{{_f' + index + '}}', html);
            };}) (i));
        }

        complete.push($.when.apply($, promises));

        if (requiredStrings.length > 0) {
            var stringspromise = str.get_strings(requiredStrings);
            complete.push(stringspromise);
            stringspromise.done(
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
                }
            );
        }

        if (complete.length > 0) {
            js = js.join(';\n');

            $.when.apply($, complete).done(function () {
                deferred.resolve(result.trim(), js);
            }).fail(function(ex) {
                deferred.reject(ex);
            });
        } else {
            deferred.resolve(result.trim(), js);
        }
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

        var searchKey = config.theme + '/' + templateName;

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
                themename: config.theme
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

    /**
     * Render an icon. All icons should be passed through here.
     *
     * @method renderIcon
     * @public
     * @param {String} iconName - The icon to render. This can either be in the traditional Moodle format or a flex icon name.
     * @param {String} alt - The string for screen readers to read (either as an alt tag, or as hidden text).
     * @param {String} cssclasses - any additional CSS classes that are needed for the icon.
     * @return {String} An HTML string containing the rendered icon.
     */
    var renderIcon = function (iconName, alt, cssclasses) {
        var iconhtml = $.Deferred();
        flexicon.getFlexTemplateData(iconName).done(function (completetemplate) {
            if (completetemplate.data === undefined) {
                completetemplate.data = {};
            } else {
                completetemplate.data.customdata = {alt: alt};
            }

            if (cssclasses !== undefined && cssclasses !== '') {
                completetemplate.data.customdata.classes = cssclasses;
            }

            templates.render(completetemplate.template, completetemplate.data)
                .done(function (html) {iconhtml.resolve(html);});
        }).fail(function () {
            // Fallback to the tradditional icon
            var parts = iconName.split('|');

            if (parts.length === 1) {
                parts.unshift('core');
            }

            var url = coreurl.imageUrl(parts[1], parts[0]);
            var attributes = [
                { name: 'src', value: url},
                { name: 'alt', value: alt},
                { name: 'class', value: 'smallicon ' + cssclasses}
            ];

            templates.render('core/pix_icon', {attributes:attributes}).done(iconhtml.resolve);
        });

        return iconhtml.promise();
    };


    var templates = /** @alias module:core/templates */ {
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
         * @param {string} themeName - ignored!
         * @return {Promise} JQuery promise object resolved when the template has been rendered.
         */
        render: function(templateName, context, themename) {
            var deferred = $.Deferred();

            var loadTemplate = getTemplate(templateName, true);

            loadTemplate.done(
                function(templateSource) {
                    var renderPromise = doRender(templateSource, context);

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

            return deferred;
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

        renderIcon: renderIcon
    };

    return templates;
});

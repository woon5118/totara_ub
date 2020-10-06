/**
 * This file is part of Totara LMS
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
 * @author Brian Barnes <brian.barnes@totaralearning.com>
 * @package block_current_learning
 */
define(['core/templates', 'core/str',], function(templates, mdlstr) {

    /** @type {HTMLDivElement} */
    var blockNode;

    /** @type {HTMLDivElement} */
    var tilesNode;

    /** @type {HTMLDivElement} */
    var pagesNode;

    /** @type {object} */
    var pagination;

    /** @type {Array} */
    var learningItems;

    /** @type {number} */
    var totalPages;

    /**
     * Initialises functionality inside the current learning block
     *
     * @param {Object} context an object containing all the data for the current learning block
     */
    function init(context) {
        // No JS functionality is required if there are no current learning items
        if (context.learningitems.length === 0) {
            return;
        }
        blockNode = document.getElementById('inst' + context.instanceid);
        pagesNode = blockNode.querySelector('.panel-footer');
        tilesNode = blockNode.querySelector('.block_current_learning-tiles ul');
        pagination = context.pagination;
        pagination.pagination = true;
        totalPages = Math.ceil(pagination.totalitems / pagination.itemsperpage);
        learningItems = context.learningitems;

        addEvents();
    }

    /**
     * Adds events to the current learning block
     */
    function addEvents() {
        blockNode.addEventListener('click', changePage);
    }

    /**
     * Changes the page that the current user is looking at
     *
     * @param {Event} e The event that triggers this call
     */
    function changePage(e) {
        /** @type {HTMLAnchorElement} */
        var target = e.target.closest('a');

        if (!target || !target.hasAttribute('data-page')) {
            return;
        }
        e.preventDefault();

        if (target.parentElement.classList.contains('disabled')) {
            return;
        }

        var page = target.getAttribute('data-page');
        if (page === 'next') {
            pagination.currentpage += 1;
        } else if (page === 'prev') {
            pagination.currentpage -= 1;
        } else {
            pagination.currentpage = parseInt(page);
        }

        render();
    }

    /**
     * Re-renders the block
     */
    function render() {
        pagination.previousclass = pagination.currentpage === 1 ? 'disabled' : '';
        pagination.nextclass = pagination.currentpage === totalPages ? 'disabled' : '';

        var start = (pagination.currentpage - 1) * pagination.itemsperpage;
        var end = Math.min((pagination.currentpage * pagination.itemsperpage), pagination.totalitems);
        var tileData = learningItems.slice(start, end);
        var strData = {
            end: end,
            start: start + 1,
            total: learningItems.length
        };

        pagination.pages.forEach(function(p) {
            if (p.page == pagination.currentpage) {
                p.active = 'active';
            } else {
                p.active = '';
            }
        });

        M.util.js_pending('block_current_learning-updated');
        /** @type {Promise} */
        var pagingComplete = mdlstr.get_string('displayingxofx', 'block_current_learning', strData).then(function(string) {
            pagination.text = string;
            return templates.render('block_current_learning/paging', pagination);
        }).then(function(html, js) {
            blockNode.querySelector('.panel-footer').outerHTML = html;
            return templates.runTemplateJS(js);
        });

        var tiles = tileData.map(function (tile) {
            return templates.render('block_current_learning/tile', tile);
        });

        var tilesUpdated = Promise.all(tiles).then(function (tilesHTML) {
            tilesNode.innerHTML = tilesHTML.join('');
        });

        return Promise.all([pagingComplete, tilesUpdated]).then(function() {
            M.util.js_complete('block_current_learning-updated');
        });
    }

    return {
        init: init
    };
});
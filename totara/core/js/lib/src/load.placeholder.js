/*
 * Add event handlers to all text field and textareas with a placeholder attribute set
 */

/* eslint-disable no-undef */

$(document).ready(function() {
    $('input[placeholder], textarea[placeholder]').placeholder();
});

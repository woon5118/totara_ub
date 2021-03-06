/**
 * Push badges to backpack.
 * @deprecated since Moodle 3.7
 */
function addtobackpack(event, args) {
    var badgetable = Y.one('#issued-badge-table');
    var errordiv = Y.one('#addtobackpack-error');
    var errortext = M.util.get_string('error:backpackproblem', 'badges');
    var errorhtml = '<div id="addtobackpack-error" class="box boxaligncenter notifyproblem">' + errortext + '</div>';

    if (typeof OpenBadges !== 'undefined') {
        OpenBadges.issue([args.assertion], function(errors, successes) { });
    } else {
        // Add error div if it doesn't exist yet.
        if (!errordiv) {
            var badgerror = Y.Node.create(errorhtml);
            badgetable.insert(badgerror, 'before');
        }
    }
}

/**
 * Check if website is externally accessible from the backpack.
 * @deprecated since Moodle 3.7
 */
function check_site_access() {
    // TL-18131 Added js_pending checks to better locations
    var callback = {
        method: "GET",
        on: {
            success: function(id, o) {
                var data = Y.JSON.parse(o.responseText);
                if (data.code == 'http-unreachable') {
                    require(['core/templates'], function(templateLib) {
                        var context = {message: data.response};
                        templateLib.render('core/notification_warning', context).done(function(html) {
                            document.getElementById('maincontent').insertAdjacentHTML('afterend', html);
                            M.util.js_complete('badge/backpack::check_site_access');
                        });
                    });
                } else {
                    M.util.js_complete('badge/backpack::check_site_access');
                }
            },
            failure: function() {
                M.util.js_complete('badge/backpack::check_site_access');
            }
        }
    };

    M.util.js_pending('badge/backpack::check_site_access');
    Y.use('io-base', function(Y) {
        Y.io('ajax.php', callback);
    });

    return false;
}

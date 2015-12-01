<?php
defined('MOODLE_INTERNAL') || die();

$strsearch = get_string('search');
$strshowall = get_string('showall', 'moodle', '');
$strsearchresults = get_string('searchresults');
?>
<form id="assignform" method="post" action="<?php echo $PAGE->url; ?>">
<div>
<input type="hidden" name="sesskey" value="<?php p(sesskey()) ?>" />
<?php
if (!empty($error)) {
    echo "<div class=\"notifyproblem\">$error</div>";
}
$idx = 0; // Iterator to put elements on their positions when adding/removing.
?>
<div class="row-fluid user-multiselect" >
    <div class="span5">
        <label for="removeselect"><?php echo $strusertochange ?></label>
        <select name="removeselect[]" size="20" id="removeselect" multiple="multiple">
        <?php
            if (!empty($userstoadd)) {
                foreach ($userstoadd as $newuser) {
                    $fullname = fullname($newuser, true);

                    if ($session->datetimeknown && ($newuser->statuscode > MDL_F2F_STATUS_BOOKED)) {
                        echo "<option value=\"$newuser->id\">".$fullname." (".
                            get_string('status_'.$MDL_F2F_STATUS[$newuser->statuscode], 'facetoface')."), ".$newuser->email."</option>\n";
                    } else {
                        echo "<option value=\"$newuser->id\">".$fullname.", ".$newuser->email."</option>\n";
                    }
                }
            }
        ?>
        </select>
        <label for="searchtoremovetext" class="accesshide"><?php p($strsearch) ?></label>
        <input type="text" name="searchtoremovetext" id="searchtoremovetext" size="20" placeholder="<?php p($strsearch) ?>" value=""/>
        <button name="searchtoremovereset" id="searchtoremovereset" class="search noshow"><?php p($strshowall) ?></button>
    </div>
    <div class="span2 controls addremove">
        <button name="add" id="add"><?php echo $OUTPUT->larrow().'&nbsp;'.get_string('add'); ?></button>
        <button name="remove" id="remove"><?php echo $OUTPUT->rarrow().'&nbsp;'.get_string('remove'); ?></button>
    </div>
    <div class="span5">
        <label for="addselect"><?php echo $stravailableusers ?></label>
        <select name="addselect[]" size="20" id="addselect" multiple="multiple">
        <?php
            if (!empty($searchtext)) {
                if ($usercount > MAX_USERS_PER_PAGE) {
                    $serchcount = new stdClass();
                    $serchcount->count = $usercount;
                    $serchcount->search = s($searchtext);
                    echo '<optgroup label="'.get_string('toomanyusersmatchsearch', 'moodle', $serchcount).'"><option></option></optgroup>'."\n"
                        .'<optgroup label="'.get_string('pleasesearchmore').'"><option></option></optgroup>'."\n";
                } else {
                    if (is_array($availableusers) || $availableusers->valid()) {
                        echo "<optgroup label=\"$strsearchresults (" . $usercount . ")\">\n";
                        foreach ($availableusers as $user) {
                            $idx++;
                            $fullname = fullname($user, true);
                            if ($session->datetimeknown && ($user->statuscode == MDL_F2F_STATUS_WAITLISTED)) {
                                echo "<option data-idx=\"$idx\" value=\"$user->id\">".$fullname." (".
                                    get_string('status_'.$MDL_F2F_STATUS[$user->statuscode], 'facetoface')."), ".$user->email."</option>\n";
                            } else {
                                echo "<option data-idx=\"$idx\" value=\"$user->id\">".$fullname.", ".$user->email."</option>\n";
                            }
                        }
                    } else {
                        echo '<optgroup label="'.get_string('nomatchingusers', 'moodle', s($searchtext)).'"><option></option></optgroup>'."\n"
                            .'<optgroup label="'.get_string('pleasesearchmore').'"><option></option></optgroup>'."\n";
                    }
                    if (is_object($availableusers)) {
                        $availableusers->close();
                    }
                }
                echo "</optgroup>\n";
            } else {
                if ($usercount > MAX_USERS_PER_PAGE) {
                    echo '<optgroup label="'.get_string('toomanytoshow').'"><option></option></optgroup>'."\n"
                          .'<optgroup label="'.get_string('trysearching').'"><option></option></optgroup>'."\n";
                } else {
                    if (is_array($availableusers) || $availableusers->valid()) {
                        foreach ($availableusers as $user) {
                            $idx++;
                            $fullname = fullname($user, true);
                            if ($session->datetimeknown && ($user->statuscode == MDL_F2F_STATUS_WAITLISTED)) {
                                echo "<option data-idx=\"$idx\" value=\"$user->id\">".$fullname." (".
                                get_string('status_'.$MDL_F2F_STATUS[$user->statuscode], 'facetoface')."), ".$user->email."</option>\n";
                            } else {
                                echo "<option data-idx=\"$idx\" value=\"$user->id\">".$fullname.", ".$user->email."</option>\n";
                            }
                        }
                    } else {
                        echo '<optgroup label="'.get_string('nousersfound').'"><option></option></optgroup>';
                    }
                    if (is_object($availableusers)) {
                        $availableusers->close();
                    }
                }
           }
         ?>
        </select>
        <label for="searchtext" class="accesshide"><?php p($strsearch) ?></label>
        <input type="text" name="searchtext" id="searchtext" size="20" value="<?php p($searchtext) ?>"/>
        <input name="search" id="search" type="submit" class="search" value="<?php p($strsearch) ?>"/>
        <?php if (!empty($searchtext)) { ?>
        <input name="clearsearch" id="clearsearch" class="search" type="submit" value="<?php echo $strshowall ?>"/>
        <?php } ?>
        <?php
            $strinterested = get_string('declareinterestfiltercheckbox', 'mod_facetoface');
            $attrchecked = $interested ? 'checked="checked"' : '';
        ?>
        <?php if (empty($nointerestsearch)) { ?>
        <br/>
        <input name="interested" id="interested" type="checkbox" value="1" <?php echo $attrchecked;?>/>
        <label for="interested"><?php echo $strinterested; ?></label>
        <?php } ?>
    </div>
</div>
</div>
    <input name="next" id="next" type="submit" value="<?php echo get_string('continue'); ?>"/>
    <input name="cancel" id="cancel" type="submit" value="<?php echo get_string('cancel'); ?>"/>
</form>

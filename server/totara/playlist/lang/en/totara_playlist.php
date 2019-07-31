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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_playlist
 */

defined('MOODLE_INTERNAL') || die();

$string['accesssettings'] = 'Settings';
$string['adddescription'] = "Add a description (optional)";
$string['addrating'] = "Add your rating";
$string['back_button'] = "{\$a}";
$string['by_author'] = "by {\$a}";
$string['cachedef_catalog_visibility'] = 'Visibility of playlist items in the catalog';
$string['cannotviewplaylist'] = "Cannot view the playlist";
$string['contribute'] = "Create new playlist";
$string['contributeplaylist'] = "Contribute playlist";
$string['createplaylistshort'] = "Create Playlist";
$string['creator'] = "Playlist creator";
$string['defaultlabel'] = "Playlist";
$string['deletewarningmsg'] = 'This action is permanent. People with access to this playlist will no longer be able to view it. This action will NOT delete the playlist contents.';
$string['deletewarningtitle'] = 'Are you sure you want to delete the playlist?';
$string['edit_playlist_title'] = 'Edit playlist title';
$string['entertitle'] = "Enter playlist title";
$string['mentionbody:comment'] = '<strong>{$a->fullname}</strong> has commented on the playlist {$a->title}.';
$string['mentionbody:playlist'] = '<strong>{$a->fullname}</strong> has mentioned you in the playlist {$a->title}.';
$string['mentiontitle:playlist'] = '{$a} has mentioned you in a playlist';
$string['mentionview:playlist'] = 'View playlist';
$string['playlistcreated'] = "Playlist created";
$string['playlistdescription'] = "Playlist description";
$string['playlistdeleted'] = "Playlist deleted";
$string['playlists'] = 'Playlists';
$string['playlistreshared'] = "Playlist re-shared";
$string['playlistshared'] = "Playlist shared";
$string['playlisttitle'] = 'Playlist title';
$string['pluginname'] = "Playlist";
$string['rating'] = 'rating';
$string['ratings'] = 'ratings';
$string['savedplaylists'] = "Saved playlists";
$string['selectcontent'] = "Select content to add into playlist";
$string['selectexistingresource'] = "select an existing resource";
$string['tagarea_playlist'] = "Playlist";
$string['userdataitemplaylist'] = "Playlist";
$string['playlist_resource'] = 'Playlist resources';
$string['toaddplaylist'] = 'to add in this playlist';
$string['yourplaylists'] = 'Your playlists';
$string['resourceplaylistposition'] = '{$a->current} of {$a->total} resources';

// Error strings
$string['error:access'] = "Cannot access to the playlist";
$string['error:addresource'] = "Cannot add a resource to the playlist";
$string['error:create'] = 'Cannot create playlist';
$string['error:update'] = "Cannot update the playlist";
$string['error:sharecapability'] = 'You do not have the required capabilities to share this playlist.';
$string['error:shareprivate'] = 'Playlist is viewable by only you. Change who can view this playlist in order to share it.';
$string['error:sharerestricted'] = 'Playlist is not viewable by everyone and only the owner is allowed to share it.';
$string['error:updateaccess'] = "Cannot update access of the playlist";

// Field strings
$string['field:name'] = 'Name';
$string['field:resourcenames'] = 'Resource Name(s)';
$string['field:summary'] = 'Summary';
$string['field:topics'] = 'Topics';

// Capability strings
$string['playlist:create'] = 'Create playlist';
$string['playlist:delete'] = 'Remove playlist';
$string['playlist:share'] = 'Share playlist';
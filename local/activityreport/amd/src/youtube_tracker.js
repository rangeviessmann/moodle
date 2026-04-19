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
 * YouTube play tracker for course pages.
 *
 * Listens for clicks on VideoJS play buttons (big play button + control bar
 * play button). On first click per player, sends a log entry to Moodle via
 * AJAX. No dependency on the VideoJS JavaScript API.
 *
 * @module     local_activityreport/youtube_tracker
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/config', 'core/log'], function(Config, Log) {
    'use strict';

    /** Player element IDs already logged (prevent duplicate entries). */
    var logged = {};

    /**
     * Extract YouTube watch URL from a .video-js element's data-setup-lazy attribute.
     *
     * @param {Element} vjsEl  The .video-js div element.
     * @returns {string}
     */
    function getVideoUrl(vjsEl) {
        if (!vjsEl) {
            return '';
        }
        var raw = vjsEl.getAttribute('data-setup-lazy') || '';
        if (!raw) {
            return '';
        }
        try {
            var config = JSON.parse(raw);
            if (config.sources && config.sources.length > 0) {
                return config.sources[0].src || '';
            }
        } catch (e) {
            // Ignore JSON parse errors.
        }
        return '';
    }

    /**
     * Walk up the DOM from a .video-js element to find the enclosing Moodle
     * activity section and return its course module ID.
     * Moodle renders activities as <li id="module-{cmid}" ...>.
     *
     * @param {Element} vjsEl  The .video-js div element.
     * @returns {string}  The cmid as a string, or '' if not found.
     */
    function getActivityCmid(vjsEl) {
        var el = vjsEl;
        while (el) {
            if (el.id && /^module-\d+$/.test(el.id)) {
                return el.id.replace('module-', '');
            }
            el = el.parentElement;
        }
        return '';
    }

    /**
     * Send a log entry to Moodle via AJAX.
     *
     * @param {number} courseid
     * @param {string} videourl
     * @param {string} cmid
     */
    function logPlay(courseid, videourl, cmid) {
        var formData = new FormData();
        formData.append('courseid', courseid);
        formData.append('videourl', videourl);
        formData.append('pagetitle', document.title);
        formData.append('cmid', cmid || '');
        formData.append('sesskey', Config.sesskey);

        fetch(Config.wwwroot + '/local/activityreport/track_youtube.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        }).then(function(resp) {
            return resp.json();
        }).then(function(data) {
            Log.debug('YouTube tracker: server response', data);
        }).catch(function(err) {
            Log.debug('YouTube tracker: AJAX error', err);
        });
    }

    /**
     * Handle a click on any play button. Finds the parent .video-js element,
     * extracts the video URL and logs the play event (once per player).
     *
     * @param {Event}  e
     * @param {number} courseid
     */
    function onPlayClick(e, courseid) {
        // Walk up to the nearest .video-js ancestor.
        var el = e.target;
        while (el && !el.classList.contains('video-js')) {
            el = el.parentElement;
        }
        if (!el) {
            return;
        }

        var playerId = el.id || el.getAttribute('data-playerid') || '';
        if (!playerId) {
            // Assign a stable key based on DOM position.
            playerId = 'vjs_' + Array.prototype.indexOf.call(
                document.querySelectorAll('.video-js'),
                el
            );
        }

        if (logged[playerId]) {
            return;
        }
        logged[playerId] = true;

        var videourl = getVideoUrl(el);
        var cmid = getActivityCmid(el);
        Log.debug('YouTube tracker: play clicked, player=' + playerId + ' url=' + videourl + ' cmid=' + cmid);
        logPlay(courseid, videourl, cmid);
    }

    /**
     * Main initialisation — called by Moodle AMD loader.
     *
     * @param {number} courseid  Moodle course ID for this page.
     */
    function init(courseid) {
        Log.debug('YouTube tracker: init for course ' + courseid);

        // Use event delegation on document so it works even if players are
        // rendered after this script runs (lazy initialisation).
        document.addEventListener('click', function(e) {
            var target = e.target;
            if (!target) {
                return;
            }

            // Match big play button, play control button, or their children.
            var isPlayBtn = false;
            var el = target;
            for (var i = 0; i < 4; i++) {
                if (!el) {
                    break;
                }
                if (el.classList &&
                    (el.classList.contains('vjs-big-play-button') ||
                     el.classList.contains('vjs-play-control'))) {
                    isPlayBtn = true;
                    break;
                }
                el = el.parentElement;
            }

            if (isPlayBtn) {
                onPlayClick(e, courseid);
            }
        }, true); // Use capture phase so we catch it before VideoJS stops propagation.
    }

    return {
        init: init
    };
});

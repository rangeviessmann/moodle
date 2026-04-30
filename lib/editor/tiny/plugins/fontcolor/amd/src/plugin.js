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
 * Tiny FontColor plugin for Moodle.
 *
 * Adds TinyMCE's built-in forecolor (text colour) and backcolor (background
 * colour) buttons to the editor toolbar and restores them in the Format menu
 * (Moodle removes them by default in editor_tiny/editor).
 *
 * This module exports an array in the format expected by editor_tiny/editor:
 *   [tinymcePluginName, { configure }]
 * Setting the plugin name to null means no extra TinyMCE plugin needs to be
 * registered — forecolor/backcolor are built-in TinyMCE 6 buttons.
 *
 * @module     tiny_fontcolor/plugin
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Modify the TinyMCE instance configuration to add colour buttons.
 *
 * Called by editor_tiny/editor after all other configuration steps,
 * including Moodle's own removal of forecolor/backcolor from the Format menu.
 *
 * @param {Object} instanceConfig  The current TinyMCE init configuration.
 * @returns {Object} Partial config to be shallow-merged into instanceConfig.
 */
const configure = (instanceConfig) => {
    // Clone the toolbar array and append a new "fontcolor" section.
    const toolbar = [...(instanceConfig.toolbar || [])];
    toolbar.push({
        name: 'fontcolor',
        items: ['forecolor', 'backcolor'],
    });

    const result = {toolbar};

    // Re-add forecolor/backcolor to the Format menu.
    // Moodle strips them in editor.js; configure() runs after that cleanup,
    // so we can safely restore them here.
    if (instanceConfig.menu && instanceConfig.menu.format && instanceConfig.menu.format.items) {
        const items = instanceConfig.menu.format.items;
        if (items.indexOf('forecolor') === -1) {
            result.menu = {
                ...instanceConfig.menu,
                format: {
                    ...instanceConfig.menu.format,
                    items: items.replace('| language', '| forecolor backcolor | language'),
                },
            };
        }
    }

    return result;
};

// Export a resolved Promise so the AMD factory returns the value explicitly,
// matching the pattern all other Moodle tiny plugins use.
// null = no extra TinyMCE plugin to load (forecolor/backcolor are built-in).
export default Promise.resolve([null, {configure}]);

<?php
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
 * Tiny FontColor plugin info.
 *
 * @package    tiny_fontcolor
 * @copyright  2026 Custom Development
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_fontcolor;

use context;
use editor_tiny\editor;
use editor_tiny\plugin;

/**
 * Plugin info class for tiny_fontcolor.
 *
 * Enables TinyMCE's built-in forecolor/backcolor buttons globally.
 * The actual toolbar injection is done in the AMD configure() function.
 */
class plugininfo extends plugin {

    /**
     * Always enabled — no capability required.
     *
     * @param context      $context
     * @param array        $options
     * @param array        $fpoptions
     * @param editor|null  $editor
     * @return bool
     */
    public static function is_enabled(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): bool {
        return true;
    }
}

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
 * Main login page.
 *
 * @package    local
 * @subpackage educ
 * @copyright  2024 Universitat Jaume I https://www.uji.es/
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Adds the loginusingvisitoraccount title to the login page. We add it
 * using javascript to avoid redefining the whole login page using an
 * alternate login page.
 *
 * The thing is tested with behat.
 *
 * @return string
 * @throws coding_exception
 */
function local_educ_before_standard_html_head()
{
    global $PAGE;

    if (!$PAGE->pagetype || $PAGE->pagetype != 'login-index') {
        return '';
    }

    $title = get_string('loginusingvisitoraccount', 'local_educ');
    $title = json_encode($title);

    return <<<_EOF
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', (e) => {
    try {
        let parent = document.getElementsByClassName("loginform")[0];
        let loginform = document.getElementById('login');
        if (!loginform) {
            return;
        }
        
        let title = document.createElement('h2');
        title.classList.add('login-heading');
        title.textContent = $title;
        parent.insertBefore(title, loginform);
    } catch (e) {
    }
});
</script>
_EOF;

}

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

namespace local_educ\local\hook;

use core\hook\output\before_standard_head_html_generation;

/**
 * Class hook_manager
 *
 * @package    local_educ
 * @copyright  2025 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_manager {

    /**
     * Adds the loginusingvisitoraccount title to the login page. We add it
     * using javascript to avoid redefining the whole login page using an
     * alternate login page.
     *
     * @param before_standard_head_html_generation $hook
     * @return string
     */
    public static function before_standard_head_html_generation (before_standard_head_html_generation $hook): void {

        global $PAGE;

        if (during_initial_install()) {
            return;
        }
        if (!get_config('local_educ', 'version')) {
            return;
        }

        if (!$PAGE->pagetype || $PAGE->pagetype != 'login-index') {
            return;
        }

        $title = get_string('loginusingvisitoraccount', 'local_educ');
        $title = json_encode($title);

        echo <<<_EOF
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', (e) => {

        try {            
            let loginform = document.getElementById('login');
            if (!loginform) {
                return;
            }

            loginform.classList.add("hidden");        

            let a = document.createElement("a");
            a.textContent = $title;
            a.setAttribute("href", "#");
            a.addEventListener("click", (e) => {
                loginform.classList.toggle("hidden");
            });        

            let title = document.createElement('h2');
            title.classList.add('login-heading');
            title.classList.add('educ-login-heading');
            title.appendChild(a);

            let parent = document.getElementById("theme_boost_union-loginorder-local");
            
            parent.insertBefore(title, loginform);                            
        } catch (e) {
         console.log("ERROR: " , e);
        }
    });
</script>
_EOF;

    }
}

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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * EDUC customizations library functions.
 *
 * @package    local_educ
 * @copyright  2026 Universitat Jaume I <https://www.uji.es/>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Adds our authproc filters to the SimpleSAMLphp SP processing chain built by
 * auth_saml2 {@see \auth_saml2\api::authproc_filters_hook()}.
 *
 * The IdP releases the organisation as the multivalued "o" attribute, but
 * Moodle's institution field only takes a single value: auth_saml2 would
 * otherwise implode every value into it. So we copy "o" into a new
 * "institution" attribute and collapse that copy to its first value, leaving
 * "o" untouched for any other consumer. Map it in
 * "Data mapping > Institution" of the auth_saml2 settings.
 *
 * Array keys are filter priorities. auth_saml2 runs core:AttributeMap with
 * oid2name at priority 50, so we run afterwards to see "o" instead of
 * "urn:oid:2.5.4.10".
 *
 * @return array
 */
function local_educ_extend_auth_saml2_proc(): array {
    return [
        60 => [
            'class' => 'core:AttributeCopy',
            'o' => 'institution',
        ],
        61 => [
            'class' => 'core:CardinalitySingle',
            'firstValue' => ['institution'],
        ],
        // Trim surrounding whitespace. No /u modifier on purpose: on invalid
        // UTF-8 preg_replace() would return null and wipe the attribute, and
        // the whitespace an IdP actually emits is ASCII anyway.
        62 => [
            'class' => 'core:AttributeAlter',
            'subject' => 'institution',
            'pattern' => '/^\s+|\s+$/',
            'replacement' => '',
        ],
        // A value that was only whitespace is now empty. Drop the attribute
        // altogether, otherwise auth_saml2 would happily overwrite the user's
        // institution with an empty string on every login.
        63 => [
            'class' => 'core:AttributeAlter',
            'subject' => 'institution',
            'pattern' => '/^$/',
            '%remove',
        ],
    ];
}

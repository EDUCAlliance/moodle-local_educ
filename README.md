# moodle-local_educ
EDUC customizations for moodle

## SAML2 institution mapping

`local/educ` registers a SimpleSAMLphp authproc filter in the SP processing
chain built by `auth/saml2`, through its `extend_auth_saml2_proc` hook
(see [lib.php](lib.php)).

The IdP releases the organisation as the multivalued `o` attribute
(`urn:oid:2.5.4.10`), but Moodle's `institution` field only takes a single
value: `auth_saml2` would otherwise implode every value into it. Stock
SimpleSAMLphp filters do the job, running right after the `core:AttributeMap` /
`oid2name` filter that `auth_saml2` inserts at priority 50:

* `core:AttributeCopy` (priority 60) copies `o` into a new `institution`
  attribute, leaving `o` untouched for any other consumer.
* `core:CardinalitySingle` (priority 61) collapses `institution` to its first
  value.
* `core:AttributeAlter` (priority 62) trims surrounding whitespace.
* `core:AttributeAlter` (priority 63) drops `institution` if the trim left it
  empty, so a blank value never overwrites the user's institution on login.

To finish the mapping, in *Site administration > Plugins > Authentication >
SAML2 > Data mapping > Institution* set:

* **Data field**: `institution`
* **Update local**: `On every login` (or `On creation` if it should only be set
  for new users)

## SAML2 email

The IdP may also release several values in the `mail` attribute
(`urn:oid:0.9.2342.19200300.100.1.3`), which `auth_saml2` would implode into
the user's email. A `core:CardinalitySingle` filter (priority 70) collapses
`mail` to its first value in place, so the usual `mail` mapping in *Data
mapping > Email address* keeps working unchanged.

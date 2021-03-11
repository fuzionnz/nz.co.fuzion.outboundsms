# nz.co.fuzion.outboundsms

When an Outbound SMS is created without visiting the "New SMS" form, civicrm does not send real SMS to the contacts.
With this extension installed, a post hook is triggerred just after the Outbound SMS activity is created.
It sends the real SMS to all the target contacts involved in the activity.

Eg: Send SMS to the contacts when outbound SMS activity is created via Drupal Webform.

This extension is not called on the main SMS form provided by CiviCRM as it already sends an SMS to the contact.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.0+
* CiviCRM

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl nz.co.fuzion.outboundsms@https://github.com/fuzionnz/nz.co.fuzion.outboundsms/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/fuzionnz/nz.co.fuzion.outboundsms.git
cv en outboundsms
```

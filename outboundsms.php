<?php

require_once 'outboundsms.civix.php';
use CRM_Outboundsms_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function outboundsms_civicrm_config(&$config) {
  _outboundsms_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function outboundsms_civicrm_xmlMenu(&$files) {
  _outboundsms_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function outboundsms_civicrm_install() {
  _outboundsms_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function outboundsms_civicrm_postInstall() {
  _outboundsms_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function outboundsms_civicrm_uninstall() {
  _outboundsms_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function outboundsms_civicrm_enable() {
  _outboundsms_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function outboundsms_civicrm_disable() {
  _outboundsms_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function outboundsms_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _outboundsms_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function outboundsms_civicrm_managed(&$entities) {
  _outboundsms_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function outboundsms_civicrm_caseTypes(&$caseTypes) {
  _outboundsms_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function outboundsms_civicrm_angularModules(&$angularModules) {
  _outboundsms_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function outboundsms_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _outboundsms_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function outboundsms_civicrm_entityTypes(&$entityTypes) {
  _outboundsms_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function outboundsms_civicrm_themes(&$themes) {
  _outboundsms_civix_civicrm_themes($themes);
}

/**
 * Ignore SMS  when it is submitted using civi form.
 */
function outboundsms_civicrm_preProcess($formName, $form) {
  if ($formName == 'CRM_Contact_Form_Task_SMS') {
    $_POST['ignore_sms'] = TRUE;
  }
}

/**
 * Implements hook_civicrm_post().
 *
 * Send SMS when outbound sms activity is acreated from API, webform submission, etc.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_post
 */
function outboundsms_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  $outboundTypeID = CRM_Core_PseudoConstant::getKey('CRM_Activity_BAO_Activity', 'activity_type_id', 'SMS');
  if (empty($_POST['ignore_sms']) && $op == 'create' && $objectName == 'Activity' && !empty($objectRef->activity_type_id) && $objectRef->activity_type_id == $outboundTypeID) {
    $activity = civicrm_api3('Activity', 'getsingle', [
      'return' => ["target_contact_id", "source_contact_id"],
      'id' => $objectId,
    ]);
    $sourceContactId = $activity['source_contact_id'] ?? CRM_Core_Session::getLoggedInContactID();
    $providers = civicrm_api3('SmsProvider', 'get', [
      'sequential' => 1,
      'is_default' => 1,
    ]);
    $smsProviderParams = [
      'activity_subject' => $objectRef->subject,
      'provider_id' => $providers['id'],
    ];
    if (empty($activity['target_contact_id'])) {
      return;
    }
    foreach ((array) $activity['target_contact_id'] as $contactId) {
      $mobile =  civicrm_api3('Phone', 'get', [
        'sequential' => 1,
        'contact_id' => $contactId,
        'phone_type_id' => "Mobile",
        'options' => ['sort' => "is_primary desc"],
      ]);
      if (!empty($mobile['values'][0]['phone'])) {
        $smsProviderParams['To'] = $mobile['values'][0]['phone'];
        try {
          CRM_Activity_BAO_Activity::sendSMSMessage(
            $contactId,
            $objectRef->details,
            $smsProviderParams,
            $objectId,
            $sourceContactId
          );
        }
        catch (CRM_Core_Exception $e) {
          $errMsgs[] = $e->getMessage();
        }
      }
    }
  }
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function outboundsms_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 *
function outboundsms_civicrm_navigationMenu(&$menu) {
  _outboundsms_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _outboundsms_civix_navigationMenu($menu);
} // */

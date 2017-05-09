<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

/**
 * @page objects
 * @section content info ContentInfo
 * @ref BxDolContentInfo
 */

/**
 * Get content info from Content modules
 *
 * Add record to sys_objects_content_info table to provide unified access to module's content,
 * to get content info just create an instance of this class and request content info by id,
 * for example:
 * @code
 *  BxDolContentInfo::getObjectInstance('my_system')->getInfo(25); // 25 - is object id
 * @endcode
 *
 * Description of sys_objects_content_info table fields:
 * @code
 *  `name` - system name, it is better to use unique module prefix here, lowercase and all spaces are underscored
 *  `alert_unit` - unit name of the alert which will be fired 
 *  `alert_action_add` - action name of the alert which is fired after a content was added
 *  `alert_action_update` - action name of the alert which is fired after a content was updated
 *  `alert_action_delete` - action name of the alert which is fired after a content was deleted
 *  `class_name` - your custom class name, if you overrride default class
 *  `class_file` - your custom class path
 * @endcode
 *
 */
class BxDolContentInfo extends BxDolFactory implements iBxDolFactoryObject
{
    protected $_oQuery;

    protected $_sSystem;
    protected $_aSystem;

    protected function __construct($sSystem)
    {
        parent::__construct();

        $aSystems = $this->getSystems();
        if(!isset($aSystems[$sSystem]))
            return;

        $this->_sSystem = $sSystem;
        $this->_aSystem = $aSystems[$sSystem];

        if(empty($this->_sSystem))
            return;
    }

   /**
     * get content info object instance
     * @param $sSystem view object name
     * @return null on error, or ready to use class instance
     */
    public static function getObjectInstance($sSystem)
    {
        $sClassKey = 'BxDolContentInfo!' . $sSystem;

        if(isset($GLOBALS['bxDolClasses'][$sClassKey]))
            return $GLOBALS['bxDolClasses'][$sClassKey];

        $aSystems = self::getSystems();
        if(!isset($aSystems[$sSystem]))
            return null;

        $sClassName = 'BxDolContentInfo';
        if(!empty($aSystems[$sSystem]['class_name'])) {
            $sClassName = $aSystems[$sSystem]['class_name'];
            if(!empty($aSystems[$sSystem]['class_file']))
                require_once(BX_DIRECTORY_PATH_ROOT . $aSystems[$sSystem]['class_file']);
        }

        $GLOBALS['bxDolClasses'][$sClassKey] = new $sClassName($sSystem);
        return $GLOBALS['bxDolClasses'][$sClassKey];
    }

    /**
     * get content info object instance (for internal usage). 
     * @see self::getObjectInstanceByAlertAdd, self::getObjectInstanceByAlertUpdate and self::getObjectInstanceByAlertDelete
     * @param $sAlertType alert type (add, update, delete)
     * @param $sUnit alert unit
     * @param $sAction alert action
     * @return null on error, or ready to use class instance
     */
    public static function getObjectInstanceByAlertCommon($sAlertType, $sUnit, $sAction)
    {
        $sAlert = $sUnit . '_' . $sAction;
        $sClassKey = 'BxDolContentInfo!' . bx_gen_method_name($sAlertType . '_' . $sAlert);

        if(isset($GLOBALS['bxDolClasses'][$sClassKey]))
            return $GLOBALS['bxDolClasses'][$sClassKey];

        $aSystems = self::getSystemsByAlertType($sAlertType);
        if(!isset($aSystems[$sAlert]))
            return null;

        $sClassName = 'BxDolContentInfo';
        if(!empty($aSystems[$sAlert]['class_name'])) {
            $sClassName = $aSystems[$sAlert]['class_name'];
            if(!empty($aSystems[$sAlert]['class_file']))
                require_once(BX_DIRECTORY_PATH_ROOT . $aSystems[$sAlert]['class_file']);
        }

        $GLOBALS['bxDolClasses'][$sClassKey] = new $sClassName($aSystems[$sAlert]['name']);
        return $GLOBALS['bxDolClasses'][$sClassKey];
    }

    public static function getObjectInstanceByAlertAdd($sUnit, $sAction)
    {
        return self::getObjectInstanceByAlertCommon('add', $sUnit, $sAction);
    }

    public static function getObjectInstanceByAlertUpdate($sUnit, $sAction)
    {
        return self::getObjectInstanceByAlertCommon('update', $sUnit, $sAction);
    }

    public static function getObjectInstanceByAlertDelete($sUnit, $sAction)
    {
        return self::getObjectInstanceByAlertCommon('delete', $sUnit, $sAction);
    }

    public static function &getSystems()
    {
        if(!isset($GLOBALS['bx_dol_content_info_systems']))
            $GLOBALS['bx_dol_content_info_systems'] = BxDolDb::getInstance()->fromCache('sys_objects_content_info', 'getAllWithKey', '
                SELECT
                    `id` as `id`,
                    `name` AS `name`,
                    `title` AS `title`,
                    `alert_unit` AS `alert_unit`,
                    `alert_action_add` AS `alert_action_add`,
                    `alert_action_update` AS `alert_action_update`,
                    `alert_action_delete` AS `alert_action_delete`,
                    `class_name` AS `class_name`,
                    `class_file` AS `class_file`
                FROM `sys_objects_content_info`', 'name');

        return $GLOBALS['bx_dol_content_info_systems'];
    }

    public static function &getSystemsByAlertType($sAlertType)
    {
        $sKey = 'bx_dol_content_info_systems_' . $sAlertType;

        if(!isset($GLOBALS[$sKey])) {
            $aSystems = BxDolContentInfo::getSystems();
            foreach($aSystems as $aSystem)
                $GLOBALS[$sKey][$aSystem['alert_unit'] . '_' . $aSystem['alert_action_' . $sAlertType]] = $aSystem;
        }

        return $GLOBALS[$sKey];
    }

    public function getName()
    {
        return $this->_sSystem;
    }

    public function getTitle()
    {
        return $this->_aSystem['title'];
    }

    public function getContentAuthor ($iContentId)
    {
        return $this->_call('get_author', $iContentId);
    }

    public function getContentDateAdded ($iContentId)
    {
        return $this->_call('get_date_added', $iContentId);
    }

    public function getContentDateChanged ($iContentId)
    {
        return $this->_call('get_date_changed', $iContentId);
    }

    public function getContentTitle ($iContentId)
    {
        return $this->_call('get_title', $iContentId);
    }

    public function getContentThumb ($iContentId)
    {
        return $this->_call('get_thumb', $iContentId);
    }

    public function getContentLink ($iContentId)
    {
        return $this->_call('get_link', $iContentId);
    }

    public function getContentText ($iContentId)
    {
        return $this->_call('get_text', $iContentId);
    }

    public function getContentInfo ($iContentId, $bSearchableFieldsOnly = true)
    {
        return $this->_call('get_info', $iContentId, $bSearchableFieldsOnly);
    }

    public function getContentSearchResultUnit ($iContentId)
    {
        return $this->_call('get_search_result_unit', $iContentId);
    }

    protected function _call($sMethod)
    {
        if(!BxDolRequest::serviceExists($this->_sSystem, $sMethod))
            return false;

        return BxDolService::call($this->_sSystem, $sMethod, array_slice(func_get_args(), 1));
    }
}

/** @} */

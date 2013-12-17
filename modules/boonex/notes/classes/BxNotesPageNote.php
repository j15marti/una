<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    Notes Notes
 * @ingroup     DolphinModules
 *
 * @{
 */

bx_import('BxTemplPage');
bx_import('BxDolModule');
bx_import('BxDolMenu');

/**
 * Note create/edit pages.
 */
class BxNotesPageNote extends BxTemplPage {    
    
    protected $_oModule;
    protected $_aContentInfo = false;

    protected $_aMapStatus2LangKey = array (
        BX_PROFILE_STATUS_PENDING => '_bx_notes_txt_status_pending',
    );

    public function __construct($aObject, $oTemplate = false) {
        parent::__construct($aObject, $oTemplate);

        $this->_oModule = BxDolModule::getInstance('bx_notes');
        
        // select view note submenu
        $oMenuSumbemu = BxDolMenu::getObjectInstance('sys_site_submenu');
        $oMenuSumbemu->setObjectSubmenu('bx_notes_view_submenu', 'notes-home');

        $iContentId = bx_process_input(bx_get('id'), BX_DATA_INT);
        if ($iContentId)
            $this->_aContentInfo = $this->_oModule->_oDb->getContentInfoById($iContentId);

        if ($this->_aContentInfo)
            $this->addMarkers($this->_aContentInfo); // every field can be used as marker
    
/* TODO: status
        // display message if profile isn't active
        if ($this->_aContentInfo && bx_get_logged_profile_id() == $this->_aContentInfo['author']) { 
            $sStatus = $this->_aContentInfo['status'];
            if (isset($this->_aMapStatus2LangKey[$sStatus])) {
                bx_import('BxDolInformer');
                $oInformer = BxDolInformer::getInstance($this->_oTemplate);
                if ($oInformer)
                    $oInformer->add('bx-notes-status-not-active', _t($this->_aMapStatus2LangKey[$sStatus]), BX_INFORMER_ALERT);
            }
        }        
*/

		bx_import('BxDolView');
		BxDolView::getObjectInstance(BxNotesConfig::$OBJECT_VIEWS, $iContentId)->doView();
    }

    public function getCode () {

        if (!$this->_aContentInfo) { // if note is not found - display standard "404 page not found" page
            $this->_oTemplate->displayPageNotFound();
            exit;
        }

        if (CHECK_ACTION_RESULT_ALLOWED !== ($sMsg = $this->_oModule->isAllowedView($this->_aContentInfo))) {
			$this->_oTemplate->displayAccessDenied($sMsg);
            exit;
        }
        $this->_oModule->isAllowedView($this->_aContentInfo, true);

        return parent::getCode ();
    }

    protected function _getPageCacheParams () {
        return $this->_aContentInfo[BxNotesConfig::$FIELD_ID]; // cache is different for every note
    }

}

/** @} */

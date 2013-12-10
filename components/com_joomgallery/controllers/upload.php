<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-2.0/JG/trunk/components/com_joomgallery/controllers/upload.php $
// $Id: upload.php 4215 2013-04-20 14:26:43Z chraneco $
/****************************************************************************************\
**   JoomGallery 2                                                                   **
**   By: JoomGallery::ProjectTeam                                                       **
**   Copyright (C) 2008 - 2012  JoomGallery::ProjectTeam                                **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                            **
**   Released under GNU GPL Public License                                              **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look                       **
**   at administrator/components/com_joomgallery/LICENSE.TXT                            **
\****************************************************************************************/

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

/**
 * JoomGallery Upload Controller
 *
 * @package JoomGallery
 * @since   2.1
 */
class JoomGalleryControllerUpload extends JController
{
  /**
   * Uploads the selected images
   *
   * @return  void
   * @since   1.5.5
   */
  public function upload()
  {
    $this->_mainframe = JFactory::getApplication();
    $type = $this->_mainframe->getUserStateFromRequest('joom.upload.type', 'type', 'single', 'post', 'cmd');

    // If the applet in JAVA upload checks for the serverProtocol,
    // it issues a HEAD request
    // Simply return an empty doc to send a HTTP 200
    if($type == 'java' && $_SERVER['REQUEST_METHOD'] == 'HEAD')
    {
      jexit();
    }

    require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'upload.php';
    $uploader = new JoomUpload();
    if($uploader->upload($type))
    {
      $msg  = JText::_('COM_JOOMGALLERY_UPLOAD_MSG_SUCCESSFULL');

      // Set redirect if we are asked for that
      if($redirect = JRequest::getVar('redirect', '', '', 'base64'))
      {
        $url  = base64_decode($redirect);
        if(JURI::isInternal($url))
        {
          $this->setRedirect(JRoute::_($url, false), $msg);

          return;
        }
      }

      // Set a redirect according to the correspondent setting in configuration manager
      switch(JoomConfig::getInstance()->get('jg_redirect_after_upload'))
      {
        case 1:
          $this->setRedirect(JRoute::_('index.php?view=upload&tab='.$type, false), $msg);
          break;
        case 2:
          $this->setRedirect(JRoute::_('index.php?view=userpanel', false), $msg);
          break;
        case 3:
          $this->setRedirect(JRoute::_('index.php?view=gallery', false), $msg);
          break;
        default:
          // Don't set a redirect in order
          // to display the debug output
          break;
      }
    }
    else
    {
      if($error = $uploader->getError())
      {
        $this->setRedirect(JRoute::_('index.php?view=upload&tab='.$type, false), $error, 'error');
      }
    }
  }

  /**
   * Handles results of the java upload
   *
   * @return  void
   * @since   1.5.7
   */
  public function concludejavaupload()
  {
    $this->_mainframe = JFactory::getApplication();
    $this->_config    = JoomConfig::getInstance();

    // Send a message if setted in configuration manager
    if($this->_config->get('jg_msg_upload_type') != 0)
    {
      require_once(JPATH_COMPONENT.DS.'helpers'.DS.'messenger.php');
      $this->_user      = & JFactory::getUser();
      $counter    = $this->_mainframe->getUserState('joom.upload.java.counter', 0);
      $messenger  = new JoomMessenger();
      $message    = array(
          'from'      => $this->_user->get('id'),
          'subject'   => JText::_('COM_JOOMGALLERY_MESSAGE_NEW_IMAGES_SUBMITTED_SUBJECT'),
          'body'      => JText::sprintf('COM_JOOMGALLERY_MESSAGE_NEW_IMAGES_SUBMITTED_BODY', $this->_config->get('jg_realname') ? $this->_user->get('name') : $this->_user->get('username'), $counter),
          'mode'      => 'upload'
      );
      $messenger->send($message);
    }
    $this->_mainframe->setUserState('joom.upload.java.counter', 0);
    $msg  = JText::_('COM_JOOMGALLERY_UPLOAD_MSG_SUCCESSFULL');

    // Set a redirect according to the correspondent setting in configuration manager
    switch($this->_config->get('jg_redirect_after_upload'))
    {
      case 1:
        $this->setRedirect(JRoute::_('index.php?view=upload&tab=java', false), $msg);
        break;
      case 2:
        $this->setRedirect(JRoute::_('index.php?view=userpanel', false), $msg);
        break;
      case 3:
        $this->setRedirect(JRoute::_('index.php?view=gallery', false), $msg);
        break;
      default:
        $this->setRedirect(JRoute::_('index.php?view=upload&tab=java', false), $msg);
        break;
    }
  }
}
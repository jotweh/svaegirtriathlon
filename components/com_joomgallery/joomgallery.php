<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-2.0/JG/trunk/components/com_joomgallery/joomgallery.php $
// $Id: joomgallery.php 3865 2012-09-15 14:40:47Z chraneco $
/****************************************************************************************\
**   JoomGallery 2                                                                      **
**   By: JoomGallery::ProjectTeam                                                       **
**   Copyright (C) 2008 - 2012  JoomGallery::ProjectTeam                                **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                            **
**   Released under GNU GPL Public License                                              **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look                       **
**   at administrator/components/com_joomgallery/LICENSE.TXT                            **
\****************************************************************************************/

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

if(version_compare(JVERSION, '3.0', 'ge'))
{
  return JError::raiseWarning(500, JText::_('JoomGallery 2.1 is not compatible with Joomla! 3. Please update to a newer version of JoomGallery once it is available.'));
}

// Require the defines
require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/defines.php';

// Enable JoomGallery plugins
JPluginHelper::importPlugin('joomgallery');

// Register some classes
JLoader::register('JoomGalleryModel', JPATH_COMPONENT.'/model.php');
JLoader::register('JoomGalleryView',  JPATH_COMPONENT.'/view.php');
JLoader::register('JoomHelper',       JPATH_COMPONENT.'/helpers/helper.php');
JLoader::register('JoomAmbit',        JPATH_COMPONENT.'/helpers/ambit.php');
JLoader::register('JoomConfig',       JPATH_COMPONENT_ADMINISTRATOR.'/helpers/config.php');
JLoader::register('JoomFile',         JPATH_COMPONENT_ADMINISTRATOR.'/helpers/file.php');
JTable::addIncludePath(               JPATH_COMPONENT_ADMINISTRATOR.'/tables');

// Create the controller
jimport('joomla.application.component.controller');
$controller = JController::getInstance('JoomGallery');

// Perform the request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
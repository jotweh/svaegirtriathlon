<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-2.0/JG/trunk/components/com_joomgallery/controllers/vote.php $
// $Id: vote.php 3904 2012-09-27 19:09:29Z erftralle $
/****************************************************************************************\
**   JoomGallery  2                                                                     **
**   By: JoomGallery::ProjectTeam                                                       **
**   Copyright (C) 2008 - 2010  JoomGallery::ProjectTeam                                **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                            **
**   Released under GNU GPL Public License                                              **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look                       **
**   at administrator/components/com_joomgallery/LICENSE.TXT                            **
\****************************************************************************************/

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

jimport('joomla.application.component.controller');

/**
 * JoomGallery Vote Controller
 *
 * @package JoomGallery
 * @since   2.1
 */
class JoomGalleryControllerVote extends JController
{
  /**
   * Votes an image
   *
   * @return  void
   * @since   2.1
   */
  public function vote()
  {
    $model = $this->getModel('vote');

    if(!$model->vote())
    {
      $this->setRedirect(JRoute::_('index.php?view=detail&id='.$model->getId(), false), $model->getError(), 'error');
    }
    else
    {
      $this->setRedirect(JRoute::_('index.php?view=detail&id='.$model->getId(), false), JText::_('COM_JOOMGALLERY_DETAIL_RATINGS_MSG_YOUR_VOTE_COUNTED'));
    }
  }
}
<?php defined('_JEXEC') or die('Direct Access to this location is not allowed.'); ?>
  <div class="jg_minicount">
<?php if($this->total < 1): ?>
    <?php echo JText::_('COM_JOOMGALLERY_MINI_NO_IMAGES'); ?>
<?php endif;
      if($this->total == 1): ?>
    <?php echo JText::_('COM_JOOMGALLERY_MINI_ONE_IMAGE_FOUND'); ?>
<?php endif;
      if($this->total > 1): ?>
    <?php echo JText::sprintf('COM_JOOMGALLERY_MINI_IMAGES_FOUND', $this->total); ?>
<?php endif; ?>
  </div>
  <div class="pagination">
    <?php echo $this->pagination->getPagesLinks(); ?>
  </div>
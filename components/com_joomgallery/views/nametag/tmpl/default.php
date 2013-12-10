<?php defined('_JEXEC') or die('Direct Access to this location is not allowed.'); ?>
<div class="gallery minigallery" style="text-align:center;">
  <div class="jg_header">
    <?php echo JText::_('COM_JOOMGALLERY_SELECT_NAMETAG'); ?>
  </div>
  <div>
    <form action="index.php" name="selectnametagform" method="post">
      <div>
        <input type="submit" value="<?php echo JText::_('COM_JOOMGALLERY_DETAIL_NAMETAGS_SELECT_MYSELF'); ?>" class="button" onclick="window.parent.selectnametag(<?php echo $this->_user->get('id'); ?>, '<?php echo $this->_user->get($this->_config->get('jg_realname') ? 'name' : 'username'); ?>');return false;" />
      </div>
      <div>
        <?php echo JHtml::_('joomselect.users', 'selectnametaglist', null, true, array(), 'window.parent.selectnametag(this.value, this[this.selectedIndex].text);', false); ?>
      </div>
    </form>
  </div>
</div>
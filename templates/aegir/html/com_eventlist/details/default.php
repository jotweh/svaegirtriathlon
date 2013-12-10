<?php
/**
 * @version 1.0.2 Stable $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2009 Christoph Lukes
 * @license GNU/GPL, see LICENSE.php
 * EventList is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * EventList is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with EventList; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

?>
<div id="eventlist" class="event_id<?php echo $this->row->did; ?> el_details" itemscope itemtype="http://data-vocabulary.org/Event">
<!-- Details EVENT -->
	<h2 itemprop="summary">
		<?php echo $this->escape($this->row->title); ?>
		<?php
    	if (!$this->item){
            echo '&nbsp;'.ELOutput::editbutton(0, $this->row->did, $this->params, $this->allowedtoeditevent, 'editevent' );
        } else{
            echo '&nbsp;'.ELOutput::editbutton($this->item->id, $this->row->did, $this->params, $this->allowedtoeditevent, 'editevent' );
        }
    	?>
	</h2>

	<?php //flyer
	echo ELOutput::flyer( $this->row, $this->dimage, 'event' );
	?>
	<dl class="event_info floattext">
  		<dt class="when"><?php echo JText::_( 'COM_EVENTLIST_WHEN' ); ?></dt>
		<dd class="when">
			<?php $_starttimestamp = strtotime($this->row->dates . ' ' . $this->row->times); ?>
			<?php $_endtimestamp = $this->row->enddates ? strtotime($this->row->enddates . ' ' . $this->row->endtimes) : 0; ?>
			<meta itemprop="startDate" content="<?php echo date('c', $_starttimestamp); ?>"/>
			<?php echo ELOutput::formatdate($this->row->dates, $this->row->times); ?>
			<?php if ($this->row->enddates) : ?>
				<meta itemprop="endDate" content="<?php echo date('c', $_endtimestamp); ?>"/>
    			<?php echo ' - '. ELOutput::formatdate($this->row->enddates, $this->row->endtimes); ?>	
    		<?php endif; ?>
    		
    		<?php
    		if ($this->elsettings->showtimedetails == 1) :
    	
				echo '&nbsp;'.ELOutput::formattime($this->row->dates, $this->row->times);
						
				if ($this->row->endtimes) :
					echo ' - '.ELOutput::formattime($this->row->enddates, $this->row->endtimes);
				endif;
			endif;
			?>
		</dd>
  		<?php
  		if ($this->row->locid != 0) :
  		?>
		    <dt class="where"><?php echo JText::_( 'COM_EVENTLIST_WHERE' ); ?></dt>
		    <dd class="where">
		    <span itemprop="location" itemscope itemtype="http://data-vocabulary.org/​Organization">
	    		<?php if (($this->elsettings->showdetlinkvenue == 1) && (!empty($this->row->url))) : ?>
	
				    <a href="<?php echo $this->row->url; ?>"><span itemprop="name"><?php echo $this->escape($this->row->venue); ?></span></a> -
	
				<?php elseif ($this->elsettings->showdetlinkvenue == 2) : ?>
	
				    <a href="<?php echo JRoute::_( 'index.php?view=venueevents&id='.$this->row->venueslug ); ?>">
				    	<span itemprop="name"><?php echo $this->row->venue; ?></span>
				   	</a> -
	
				<?php elseif ($this->elsettings->showdetlinkvenue == 0) : ?>
					<span itemprop="name"><?php echo $this->escape($this->row->venue)?></span><?php echo ' - '; ?>
				<?php endif; ?>
				<?php echo $this->escape($this->row->city); ?>		
			</span>
			</dd>

		<?php endif; ?>

		<dt class="category"><?php echo JText::_( 'COM_EVENTLIST_CATEGORY' ); ?></dt>
    		<dd class="category">
    			<span itemprop="category">
					<?php echo "<a href='".JRoute::_( 'index.php?view=categoryevents&id='.$this->row->categoryslug )."'>".$this->escape($this->row->catname)."</a>";?>
				</span>
			</dd>
			
	<?php
	// is a plugin catching the display of creator ?
  $obj = new stdClass();
  // is a plugin catching this ?
  if ($res = $this->dispatcher->trigger( 'onEventCreatorDisplay', array( $this->row->created_by, $obj )))
  {
     ?>
     <dt class="creator"><?php echo JText::_( 'COM_EVENTLIST_PROPOSED_BY' ); ?></dt>
        <dd class="creator">
        <?php echo $obj->text;?>
      </dd>
     <?php
  }
  ?>
  
	</dl>
<!-- END of event summary section -->
	
  	<?php if ($this->elsettings->showevdescription == 1) : ?>
  		<div class="description event_desc" itemprop="description">
  			<?php echo $this->row->datdescription; ?>
  		</div>

  	<?php endif; ?>

<!--  	Venue  -->

	<?php if ($this->row->locid != 0) : ?>

		<?php //flyer
		echo ELOutput::flyer( $this->row, $this->limage );
		echo ELOutput::mapicon( $this->row );
		?>

		<dl class="location floattext">
			 <dt class="venue"><?php echo $this->elsettings->locationname; ?></dt>
				<dd class="venue">
				<?php echo "<a href='".JRoute::_( 'index.php?view=venueevents&id='.$this->row->venueslug )."'>".$this->escape($this->row->venue)."</a>"; ?>

				<?php if (!empty($this->row->url)) : ?>
					&nbsp; - &nbsp;
					<a href="<?php echo $this->row->url; ?>"> <?php echo JText::_( 'COM_EVENTLIST_WEBSITE' ); ?></a>
				<?php
				endif;
				?>
				</dd>

			<?php
  			if ( $this->elsettings->showdetailsadress == 1 ) :
  			?>

  				<?php if ( $this->row->street ) : ?>
  				<dt class="venue_street"><?php echo JText::_( 'COM_EVENTLIST_STREET' ); ?></dt>
				<dd class="venue_street">
    				<?php echo $this->escape($this->row->street); ?>
				</dd>
				<?php endif; ?>

				<?php if ( $this->row->plz ) : ?>
  				<dt class="venue_plz"><?php echo JText::_( 'COM_EVENTLIST_ZIP' ); ?></dt>
				<dd class="venue_plz">
    				<?php echo $this->escape($this->row->plz); ?>
				</dd>
				<?php endif; ?>

				<?php if ( $this->row->city ) : ?>
    			<dt class="venue_city"><?php echo JText::_( 'COM_EVENTLIST_CITY' ); ?></dt>
    			<dd class="venue_city">
    				<?php echo $this->escape($this->row->city); ?>
    			</dd>
    			<?php endif; ?>

    			<?php if ( $this->row->state ) : ?>
    			<dt class="venue_state"><?php echo JText::_( 'COM_EVENTLIST_STATE' ); ?></dt>
    			<dd class="venue_state">
    				<?php echo $this->escape($this->row->state); ?>
    			</dd>
				<?php endif; ?>

				<?php if ( $this->row->country ) : ?>
				<dt class="venue_country"><?php echo JText::_( 'COM_EVENTLIST_COUNTRY' ); ?></dt>
    			<dd class="venue_country">
    				<?php echo $this->row->countryimg ? $this->row->countryimg : $this->row->country; ?>
    			</dd>
    			<?php endif; ?>
			<?php
			endif;
			?>
		</dl>

		<?php if ($this->elsettings->showlocdescription == 1) :	?>

  			<div class="description location_desc">
  				<?php echo $this->row->locdescription;	?>
  			</div>

		<?php endif; ?>

	<?php
	//row->locid !=0 end
	endif;
	?>

	<?php if ($this->row->registra == 1) : ?>

		<!-- Registration -->
		<?php echo $this->loadTemplate('attendees'); ?>

	<?php endif; ?>
	
	<?php if ($this->elsettings->commentsystem != 0 && !$this->params->get('pop', 0)) :	?>
	
		<!-- Comments -->
		<?php echo $this->loadTemplate('comments'); ?>
		
  	<?php endif; ?>

<p class="copyright">
	<?php echo ELOutput::footer( ); ?>
</p>
</div>
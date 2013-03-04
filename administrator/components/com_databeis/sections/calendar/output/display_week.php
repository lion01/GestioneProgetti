<?php
/**
* $Id: display_week.php 865 2011-03-21 07:52:39Z angek $
* @package    Databeis
* @subpackage Calendar
* @copyright  Copyright (C) 2006-2010 DataBeis. All rights reserved.
* @license    http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.php
*
* This file is part of Databeis.
*
* Databeis is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License License as published by
* the Free Software Foundation, either version 3 of the License,
* or any later version.
*
* Databeis is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Databeis.  If not, see <http://www.gnu.org/licenses/gpl.html>.
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

echo $form->Start();
?>
<script type="text/javascript">
function task_delete()
{
	if(!document.adminForm.boxchecked.value) {
		alert('<?php echo PFformat::Lang('ALERT_LIST');?>');
	}
	else {
		submitbutton('task_delete');
	}
}
</script>
<a id="pf_top"></a>
<div class="pf_container">
    <div class="pf_header componentheading">
        <h3><?php echo $ws_title." / "; echo PFformat::Lang('CALENDAR');?> :: <?php echo PFformat::Lang('DISPLAY_WEEK');?>
        <?php echo PFformat::SectionEditButton();?>
        </h3>
    </div>
    <div class="pf_body">
    
        <!-- NAVIGATION START-->
        <?php PFpanel::Position('calendar_nav'); ?> 
        <!-- NAVIGATION END -->
        
		<!-- LEGEND START-->
        <div class="pf_legend">
            <span class="event project_event" <?php echo $project_bg;?>><?php echo PFformat::Lang('PROJECTS'); ?></span>
            <span class="event milestone_event" <?php echo $milestone_bg;?>><?php echo PFformat::Lang('MILESTONES'); ?></span>
            <span class="event task_event" <?php echo $task_bg;?>><?php echo PFformat::Lang('TASKS'); ?></span>
            <span class="event"><?php echo PFformat::Lang('EVENTS'); ?></span>
        </div>
        <!-- LEGEND END -->
        <!-- TABLE START -->
        <table class="pf_table adminlist" width="100%" cellpadding="1" cellspacing="1">
            <thead>
                <tr>
                    <th class="sectiontableheader title"><?php echo PFformat::Lang('HOUR');?></th>
                    <?php
                        $c = 0;
                        while ($start_of_week_y.$start_of_week_m.$start_of_week <= $end_of_week_y.$end_of_week_m.$end_of_week ) 
                        {
  	                        ?>
  	                        <th class="sectiontableheader title" width="14%" style="text-align:center"><?php echo $day_names[$c];?></th>
  	                        <?php
  	                        $start_of_week_y = date("Y", mktime(0,0,0,$start_of_week_m,$start_of_week+1,$start_of_week_y));
  	  
                            if (date("m", mktime(0,0,0,$start_of_week_m,$start_of_week+1,$start_of_week_y)) == $start_of_week_m) {
                                $start_of_week = date("d", mktime(0,0,0,$start_of_week_m,$start_of_week+1,$start_of_week_y));
                            } else {
                                $start_of_week_m = date("m", mktime(0,0,0,$start_of_week_m,$start_of_week+1,$start_of_week_y));
                                $start_of_week   = date("d", mktime(0,0,0,$start_of_week_m,1,$start_of_week_y));
                            }
                            $c++;
                        }
                    ?>
                </tr>
            </thead>
            <tbody>
            <?php 
                $k = 0;
                $h = 0;
                $html = '';
                $link_edit    = 'section=calendar&task=form_edit&year='.$year.'&month='.$month.'&day='.$today.'&id=';
                $project_link = 'section=projects&task=display_details&id=';
  	            $task_link    = 'section=tasks&task=display_details&id=';
  	            $cday = 1;

                foreach ($hours AS $hour) 
                {
                    $html .= '
                    <tr class="row'.$k.' sectiontableentry'.($k + 1).'">
                 	     <td width="5%" align="center"><div class="day">'.$hour.'</div></td>
                 	     <td>';
 
                    while(8 > $cday)
                    {
                        // Events
                    	foreach($rows[$cday][$h]['events'] AS $x => $row)
                     	{
                     	    JFilterOutput::objectHTMLSafe($row);
                     	    $checkbox = '<input id="cb'.$x.'" name="cid[]" value="'.$row->id.'" onclick="isChecked(this.checked);" type="checkbox">';
                     	    $html .= '
                            <div class="event">'.$checkbox.'
                                <a href="'.PFformat::Link($link_edit.$row->id).'">'.$row->title.'</a>
                            </div>';
                     	}
                     	// Projects
                     	foreach($rows[$cday][$h]['projects'] AS $x => $row)
                     	{
                     	    JFilterOutput::objectHTMLSafe($row);
                     	    $html .= '
                            <div class="event project_event" '.$project_bg.'>
                                 <a href="'.PFformat::Link($project_link.$row->id).'">'.$row->title.'</a>
                            </div>';
                     	}
                     	// Milestones
                     	foreach($rows[$cday][$h]['milestones'] AS $x => $row)
                     	{
                 	        JFilterOutput::objectHTMLSafe($row);
                 	        $html .= '<div class="event milestone_event" '.$milestone_bg.'>'.$row->title.'</div>';
                     	}
                     	// Tasks
                     	foreach($rows[$cday][$h]['tasks'] AS $x => $row)
                     	{
                     	    JFilterOutput::objectHTMLSafe($row);
                     	    $html .= '
                            <div class="event task_event" '.$task_bg.'>
                                 <a href="'.PFformat::Link($task_link.$row->id).'">'.$row->title.'</a>
                            </div>';
                     	}
                     	if($cday != 7) $html .= '</td><td>';
                        $cday++;
                    }
                    
                    $cday = 1;
                 	$k = 1-$k;
             	    $h++;
             	    $html .= '</td></tr>';
                }
                
                echo $html;
                unset($html);
            ?>
            </tbody>
        </table>
        <!-- TABLE END -->

    </div>
</div>
<?php
echo $form->HiddenField('option');
echo $form->HiddenField('section');
echo $form->HiddenField('task', 'display_week');
echo $form->HiddenField('boxchecked');
echo $form->End();
?>
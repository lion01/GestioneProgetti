<?php
/**
* @package   User Status
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/


defined( '_JEXEC' ) or die( 'Restricted access' );

$user = PFuser::GetInstance();

if($user->GetId()) {

    $load   = PFload::GetInstance();
    $config = PFconfig::GetInstance();
    $com    = PFcomponent::GetInstance();
    
    $location = $com->Get('location');
    
    // Update activity?
    if(JRequest::getVar('setactivity')) {
        $user->SetProfile('activity', JRequest::getVar('activity'));
    }

    // Add panel CSS
    $load->PanelCSS('mystatus.css', 'cp_mystatus');
    
    // Start output
    $pf_form = new PFform('myactivity', NULL, 'post');
    $pf_form->setBind(true, 'REQUEST');
    echo $pf_form->Start();
    ?>
    <div class="myactivity">
    	<label for="mystatus"><?php echo PFformat::Lang('MY_STATUS');?></label>
		<input type="text" id="mystatus" class="myactivity_input" 
                    name="activity" value="<?php echo htmlspecialchars($user->GetProfile('activity', ''), ENT_QUOTES);?>"
                    maxlength="124" size="20"
                    onfocus="this.select()"
             />
             <input type="submit" name="submitbutton" value="Submit" class="pf_button"/>
    </div>
       
    <?php
    echo $pf_form->HiddenField('option');
    echo $pf_form->HiddenField('section', 'controlpanel');
    echo $pf_form->HiddenField('setactivity', '1');
    echo $pf_form->End();
    // End output
    
    unset($load,$config,$com);
}
?>
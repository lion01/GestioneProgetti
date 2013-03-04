<?php

// This line must be at the top of every PHP file to prevent direct access!
defined( '_JEXEC' ) or die( 'Restricted access' );

// Start form output
echo $form->Start();
?>
<a id="pf_top"></a>
<div class="pf_container">
    <div class="pf_header componentheading">
    
        <!-- 
        Generate breadcrumbs. Class PFformat can be found in:
        com_databeis/_core/lib/utilities.php
        -->
        <h3>
        <?php echo PFformat::WorkspaceTitle()." / "; echo PFformat::Lang('EXAMPLE_SECTION');?> :: <?php echo PFformat::Lang('NEW');?>
        </h3>
        
    </div>
    <div class="pf_body">

        <table class="admintable" width="100%">
            <tr>
        	    <td class="key required" width="150"><?php echo PFformat::Lang('TITLE');?></td>
        		<td><?php echo $form->InputField('title*', '', 'size="50" maxlength="124"');?></td>
        		<td><?php echo PFformat::Lang('EXAMPLE_TITLE_DESC');?></td>
        	</tr>
       	</table>

    </div>
</div>
<?php
// Add hidden form fields
echo $form->HiddenField("option");
echo $form->HiddenField("section");
echo $form->HiddenField("task");

// Close form
echo $form->End();
?>
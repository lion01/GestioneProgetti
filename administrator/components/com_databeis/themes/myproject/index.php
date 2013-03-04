<?php
/**
* $Id: index.php 442 2009-10-06 13:56:37Z kyle.ledbetter $
* @package   Databeis MyProject
* @copyright Copyright (C) 2010 Pixel Praise LLC. All rights reserved.
* @license   All PHP code is licensed under GNU/GPL v3, all Images & CSS is copyrighted.
* http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.php
*
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

$com  = PFcomponent::GetInstance();
$load = PFload::GetInstance();
$cfg  = PFconfig::GetInstance();
$core = PFcore::GetInstance();

$location = $com->Get('location');
$hide_tpl = $cfg->Get('hide_template');
$section  = $core->GetSection();

$uri  = JFactory::getURI();

// Load theme header processes
PFprocess::Event('theme_header');

// Load theme CSS
$load->ThemeCSS('general.css');
$load->ThemeCSS('theme.css');
$load->ThemeCSS('icons.css');

// Get theme color
$themeColor = $cfg->get('themeColor', 'theme_myproject');
if(!$themeColor){
$themeColor = "color1";
}

// Hide Task Info
$hide_info = $cfg->get('hide_info', 'theme_myproject');

// Hide Return Link
$hide_return = $cfg->get('hide_return', 'theme_myproject');

// Hide Footer
$hide_footer = $cfg->get('hide_footer', 'theme_myproject');

// Load theme css
$load->ThemeCSS('colors/'.$themeColor.'.css');

// Hide the joomla admin toolbar in the backend, we're not using it
if($location == 'backend') {
    echo '<style type="text/css">#toolbar-box { display:none; }</style>';
}

// Load theme for location
if($location == 'frontend' && $hide_tpl == '1') {
	$load->ThemeCSS('notpl.css');
} else if($location == 'frontend' && !$hide_tpl) {
	$load->ThemeCSS('frontend.css');
} else if($location == 'backend') {
	$load->ThemeCSS('backend.css');
}

//Check for active workspace
$user = PFuser::GetInstance();
$workspace = $user->GetWorkspace();

//Change the active Joomla template if using Databeis on the frontend and hiding Joomla
$user = &JFactory::getUser();
if($location == 'frontend' && $hide_tpl == '1') {
$app =& JFactory::getApplication();
$app->setTemplate('rhuk_milkyway'); 
}
?>
<div class="<?php echo $themeColor;?>">
<?php if($location == 'backend') { ?>
<?php PFpanel::Position("theme_header", "panel_simple"); ?>
<?php } ?>
<?php if($location == 'frontend' && $hide_tpl == '1') { ?>
<div id="pf-frontend-notemplate">

<?php } ?>
<div id="pf-header">
	
	<?php echo PFpanel::Render("theme_logo", "panel_simple"); ?>
	<?php echo PFpanel::Render("quicklink_project", "panel_simple"); ?>
	<?php echo PFpanel::Render("cp_welcome", "none"); ?>
	<div class="clr"></div>
	<?php if(!$hide_tpl && ($location == 'frontend')) { ?>
	<?php PFpanel::Position("theme_header", "panel_simple"); ?>
	<?php } ?>
</div>
<?php if($location == 'frontend' && $hide_tpl == '1') { ?>
<div id="pf-sidebar">
	<?php if(!$hide_return){?>
	<div class="pf-navigation">
		<ul class="return-list">
		<li class="return-item"><a href="<?php echo $uri->base();?>index.php" class="pf-return-link"><span>Uscita</span></a></li>
		</ul>
	</div>
	<?php } ?>
	<?php PFpanel::Position("theme_header", "panel_simple"); ?>
	
	<div class="clr"></div>
	<?php PFpanel::Position("theme_sidebar"); ?>
</div>
<?php } ?>
<div id="pf-wrapper" class="pf-<?php echo $section;?> pf-<?php echo $location;?> pf-color-<?php echo PFtheme::ProjectColor(); ?> <?php if(!$workspace){echo"pf-noworkspace";}?> <?php if($hide_info){echo"pf-hideinfo";}?>">
	<div id="pf-wrapper-inner">	
	<div id="pf-top">
     	<div id="pf-mainpanel"><?php PFpanel::Position('theme_pos1'); ?>
     	<div class="clr"></div>
   </div>
   <div class="clr"></div>
      <div id="pf-section-subpanel"><?php PFpanel::Position($section."_sub"); ?></div>
   </div>
   <div id="pf-body">
      <div>
      	<div class="pf-inner">
	         <div id="pf-content">
	         	<div id="pf-infopanel">
	         	<?php PFpanel::Position('theme_pos2'); ?>
	         	</div>
	            <div><?php PFpanel::Position('theme_top'); ?></div>
	            <div id="pf-main"><?php PFsection::Render(); ?></div>
	            <div id="pf-bottom"><?php PFpanel::Position('theme_bottom'); ?></div>
	         </div>
	         <div class="clr"></div>
         </div>
         <div class="clr"></div>
      </div>
      <div class="clr"></div>
   </div>

   </div>
</div>
<?php if($location == 'frontend' && $hide_tpl) { ?>
</div>
<?php } ?>
</div>
<?php
unset($com,$load,$core,$cfg);
?>



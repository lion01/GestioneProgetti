<?php
/*
* This is the entry file for your section. From here on you can process any user requests.
* The class and controller file are automatically included at this point. You don't have to
* load them again!
*/

// This line must be at the top of every PHP file to prevent direct access!
defined( '_JEXEC' ) or die( 'Restricted access' );

// Load core class object (see: com_databeis/_core/core.php)
$core = PFcore::GetInstance();

// Capture user input
$id = (int) JRequest::GetVar('id');

// Create a new instance of our example controller (pod.controller.php)
$controller = new PFexampleController();

// Get the current task from the core and decide what to do
switch( $core->GetTask() )
{
    // This is the default action and may be the "main" screen of your section (such as task list)
	default:
		$controller->DisplayList();
		break;

    // Shows the form for a new item
	case 'form_new':
        $controller->DisplayNew();
		break;
	
    // Shows the form for editing an item
	case 'form_edit':
		$controller->DisplayEdit($id);
		break;
		
	// Stores a new item
	case 'task_save':
		$controller->Save();
		break;
		
	// Updates an existing item
	case 'task_update':
		$controller->Update($id);
		break;
}
?>
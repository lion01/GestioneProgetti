<?php
/*
* This file contains the controller class. Its methods are normally called 
* from the *.init.php file. The controller class is responsible for preparing
* data and assembling pages.
*/

// This line must be at the top of every PHP file to prevent direct access!
defined( '_JEXEC' ) or die( 'Restricted access' );

// The class extends "PFobject" which is located in: com_databeis/_core/core.php
class PFexampleController extends PFobject
{
    /**
     * Constructor. Nothing special about it
     **/
    public function __construct()
    {
        // Call PFobject constructor
        parent::__construct();
        
        // You can write any custom code below here
    }
    
    /**
     * Displays a list of example items that the user has created
     *      
     **/
	public function DisplayList()
	{
		require_once( $this->GetOutput('list_items.php') );
    }
    
    /**
     * Displays a form for creating a new item
     *      
     **/
    public function DisplayNew()
    {
     }
    
    /**
     * Displays a form for editing an item
     * 
     * @param   integer   The record id to edit          
     **/
    public function DisplayEdit($id)
    {
     }
    
    /**
     * Stores an item and then redirects back to the list overview
     * 
     * @return    boolean    True on success, False on error          
     **/
    public function Save()
    {
     }
    
    /**
     * Updates an item and then redirects back to the list overview
     * 
     * @param     integer    The record id to update     
     * @return    boolean    True on success, False on error            
     **/
    public function Update($id)
    {
     }
}
?>
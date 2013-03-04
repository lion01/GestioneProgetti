<?php
/*
* This file contains the section class. Its methods are normally called 
* from the *.controller.php and/or output files. The class is responsible for 
* fetching and processing data from the database.
*/

// This line must be at the top of every PHP file to prevent direct access!
defined( '_JEXEC' ) or die( 'Restricted access' );


class PFexampleClass extends PFobject
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Counts all records for the given project in the table #__pf_exampledata
     * 
     * @param    integer    $project    The project id
     * @return   integer    $count      The amount of items found
     **/
    public function Count($project = 0)
    {
      
        return 1;       
    }
    
    /**
     * Loads a single record from the table #__pf_exampledata
     * 
     * @param    integer    $id     The item id
     * @return   mixed      $row    Object if a record was found, otherwise NULL
     **/
    public function Load($id)
    {
        return 1;
    }
    
    /**
     * Loads a list of records from the table #__pf_exampledata
     * 
     * @param    integer    $limitstart    List limitstart
     * @param    integer    $limit         List limit
     * @param    string     $ob            Order by field
     * @param    string     $od            Order direction
     * @param    integer    $project       Project id  
     * @return   array      $rows          The records
     **/
    public function LoadList($limitstart = 0, $limit = 50, $ob = 'e.title', $od = 'ASC', $project = 0)
    {
        return 1;       
    }
    
    /**
     * Saves a new item in the table #__pf_exampledata
     * 
     * @return   boolean    True on success, False on error
     **/
    public function Save()
    {
        return true;
    }
    
    /**
     * Updates a record
     * 
     * @param    integer    $id    The record id to update
     * @return   boolean    True on success, False on error
     **/
    public function Update($id)
    {
        return true;       
    }
}
?>
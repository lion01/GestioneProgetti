<?php
/**
* $Id: comments.php 838 2010-11-25 20:49:32Z eaxs $
* @package   Databeis
* @copyright Copyright (C) 2006-2010 DataBeis. All rights reserved.
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.php
*
* This file is part of Databeis.
*
* Databeis is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
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

if(!defined('PF_COMMENTS_PROCESS')) {
	define('PF_COMMENTS_PROCESS', 1);
	
class PFcomments
{
    private $scope;
    private $itemid;
    
    private function Check($content)
    {
        if(!$this->scope || !$this->itemid) return false;
		if(!$content) return false;
		
		return true;
    }
    
    public function Init($scope, $itemid)
    {
        $this->scope  = $scope;
        $this->itemid = $itemid;
    }
    
    public function Load($id)
    {
        $db = PFdatabase::GetInstance();
        $id = $db->Quote($id);
        
        $query = "SELECT * FROM #__pf_comments"
               . "\n WHERE id = $id";
			   $db->setQuery($query);
			   $row = $db->loadObject();
			       
		if($db->getErrorMsg()) {
            unset($db);
            return false;
        }
        
        unset($db);
		return $row;
    }
    
    public function LoadList()
	{
	    $db = PFdatabase::GetInstance();
	    
		$scope = $db->Quote($this->scope);
		$id    = $db->Quote($this->itemid);
			
		$query = "SELECT c.*, u.name FROM #__pf_comments AS c"
			   . "\n LEFT JOIN #__users AS u ON u.id = c.author"
			   . "\n WHERE c.scope = $scope"
			   . "\n AND c.item_id = $id"
			   . "\n GROUP BY c.cdate"
			   . "\n ORDER BY c.cdate DESC";
			   $db->setQuery($query);
			   $rows = $db->LoadObjectList();

		if(!is_array($rows)) $rows = array();
		if($db->getErrorMsg()) return false;
		
		return $rows;       
	}
	
	public function Save($title, $content, $author)
	{
		if(!$this->Check($content, $author)) return false;
		
		$db = PFdatabase::GetInstance();
		
		$title   = $db->Quote($title);
		$content = $db->Quote($content);
		$author  = $db->Quote(intval($author));
		$scope   = $db->Quote($this->scope);
		$id      = $db->Quote($this->itemid);
		$now     = time();
		
		/////////////////////////////// Enregistre le texte du commentaire en HTML
				
			if(defined('PF_DEMO_MODE')) {
			$content = $db->Quote(JRequest::getVar('ctext'));
		    }
		    else {
			$content = $db->Quote(JRequest::getVar('ctext', '', 'default', 'none', JREQUEST_ALLOWRAW));
		    }
					
		//////////////////////////////
		$query = "INSERT INTO #__pf_comments VALUES"
               . "\n (NULL, $title, $content, $scope, $id, $author, $now)";
		       $db->setQuery($query);
		       $db->query();
		       
		if($db->getErrorMsg()) return false;
		
		$query = "SELECT LAST_INSERT_ID() FROM #__pf_comments";
			     $db->setQuery($query);
		         $db->query();
				 $lastinsertid = $db->loadResult();
				 
		$this->save_file($lastinsertid); // Appelle la fonction save_file
			
		return true;
	}
	
	public function save_file($id_comment = NULL)
	{
		jimport('joomla.filesystem.file');
		
		$user     = PFuser::GetInstance();
        $config = PFconfig::GetInstance();
		$project = (int) $user->GetWorkspace();
		$db = PFdatabase::GetInstance();
		
		$files   = JRequest::getVar( 'file', array(), 'files');
		$descs   = JRequest::getVar('description', array());

		$i       = 0;
		$count   = (int) count($files['name']);
		$tasks   = JRequest::getVar('tasks', array(), 'array');

		while ($count > $i)	{
			$file             = array();
			$file['size']     = $files['size'][$i];
			$file['tmp_name'] = $files['tmp_name'][$i];
			$file['name']     = JFile::makeSafe($files['name'][$i]);

			$desc         = $descs[$i];
			//$user         = $this->_session->getUser();
			$now          = time();
			$e            = false;
			$dir = 0;

			if (isset($file['name'])) {
				// generate prefix
				$prefix1  = "project_".$project;
				$prefix2  = uniqid(md5($file['name']).rand(1,1000))."_";
				$filepath = JPath::clean(JPATH_ROOT.DS.$config->Get('upload_path', 'filemanager').DS.$prefix1);
				$size     = $file['size'] / 1024;
				$name     = $file['name'];

				// create the upload path if it does not exist
				if(!JFolder::exists($filepath)) {
					JFolder::create($filepath, 0777);
				}
				else {
					JPath::setPermissions($filepath, '0644', '0777');
				}

				// upload the file
				if (!JFile::upload($file['tmp_name'], $filepath.DS.$prefix2.strtolower($file['name']))) {
					$i++;
					$e = true;
					$this->SetError(PFL_E_FILE_UPLOAD);	
					continue;
				}

				// chmod upload folder
				JPath::setPermissions($filepath, '0644', '0755');

				$query = "INSERT INTO #__pf_files VALUES(NULL, ".$db->quote($name).", '".$prefix2."', ".$db->quote($desc).", ".$db->quote($user->GetId()).","
				. "\n ".$db->quote($project).", ".$dir.", ".$db->quote($size).", ".$db->quote($now).", ".$db->quote($now).")"; // $db->quote($dir) par $dir  , rajoute $task_id et lastinsertid
				$db->setQuery($query);
				$db->query();

				$id = $db->insertid();

				if(!$id) {
					$i++;
					$e = true;
					$this->SetError($db->getErrorMsg());
					continue;
				}

				// save task connections
				if((int) $config->Get('attach_files', 'filemanager')) {
					$this->save_attachments($id, 'file', $tasks);
				}
			}
			$i++;
		}
	}
	
	public function save_attachments($id, $type, &$tasks)
	{
		$db = PFdatabase::GetInstance();
		
		$id   = $db->Quote($id);
		$type = $db->Quote($type);
		$looped = array();

		foreach ($tasks AS $task){
			$task = (int) $task;

			if(!$task) {
				continue;
			}

			if(!in_array($task, $looped)) {
				$task2 = $db->Quote((int)$task);

				$query = "INSERT INTO #__pf_task_attachments VALUES(NULL,$task2,$id,$type)";
				$db->setQuery($query);
				$db->query();

				if($db->getErrorMsg()) {
					$this->SetError($db->getErrorMsg());
					continue;
				}
				$looped[] = $task;
			}
		}
	}
	
	public function Update($title, $content, $id)
	{
	    if(!$this->Check($content)) return false;
	    
	    $db = PFdatabase::GetInstance();
	    
		$title   = $db->Quote($title);
		$content = $db->Quote($content);
		$id      = $db->Quote(intval($id));
		
		$query = "UPDATE #__pf_comments"
               . "\n SET title = $title, content = $content"
		       . "\n WHERE id = $id";
		       $db->setQuery($query);
		       $db->query();
		       
		if($db->getErrorMsg()) return false;
		
		$this->save_file($id_comment); // Appelle la fonction save_file	
		
		return true;
	}
	
	public function Delete($id)
	{
	    $db = PFdatabase::GetInstance();
		$id = $db->Quote($id);
		
		$query = "DELETE FROM #__pf_comments WHERE id = $id";
		       $db->setQuery($query);
		       $db->query();
		       
		if($db->getErrorMsg()) return false;
		return true;
	}
	
	public function RenderNew($title = '', $content = '', $date = null, $formname = 'adminform')
	{
		$title   = htmlspecialchars($title);
		$content = htmlspecialchars($content);
		$editor  = JFactory::getEditor();
        $avc     = class_exists('PFavatar');
        
        if(!$date) $date = time();
        
        $form   = new PFform();
        $date   = PFformat::ToDate($date);
        $my     = PFuser::GetInstance();
        $config = PFconfig::GetInstance();
        
        $avatar = "";
        if($avc) $avatar = PFavatar::Display($my->GetId());
        
        $use_ce = (int) $config->Get('use_ce', 'comments');

        if($use_ce && !defined('PF_DEMO_MODE')) {
            $area = $editor->display( 'ctext',  $content , '100%', '350', '75', '20' ) ;
            $sb = '
            <script type="text/javascript">
            function save_pfcomment()
            {
                '.$editor->save( 'ctext' ).'
                document.'.$formname.'.submit();
            }
            </script>
            <input type="button" class="pf_button" value="'.PFformat::Lang('SAVE').'" onclick="save_pfcomment();"/>';
        }
        else {
            $area = "<textarea name='ctext' class='text' rows='5' cols='40'>$content</textarea>";
            $sb = "<input type='submit' class='pf_button' value='".PFformat::Lang('SAVE')."' onclick=\"save_pfcomment();\"/>";
        }

		$html = "<table class='pf_comments' width='100%' cellspacing='0' cellpadding='0'>";
		$html .= "<tr class='pf_row0'>";
		$html .= "<td class='date' align='center'>".$date."</td>";
		$html .= "<td class='title' valign='top' align='left'><input type='text' class='inputbox' name='title' size='40' value='$title'/></td>";
		$html .= "</tr>";
		$html .= "<tr class='pf_row0'>";
		$html .= "<td class='author' valign='top' align='center' width='10%' nowrap='nowrap'>";
		$html .= "<div>".$avatar."</div>";
		$html .= "<strong>".$my->GetName()."</strong>";
		$html .= "</td>";
		$html .= "<td class='content' valign='top' align='left'>$area</td>";
		$html .= "</tr>";
		
		if(ereg('href','<xmp>'.$form->NavButton(PFformat::lang('NEW_FILE'), 'section=filemanager&dir=&task=form_new_file' , PFformat::Lang('TT_UPLOAD_FILE')).'</xmp>')) {
			$html .= "<tr>";
			$html .= "<td></td>";
			$html .= "<td>";
			
			$html .= "<br/>";
			$html .= "<br/>";
			
			$html .= "		  <fieldset class='adminform'>";
			$html .= "			<legend>".PFformat::Lang('ATTACH_TO_TASKS')."</legend>";
			$html .= "			<table class='admintable'>";
			$html .= "			   <tbody>";			   
			$html .= "			   <tr>";
			$html .= "				  <td id='attachments' valign='top'>";
			$html .= "				  ".$this->select_ws_task('tasks[]').""; 
			$html .= "				  </td>";
			$html .= "			   </tr>";			   
			$html .= "			   </tbody>";
			$html .= "			</table>";
			$html .= "		 </fieldset>";
			
			$html .= "<span class='btn pf_add'><a class='pf_button_submit' href='javascript:addFile(\"add_file\", \"file_container\")'>". PFformat::Lang('ADD')."</a></span>";
			
			$html .= "<br/>";
			$html .= "<br/>";
			
			$html .= "<div id='add_file' style='display:visible'>";
			
			$html .= "  <div class='col'>";
			$html .= "			 <fieldset class='adminform'>";
			$html .= "	    <legend>".PFformat::Lang('GENERAL_INFORMATION')."</legend>";
			$html .= "	     <table class='admintable'>";
			$html .= "				   <tr>";
			$html .= "					  <td class='key required' width='150'>".PFformat::Lang('FILE')."</td>";
			$html .= "					  <td>".$form->FileField('file[]*', '', 'size="40"')."</td>";
			$html .= "				   </tr>";
			$html .= "				   <tr>";
			$html .= "					  <td class='key' width='150' valign='top'>".PFformat::Lang('DESC')."</td>";
			$html .= "					  <td>".$form->InputField('description[]', '', 'size="80" maxlength="124"')."</td>";
			$html .= "				   </tr>";		
			$html .= "				</table>";
			$html .= "			 </fieldset>";
			$html .= "			 <div id='file_container'></div>";
			$html .= "	      </div>";
			
			$html .= "</div>";
			
            $html .= "    <script type='text/javascript'>";			
			$html .= "				function addFile(template_id, target_id)";
			$html .= "	{";
			$html .= "		var template = document.getElementById(template_id).innerHTML;";
			$html .= "				var div = document.createElement('div');";
			$html .= "			div.style.padding = '2px';";
			$html .= "			div.innerHTML = template;";
            $html .= "		document.getElementById(target_id).appendChild(div);";
			$html .= "	}";
			$html .= "    </script>"; 
				
			$html .= "</td>";
			$html .= "</tr>";
		}
		$html .= "<tr class='pf_row0'>";
		$html .= "<td class='save' colspan='2' align='right'>$sb</td>";
		$html .= "</tr>";
		$html .= "</table>";
		
		return $html;
	}
	function select_ws_task($n, $ps = NULL, $p = "")
	{
		$user 		= PFuser::GetInstance();

		$pf_config  = PFconfig::GetInstance();
		$ws         = (int) $user->GetWorkspace();
		$use_ms     = (int) $pf_config->Get('use_milestones', 'tasks');
		$db 		= $db = PFdatabase::GetInstance();
		
		if(!$ws || $ws == 0) {
			return false;
		}

		$h = "<select name='$n' $p><option value='0'>Seleziona Attivita'</option>";

		if(!$use_ms) {
			$query = "SELECT id, title FROM #__pf_tasks WHERE project = '$ws' ORDER BY title ASC";
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			if(!is_array($rows)) { $rows = array(); }

			foreach ($rows AS $row)
			{
				$s = '';
				if( $ps == $row->id) { $s = "selected='selected'"; }

				$h .= "<option value='$row->id' $s>".htmlspecialchars($row->title)."</option>";
			}
		}
		else if (JRequest::getVar('id'))
		{
			$task_bd = JRequest::getVar('id');

			$query = "SELECT t.id, t.title, t.milestone, m.id AS ms_id, m.title AS ms_title FROM #__pf_tasks AS t"
			. "\n LEFT JOIN #__pf_milestones AS m ON m.id = t.milestone"
			. "\n WHERE t.project = '$ws'"
			. "\n AND t.id = '$task_bd'"
			. "\n ORDER BY ms_id, t.title ASC";

			$db->setQuery($query);
			$rows = $db->loadObjectList();

			if(!is_array($rows)) { $rows = array(); }

			$h = "<select name='$n' $p>";
			$current_ms = 0;
			foreach ($rows AS $i => $row)
			{
				$s = '';
				if( $ps == $row->id) { $s = "selected='selected'"; }

				$h .= "<option value='$row->id' $s>".htmlspecialchars($row->title)."</option>";

				$current_ms = $row->ms_id;
				if($row->ms_id != $current_ms) {
					$h .= "</optgroup>";
				}
			}
		}
		else {
			$query = "SELECT t.id, t.title, t.milestone, m.id AS ms_id, m.title AS ms_title FROM #__pf_tasks AS t"
			. "\n LEFT JOIN #__pf_milestones AS m ON m.id = t.milestone"
			. "\n WHERE t.project = '$ws'"
			. "\n ORDER BY ms_id, t.title ASC";
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			if(!is_array($rows)) { $rows = array(); }

			$current_ms = 0;
			foreach ($rows AS $i => $row)
			{
				if($i == 0) {
					$h .= "<optgroup label='".PFformat::Lang('PFL_UNCATEGORIZED')."'>";
				}
				if($row->ms_id != $current_ms) {
					$h .= "<optgroup label='".htmlspecialchars($row->ms_title)."'>";
				}

				$s = '';
				if( $ps == $row->id) { $s = "selected='selected'"; }

				$h .= "<option value='$row->id' $s>".htmlspecialchars($row->title)."</option>";

				$current_ms = $row->ms_id;
				if($row->ms_id != $current_ms) {
					$h .= "</optgroup>";
				}
			}
		}

		$h .= "</select>";

		return $h;
	}
	
	public function RenderEdit($id)
	{
		$row = $this->Load($id);
		
		return $this->RenderNew($row->title, $row->content, $row->cdate);
	}
	
	public function RenderList()
	{
		$rows = $this->LoadList();
		$form = new PFform();
		
		$load = PFload::GetInstance();
		$user = PFuser::GetInstance();
		$core = PFcore::GetInstance();
		$avc  = class_exists('PFavatar');
		
		$k    = 0;
		$flag = $user->GetFlag();
		
		$edit_image = $load->ThemeImg('reply.png');
		$del_image  = $load->ThemeImg('action_delete.png');

		$html = "<a id='cform'></a><table class='pf_comments' width='100%' cellspacing='0' cellpadding='0'>";
		
		foreach ($rows AS $row)
		{
			// edit link
			if($flag == 'system_administrator' || $user->GetId() == $row->author) {
				$edit_link = PFformat::Link("section=".$core->GetSection()."&task=form_edit_comment&id=".$this->itemid."&cid=".$row->id)."#cform";
			    $edit_link = "<a href='$edit_link' class='pf_button'>$edit_image</a>";
			}
			else {
				$edit_link = "";
			}
			
			// delete link
			if($flag == 'system_administrator' || $user->GetId() == $row->author) {
				$del_link = PFformat::Link("section=".$core->GetSection()."&task=task_delete_comment&id=".$this->itemid."&cid=".$row->id);
			    $del_link = "<a href='$del_link' class='pf_button'>$del_image</a>";
			}
			else {
				$del_link = "";
			}

            if($user->Access('display_details', 'profile', $row->author)) {
                $plink_s = "<a href='".PFformat::Link("section=profile&task=display_details&id=$row->author")."'>";
                $plink_e = "</a>";
            }
            else {
                $plink_s = "";
		        $plink_e = "";
            }
            
            $avatar = "";
            if($avc) $avatar = PFavatar::Display($row->author);
			
			$html .= "<tr class='pf_row$k'>";
			$html .= "<td class='date' align='center'>".PFformat::ToDate($row->cdate)."</td>";
			$html .= "<td class='title' valign='top' align='left'>$row->title</td>";
			$html .= "</tr>";
			$html .= "<tr class='pf_row$k'>";
			$html .= "<td class='author' valign='top' align='center' width='10%' nowrap='nowrap'>";
			$html .= "<div>".$plink_s.$avatar.$plink_e."</div>";
			$html .= "<strong>".$plink_s.$row->name.$plink_e."</strong>";
			$html .= "</td>";
			$html .= "<td class='content' valign='top' align='left'>".nl2br($row->content)."</td>";
			$html .= "</tr>";
			$html .= "<tr class='pf_row$k'>";
			$html .= "<td class='edit' colspan='2' align='right'><span>".$edit_link.$del_link."</span></td>";
			$html .= "</tr>";
			
			$k = 1 - $k;
		}
		
		if(!count($rows)) {
			$html .= "<tr class='pf_row$k'>";
			$html .= "<td class='content' valign='top' align='center'>".PFformat::Lang('NO_COMMENTS')."</td>";
			$html .= "</tr>";
		}
		
		$html .= "</table>";
		
		return $html;
	}
}
}
?>

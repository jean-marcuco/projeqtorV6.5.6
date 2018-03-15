<?php
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2016 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
 * Contributors : -
 *
 * This file is part of ProjeQtOr.
 * 
 * ProjeQtOr is free software: you can redistribute it and/or modify it under 
 * the terms of the GNU Affero General Public License as published by the Free 
 * Software Foundation, either version 3 of the License, or (at your option) 
 * any later version.
 * 
 * ProjeQtOr is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for 
 * more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * ProjeQtOr. If not, see <http://www.gnu.org/licenses/>.
 *
 * You can get complete code of ProjeQtOr, other resource, help and information
 * about contributors at http://www.projeqtor.org 
 *     
 *** DO NOT REMOVE THIS NOTICE ************************************************/
include_once ("../tool/projeqtor.php");
$id=RequestHandler::getId('id',true);
$dep=new Dependency($id);
$delayDep=$dep->dependencyDelay;
$commentDep=$dep->comment;


?>
<div class="contextMenuDiv" id="contextMenuDiv" style="height:135px;z-index:99999999999;">
  <table >
    <tr>
      <td>
        <span>
        <div style="width:180px;border-radius:1px 1px 0px 0px;">
          <div class="section" style="display: inline-block;width:100%; border-radius:0px" >
            <p  style="text-align:center;color:white;height:20px;font-size:15px;display:inline-block;"><?php echo i18n("operationUpdate");?></p>
          <div style="float:right;">
	             <a onclick="hideDependencyRightClick();" <?php echo formatSmallButton('Mark') ;?></a>
        </div>
       </div>
       </div>

			     <form dojoType="dijit.form.Form" id='dynamicRightClickDependencyForm' 
					       name='dynamicRightClickDependencyForm' onSubmit="return false;" style="padding:5px;">
					     <input id="dependencyRightClickId" name="dependencyRightClickId" type="hidden"
						          value="<?php echo $id;?>" />
				       <label for="dependencyDelay" style="text-align: left;display:inline-block;width:100px;"><?php echo i18n("colDependencyDelay");?>&nbsp;:&nbsp;</label>
					     <input id="delayDependency" name="delayDependency" dojoType="dijit.form.NumberTextBox" 
                   constraints="{min:-999, max:999}" 
	                 style="width:25px; text-align: right;display:inline-block;margin-left:-23px;" 
						       value="<?php echo $delayDep;?>" />
						   <div style="display:inline-block;margin-left:38px;">
				          <a id="dependencyRightClickSave" onclick="saveDependencyRightClick(<?php $typeDep;?>);">
                      <?php echo formatMediumButton('Save') ;?>
                  </a> 
               </div>   
					     <label for="commentDependency" style="text-align: left;"><?php echo i18n("colComment");?>&nbsp;:&nbsp;</label>
					     <input id="commentDependency" name="commentDependency"  dojoType="dijit.form.Textarea"
						             value="<?php echo $commentDep;?>" />                        
				   </form>

				  <div style="width:180px;height:25px;"> 
				     <label for="removeDependency" style="height:25px;margin-top:7px;"><?php echo i18n("deleteButton");?>&nbsp;:&nbsp;</label>               		      		 
	          <div style="float:right;">		 
	               <a onclick="removeDependencyRightClick();" <?php echo formatMediumButton('Remove') ;?></a>	
	          </div>
			    </div> 
			    	
			    
			    
			 </span>
			</td>
		</tr>
    <span></span>

  </table>
</div>
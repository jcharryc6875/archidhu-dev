<?php

/**
 * @author 
 * @copyright 2008
 */

class myexport{
	
   public function myexport(){
	
	$c = new Criteria();  	
	$rs = WfInstanciaBitacoraPeer::doSelect( $c);
	/*
	 foreach($rs as $row){
              $result = $row-> ;
      }
      */
	return $rs;
   }	
	
};

?>
<?php
/*
 * created on 10/10/2013
 * To get all the products for PIM studio
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class GetCSDataProductForExport extends CSPms {
 
   public function getParentProducts() {
     $aParentProductList = array();
     $filter = 'ParentID = 0';
     $aOutputResult = parent::getProducts($filter);
     foreach ($aOutputResult as $productObj) {
         $aParentProductList[] = $productObj;
     }
      return $aParentProductList;
    } 
    
    public function getChildrenForGivenProduct($oInputProduct){
        $aChildrenProductList = array();
        $aChildrenIdProductList = array($oInputProduct->getChildrenIDs());
        foreach ($aChildrenIdProductList[0] as $ichildId) {
            $aChildrenProductList[] = parent::getProduct($ichildId);
        }
        return $aChildrenProductList;
    }
    
    public function getProductInformationForGivenProduct($oInputProduct){
        //$productInformationList = array();
        $productid = $oInputProduct->getID();
        $label = $oInputProduct->getLabel();
        $externalkey = $oInputProduct->getExternalKey();
        $externalkey = ($externalkey==null||"")?NULL:$externalkey;
        $owner = $oInputProduct->getOwner();
        $productInformationList = array ("ID"=>$productid,"Label"=>$label,"ExternalKey"=>$externalkey,"Owner"=>$owner);
        return $productInformationList;
    }
    
    public function getAllAttributesForGivenProduct($oInputProduct){
        $oAttributeList = array();
        $oAttributeIds = array($oInputProduct->getFieldIDs());
        foreach($oAttributeIds[0] as $iattributeid){
            $oattribute = $oInputProduct->getField($iattributeid);
            $aName = $oattribute->getLabel();
            $aValue = $oInputProduct->getValue($iattributeid,null,true,CSITEM_VALUES_FORMATTED,0);
           $oAttributeList[] = array ("attributeId"=>$iattributeid,"attributeName"=>$aName,"attributeValue"=>$aValue); 
        }
        return $oAttributeList;
    }
    
    
    public function  getParentId($child)
    {
    	$parent = $child->getParent();
    	$parentId = $parent->getID();
    	$parentId = ($parentId==0||"")?NULL:$parentId;
    	return $parentId;
    }
  public function getParentLabel($child)
    {
    	$parent = $child->getParent();
    	$parentId = $parent->getLabel();
    	$parentId = ($parentId==null||"")?NULL:$parentId;
    	return $parentId;
    }
}

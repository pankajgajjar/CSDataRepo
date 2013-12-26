<?php
/*
 * created on 10/10/2013
 * To get all the products for PIM studio
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class CSGetDataProductForExport extends CSPms
{


    /*
     * Gets the Parent Products in the Hierarchy
     * Returns Array of all the Parent Products
     * @access public
     */
    public function getParentProducts()
    {
        $aParentProductList = array();
        $sFilter = 'ParentID = 0';
        $aOutputResult = parent::getProducts($sFilter);
        foreach ($aOutputResult as $oProductObj) {
            $aParentProductList[] = $oProductObj;
        }
        return $aParentProductList;
    }

    /*
     * Returns Children of the Given Products
     * @param object $oInputProduct Specifies the Product for which the CHildren are needed
     * @access public
     */
    public function getChildrenForGivenProduct($oInputProduct)
    {
        $aChildrenProductList = $oInputProduct->getChildren();
        return $aChildrenProductList;
        // print_r($oInputProduct->getChildren());
        // $aChildrenIdProductList = array($oInputProduct->getChildrenIDs());
        // foreach ($aChildrenIdProductList[0] as $iChildId) {
        //    $aChildrenProductList[] = parent::getProduct($iChildId);
        //}
        //print_r(sizeof($aChildrenProductList).'---');

    }

    /*
      * Returns Information(id,label,external key,owner) of the Given Products
      * @param object $oInputProduct Specifies the Product for which the Information is needed
      * @access public
      */
    public function getProductInformationForGivenProduct($oInputProduct)
    {
        $sProductid = $oInputProduct->getID();
        $sLabel = $oInputProduct->getLabel();
        $sExternalkey = $oInputProduct->getExternalKey();
        $sExternalkey = ($sExternalkey == null || "") ? NULL : $sExternalkey;
        $sOwner = $oInputProduct->getOwner();
        $aproductInformationList = array("ID" => $sProductid, "Label" => $sLabel, "ExternalKey" => $sExternalkey, "Owner" => $sOwner);
        return $aproductInformationList;
    }


    /*
     * Returns All Attributes of the Given Products
     * @param object $oInputProduct Specifies the Product for which the Attributes are needed
     * @access public
     */

    public function getAllAttributesForGivenProduct($oInputProduct)
    {
        $oAttributeList = array();
        $oAttributeIds = array($oInputProduct->getFieldIDs());
        foreach ($oAttributeIds[0] as $iAttributeid) {

            $oAttribute = $oInputProduct->getField($iAttributeid);
            $aName = $oAttribute->getLabel();
            $aValue = $oInputProduct->getValue($iAttributeid, null, true, CSITEM_VALUES_FORMATTED, 0);
            $oAttributeList[] = array("attributeId" => $iAttributeid, "attributeName" => $aName, "attributeValue" => $aValue);
        }

        return $oAttributeList;
    }

    /*
     * Returns Parent Product of the Given Products
     * @param object $oChild specifies product for which you want the parent for
     * @access public
     */
    public function  getParentId($oChild)
    {
        $oParent = $oChild->getParent();
        $sParentId = $oParent->getID();
        $sParentId = ($sParentId == 0 || "") ? NULL : $sParentId;
        return $sParentId;
    }

    /*
     * Returns Parent Label of the Given Products
     * @param object $oChild specifies product for which you want the Label for
     * @access public
     */
    public function getParentLabel($oChild)
    {
        $oParent = $oChild->getParent();
        $sParentId = $oParent->getLabel();
        $sParentId = ($sParentId == null || "") ? NULL : $sParentId;
        return $sParentId;
    }

    public function getValuesofProduct($oChild)
    {
        $attributesList = $oChild->getFormattedValues();
        return $attributesList;

    }
    
    public function getAttributeName($map,$id)
    {
    	return $map[$id];
    }
}

<?php //00e57
// *************************************************************************
// *                                                                       *
// * WHMCS - The Complete Client Management, Billing & Support Solution    *
// * Copyright (c) WHMCS Ltd. All Rights Reserved,                         *
// * Version: 5.3.14 (5.3.14-release.1)                                    *
// * BuildId: 0866bd1.62                                                   *
// * Build Date: 28 May 2015                                               *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * Email: info@whmcs.com                                                 *
// * Website: http://www.whmcs.com                                         *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * This software is furnished under a license and may be used and copied *
// * only  in  accordance  with  the  terms  of such  license and with the *
// * inclusion of the above copyright notice.  This software  or any other *
// * copies thereof may not be provided or otherwise made available to any *
// * other person.  No title to and  ownership of the  software is  hereby *
// * transferred.                                                          *
// *                                                                       *
// * You may not reverse  engineer, decompile, defeat  license  encryption *
// * mechanisms, or  disassemble this software product or software product *
// * license.  WHMCompleteSolution may terminate this license if you don't *
// * comply with any of the terms and conditions set forth in our end user *
// * license agreement (EULA).  In such event,  licensee  agrees to return *
// * licensor  or destroy  all copies of software  upon termination of the *
// * license.                                                              *
// *                                                                       *
// * Please see the EULA file for the full End User License Agreement.     *
// *                                                                       *
// *************************************************************************
/**
 * Create a Solution Collection
 */
class WHMCS_Payment_Solutions extends ArrayObject
{
    protected $iterator = NULL;
    const TYPE_GATEWAY = 'gateway';
    const TYPE_ALTERNATE = 'alternate';
    const TYPE_MULTI = 'allinone';
    /**
     * Constructor
     *
     * @return WHMCS_Payment_Solutions
     */
    public function __construct()
    {
        return $this;
    }
    public function getIterator()
    {
        if( !$this->iterator )
        {
            $this->restoreDefaultIterator();
        }
        return $this->iterator;
    }
    protected function restoreDefaultIterator()
    {
        $this->setIterator(new ArrayIterator($this->getArrayCopy()));
        foreach( $this->iterator as $v )
        {
        }
        return $this;
    }
    public function setIterator($iterator)
    {
        if( !$iterator instanceof CachingIterator )
        {
            $iterator = new CachingIterator($iterator, CachingIterator::FULL_CACHE);
        }
        foreach( $iterator as $v )
        {
        }
        $this->iterator = $iterator;
        return $this;
    }
    public function count()
    {
        return $this->getIterator()->count();
    }
    public function loadSolutionsInDirectory($directory)
    {
        $dirIterator = new DirectoryIterator($directory);
        $solutionSet = array(  );
        foreach( $dirIterator as $resource )
        {
            if( $resource->isFile() && !$resource->isLink() && $resource->getExtension() == 'php' && $resource->getBasename() != 'index' )
            {
                try
                {
                    $solution = $this->getAdapterFromFile($resource);
                    $solutionSet[$solution->getName()] = $solution;
                }
                catch( WHMCS_Payment_Exceptions_InvalidModuleException $e )
                {
                    trigger_error($e->getMessage(), E_USER_WARNING);
                }
            }
        }
        $this->exchangeArray($solutionSet);
        return $this;
    }
    /**
     *
     * NOTE: this assumes the classic "modules/gateways/${gateway}.php"
     *
     * @param SplFileInfo $resource
     *
     * @return WHMCS_Payment_Adapter_AdapterInterface
     */
    public function getAdapterFromFile($resource)
    {
        $basename = $resource->getBasename(".php");
        $resource_classname = $basename . 'PaymentSolution';
        $moduleIncludeFile = $resource->getPathname();
        global $CONFIG;
        global $whmcs;
        global $GATEWAYMODULE;
        $whmcs = WHMCS_Application::getinstance();
        $CONFIG = $whmcs->getApplicationConfig();
        include_once($moduleIncludeFile);
        if( class_exists($resource_classname) )
        {
            $adapter = new $resource_classname();
            if( !$adapter instanceof WHMCS_Payment_Adapter_AdapterInterface )
            {
                throw new WHMCS_Payment_Exceptions_InvalidModuleException(sprintf("Payment solution module class '%s' does not implement %s", $resource_classname, 'WHMCS_Payment_Adapter_AdapterInterface'));
            }
        }
        else
        {
            $adapter = new WHMCS_Payment_Adapter_GatewaysModuleAdapter($basename);
        }
        return $adapter;
    }
    public static function getValidSolutionTypes()
    {
        return array( self::TYPE_MULTI, self::TYPE_GATEWAY, self::TYPE_ALTERNATE );
    }
    public static function isValidSolutionType($type)
    {
        $validOptions = self::getvalidsolutiontypes();
        return in_array($type, $validOptions);
    }
    /**
     * Apply a filter
     *
     * @param WHMCS_Payment_Filter_FilterInterface $filter
     *
     * @return FilterIterator The iterator with attached dataset
     */
    public function applyFilter($filter)
    {
        $iterator = $filter->getFilteredIterator($this->getIterator());
        $this->setIterator($iterator);
        return $iterator;
    }
    /**
     * Remove all filters
     *
     * NOTE: Because applyFilter() actually applies a filter to the current
     * iterator, any removed filter will wrapper previous filters (iterators)
     *
     * In the event that no filters existed, the interal iterator will be returned
     *
     * @return Iterator The first filter removed or the internal iterator
     */
    public function removeAllFilters()
    {
        $filter = $this->getIterator()->getInnerIterator();
        $this->restoreDefaultIterator();
        return $filter;
    }
    /**
     *
     * In the event that no filters existed, the interal iterator will be returned
     *
     * @return Iterator The removed filter (WHMCS_Payment_Filter_FilterInterface) or the internal iterator (ArrayIterator)
     */
    public function removeFilter()
    {
        $iterator = $this->getIterator();
        $innerIterator = $iterator->getInnerIterator();
        if( $innerIterator instanceof FilterIterator )
        {
            $iteratorToSet = $innerIterator->getInnerIterator();
            $iteratorToReturn = $innerIterator;
        }
        else
        {
            $iteratorToReturn = $iteratorToSet = $innerIterator;
        }
        $this->setIterator($iteratorToSet);
        return $iteratorToReturn;
    }
}
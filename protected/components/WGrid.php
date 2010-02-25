<?php
/**
 * WGrid class file.
 * WGrid is a widget displaying jqGrid or a static grid.
 */
class WGrid extends CWidget
{
    /**
     * @var array of the jqGrid columns
     */
    public $columns;

    /**
     * @var array of the jqGrid colModel, defines the look
     * of each column and the whole table, sets width and all other properties.
     * The total width should be area-2px, e.g. 698.
     */
    public $columnsModel;

    /**
     * @var string id of the controller, is necessary for the delete and other buttons.
     */
    public $controllerId;

    /**
     * @var string data type used by the data grid page to return to the jqGrid,
     * usually 'json' or 'xml'.
     */
    public $datatype;

    /**
     * @var boolean whether display the button close in the static grid titlebar
     */
    public $displayButtonClose;

    /**
     * @var boolean whether display the jqGrid,
     * default is true.
     */
    public $displayGrid;

    /**
     * @var boolean whether display the static grid,
     * default is true.
     */
    public $displaySGrid;

    /**
     * @var boolean whether display the static grid pager,
     * should be displayed in the most cases, default is true.
     */
    public $displaySGridPager;

    /**
     * @var boolean whether display the static grid titlebar,
     * default is true.
     */
    public $displaySTitlebar;

    /**
     * @var boolean whether display the jqGrid titlebar,
     * default is true.
     */
    public $displayTitlebar;

    /**
     * @var string id of the jqGrid,
     * default is 'w3Grid'.
     */
    public $gridId;

    /**
     * @var string id of the jqGrid pager,
     * default is 'w3GridPager'.
     */
    public $gridPagerId;

    /**
     * @var boolean whether grid has a link icon (show, edit, and so on),
     * so we need to register the hover effects script for it,
     * default is true.
     */
    public $hasLinkIcon;

    /**
     * @var integer total height of the jqGrid rows area, in pixels,
     * default is 500.
     */
    public $height;

    /**
     * @var array of important rows that should be displayed in the bottom
     * of the static grid. this variable is the array of rows (<tr>), each row
     * is an array of columns (<td>). Content of these rows will be displayed
     * bold, with a background like the pager bar.
     */
    public $importantRowsBottom;

    /**
     * @var integer maximum row's number of the static grid
     */
    public $maxRow;

    /**
     * @var integer minimum row's number of the static grid
     */
    public $minRow;

    /**
     * @var string method type used to request jqGrid data from the grid data page,
     * should be 'GET' or 'POST', default is 'POST'.
     */
    public $mtype;

    /**
     * @var object instance of {@link CPagination}, optional,
     * may be used to calculate maxRow, minRow, totalRecords.
     */
    public $pages;

    /**
     * @var boolean whether register script for the grid link icon,
     * such as delete button, default is true.
     */
    public $registerGridLinkIcon;

    /**
     * @var integer number of rows to display in the jqGrid,
     * default is 50.
     */
    public $rowNum;

    /**
     * @var string list of numbers of rows per page allowed by jqGrid to display,
     * default is '[25,50,100,200]'.
     */
    public $rowList;

    /**
     * @var array of the static grid rows data - array or rows (<tr>),
     * each row is an array of columns (<td>). 
     */
    public $rows;

    /**
     * @var array of the static grid columns
     */
    public $sColumns;

    /**
     * @var string id of the static grid,
     * default is 'w3StaticGrid'.
     */
    public $sGridId;

    /**
     * @var string id of the static grid wrapper,
     * default is 'w3StaticGridWrapper'.
     */
    public $sGridWrapperId;

    /**
     * @var string name of the jqGrid sort field
     */
    public $sortname;

    /**
     * @var string sort order for the jqGrid sort field,
     * should be 'asc' or 'desc', default is 'asc'.
     */
    public $sortorder;

    /**
     * @var string title of the static grid
     */
    public $sTitle;

    /**
     * @var string title of the jqGrid
     */
    public $title;

    /**
     * @var integer total number of existing records,
     * used for the static grid pager bar.
     */
    public $totalRecords;

    /**
     * @var string url of the grid data page,
     * default is controllerId/gridData.
     */
    public $url;

    /**
     * @var boolean whether display the number of total records
     * in the pager bar of the jqGrid, default is true.
     */
    public $viewrecords;

    /**
     * When widget is called, following function is run.
     */
    public function run()
    {
        // set the default values and validate the data
        if($this->displayGrid===null)
            $this->displayGrid=$this->columns!==null && $this->columnsModel!==null;
        if($this->displaySGrid===null)
            $this->displaySGrid=$this->sColumns!==null && $this->rows!==null;
        if($this->title===null)
            $this->title=CHtml::encode(MParams::getPageLabel());
        else
            $this->title=(string)$this->title;
        // jqGrid
        if($this->displayGrid)
        {
            if(!is_array($this->columns))
                $this->columns=array();
            if(!is_array($this->columnsModel))
                $this->columnsModel=array();
            if($this->datatype===null)
                $this->datatype='json';
            if($this->displayTitlebar===null)
                $this->displayTitlebar=true;
            if($this->gridId===null)
                $this->gridId='w3Grid';
            if($this->gridPagerId===null)
                $this->gridPagerId=$this->gridId.'Pager';
            if($this->height===null)
                $this->height=500;
            else
                $this->height=(int)$this->height;
            if($this->mtype===null)
                $this->mtype='POST';
            if($this->rowList===null)
                $this->rowList='[25,50,100,200]';
            if($this->rowNum===null)
                $this->rowNum=_CController::GRID_PAGE_SIZE;
            $this->rowNum=(int)$this->rowNum;
            $this->sortname=(string)$this->sortname;
            if($this->sortorder===null)
                $this->sortorder='asc';
            if($this->url===null)
                $this->url=Yii::app()->urlManager->createUrl(Yii::app()->controller->id.'/gridData');
            $this->url=(string)$this->url;
            if($this->viewrecords===null)
                $this->viewrecords=true;
        }
        // static grid
        if($this->displaySGrid)
        {
            if(!is_array($this->rows))
                $this->rows=array();
            if($this->controllerId===null)
                $this->controllerId=Yii::app()->controller->id;
            if($this->displayButtonClose===null)
                $this->displayButtonClose=false;
            if($this->displaySGridPager===null)
                $this->displaySGridPager=true;
            if($this->displaySTitlebar===null)
                $this->displaySTitlebar=true;
            if($this->hasLinkIcon===null)
                $this->hasLinkIcon=true;
            if(!is_array($this->importantRowsBottom))
                $this->importantRowsBottom=array();
            if($this->maxRow===null && $this->pages instanceof CPagination)
            {
                $maxRow=$this->pages->getCurrentPage()*$this->pages->getPageSize()+count($this->rows); // ($this->pages->getCurrentPage()+1)*$this->pages->getPageSize()
                $this->maxRow=$maxRow > $this->pages->getItemCount() ? $this->pages->getItemCount() : $maxRow;
            }
            else
                $this->maxRow=(int)$this->maxRow;
            if($this->minRow===null && $this->pages instanceof CPagination)
                $this->minRow=$this->pages->getCurrentPage()*$this->pages->getPageSize()+1;
            else
                $this->minRow=(int)$this->minRow;
            if($this->registerGridLinkIcon===null)
                $this->registerGridLinkIcon=true;
            if(!is_array($this->sColumns))
                $this->sColumns=array();
            if($this->sGridId===null)
                $this->sGridId='w3StaticGrid';
            if($this->sGridWrapperId===null)
                $this->sGridWrapperId=$this->sGridId.'Wrapper';
            if($this->sTitle===null)
                $this->sTitle=$this->title;
            if($this->totalRecords===null && $this->pages instanceof CPagination)
                $this->totalRecords=$this->pages->getItemCount();
            else
                $this->totalRecords=(int)$this->totalRecords;
        }
        // data for the renderer
        $data=array(
            'columns'=>$this->columns,
            'columnsModel'=>$this->columnsModel,
            'controllerId'=>$this->controllerId,
            'datatype'=>$this->datatype,
            'displayButtonClose'=>$this->displayButtonClose,
            'displayGrid'=>$this->displayGrid,
            'displaySGrid'=>$this->displaySGrid,
            'displaySGridPager'=>$this->displaySGridPager,
            'displayTitlebar'=>$this->displayTitlebar,
            'displaySTitlebar'=>$this->displaySTitlebar,
            'gridId'=>$this->gridId,
            'gridPagerId'=>$this->gridPagerId,
            'hasLinkIcon'=>$this->hasLinkIcon,
            'height'=>$this->height,
            'importantRowsBottom'=>$this->importantRowsBottom,
            'maxRow'=>$this->maxRow,
            'minRow'=>$this->minRow,
            'mtype'=>$this->mtype,
            'registerGridLinkIcon'=>$this->registerGridLinkIcon,
            'rowList'=>$this->rowList,
            'rowNum'=>$this->rowNum,
            'rows'=>$this->rows,
            'sColumns'=>$this->sColumns,
            'sGridId'=>$this->sGridId,
            'sGridWrapperId'=>$this->sGridWrapperId,
            'sortname'=>$this->sortname,
            'sortorder'=>$this->sortorder,
            'sTitle'=>$this->sTitle,
            'title'=>$this->title,
            'totalRecords'=>$this->totalRecords,
            'url'=>$this->url,
            'viewrecords'=>$this->viewrecords,
            'colModel','colNames','i','n'
        );
        // render the view file
        $this->render('wGrid',$data);
    }
}
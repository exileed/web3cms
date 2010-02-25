<?php
/**
 * WLinkList class file.
 * WLinkList is a widget displaying a list of the links.
 */
class WLinkList extends CWidget
{
    /**
     * @var string area where this widget
     * is being displayed in, e.g. 'sidebar2'.
     */
    public $area;

    /**
     * @var boolean whether display the list titlebar,
     * default is true.
     */
    public $displayTitlebar;

    /**
     * @var string identificator of the box where this widget
     * is being displayed in, e.g. 'sidebar'.
     */
    public $id;

    /**
     * @var string title of the list
     */
    public $title;

    /**
     * When widget is called, following function is run.
     */
    public function run()
    {
        // retrieve data from the global storage
        $data=MLinkList::get($this->id);
        // the new array of the links is a validated one
        $links=array();
        if(isset($data['links']) && is_array($data['links']))
        {
            foreach($data['links'] as $link)
            {
                if(isset($link['visible']) && !$link['visible'])
                    continue;
                if(is_array($link) && (isset($link['text']) || isset($link['url']) || isset($link['icon']) || isset($link['options'])))
                {
                    $links[]=array(
                        'text'=>isset($link['text']) ? (string)$link['text'] : '',
                        'url'=>(isset($link['url']) && (is_array($link['url']) || is_string($link['url']))) ? $link['url'] : '#',
                        'options'=>(isset($link['options']) && is_array($link['options'])) ? $link['options'] : array()
                    );
                    $i=count($links)-1;
                    if(isset($link['icon']) && (is_string($link['icon']) || is_numeric($link['icon'])))
                        $links[$i]['icon']=$link['icon'];
                    if(!isset($links[$i]['options']['title']))
                        $links[$i]['options']['title']=$links[$i]['text'];
                }
            }
        }
        // do not display the widget if the data has no links
        if(($c=count($links))===0)
            return null;
        // set the default values and validate the data
        $area=$this->area===null ? $this->id : $this->area;
        if($this->displayTitlebar===null)
            $this->displayTitlebar=isset($data['displayTitlebar']) ? (bool)$data['displayTitlebar'] : true;
        if($this->title===null)
            $this->title=isset($data['title']) ? (string)$data['title'] : Yii::t('t','Related links');
        else
            $this->title=(string)$this->title;
        // data for the renderer
        $data=array(
            'area'=>$area,
            'c'=>$c,
            'displayTitlebar'=>$this->displayTitlebar,
            'links'=>$links,
            'title'=>$this->title,
            'class','n'
        );
        // render the view file
        $this->render('wLinkList',$data);
        // update the layout statistics
        if($area==='sidebar1')
            MLayout::incrementNumberOfItemsSidebar1();
        else if($area==='sidebar2')
            MLayout::incrementNumberOfItemsSidebar2();
    }
}
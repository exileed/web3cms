<?php
/**
 * WPreItemActionBar is a widget displaying list of related links before a block item.
 * 
 * The block item can be anything: list, grid, show-item
 * and other html blocks that are treated as full width widgets.
 * The list of related links is usually not more than 1-3 buttons,
 * placed horizontally right above the block item, aligned right and
 * are related to the displaying page and/or the block item. If there
 * is an icon in the jquery-ui that fits the current link needs,
 * then we display it in the left of the link label, inside the link cell.
 */
class WPreItemActionBar extends CWidget
{
    /**
     * @var array of the links
     */
    public $links;

    /**
     * When widget is called, following function is run.
     */
    public function run()
    {
        // the new array of the links is a validated one
        $links=array();
        if(is_array($this->links))
        {
            foreach($this->links as $link)
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
                    if(isset($link['dropDown']))
                    {
                        $links[$i]['dropDown']=$link['dropDown'];
                        if($links[$i]['url']==='#')
                            $links[$i]['url']='javascript:void(0)';
                    }
                }
            }
        }
        // do not display the widget if the data has no links
        if(($c=count($links))===0)
            return null;
        // data for the renderer
        $data=array(
            'c'=>$c,
            'links'=>$links,
            'class','n'
        );
        // render the view file
        $this->render('wPreItemActionBar',$data);
    }
}
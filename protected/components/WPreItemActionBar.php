<?php
/**
 * WPreItemActionBar is a widget displaying list of related links before a block item.
 * 
 * The block item can be anything: items-list, items-grid, show-item
 * and other html blocks that are treated as full width widgets.
 * The list of related links is usually not more than 1-3 buttons,
 * placed horizontally right above the block item, aligned right and
 * are related to the displaying page and/or the block item. If there
 * is an icon in the jquery-ui that fits the current link needs,
 * then we display it in the left of the link label, inside the link cell.
 */
class WPreItemActionBar extends CWidget
{
    public $links;

    public function run()
    {
        // links array should not be empty
        if(!is_array($this->links) || ($c=count($this->links))===0)
            return null;
        $links=array();
        // carefully validate links parameter
        foreach($this->links as $link)
        {
            if(is_array($link) && (isset($link['text']) || isset($link['url']) || isset($link['icon']) || isset($link['options'])))
            {
                $links[]=array(
                    'text'=>isset($link['text']) ? (string)$link['text'] : '',
                    'url'=>(isset($link['url']) && (is_array($link['url']) || is_string($link['url']))) ? $link['url'] : '#',
                    'options'=>(isset($link['options']) && is_array($link['options'])) ? $link['options'] : array()
                );
                if(isset($link['icon']) && (is_string($link['icon']) || is_numeric($link['icon'])))
                    $links[count($links)-1]['icon']=$link['icon'];
            }
        }
        // register script for visual effects
        MClientScript::registerScript('w3ActionButton');
        // render the view file
        $this->render('wPreItemActionBar',array('links'=>$links,'c'=>$c,'n','liClass'));
    }
}
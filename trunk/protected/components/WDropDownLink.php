<?php
/**
 * WDropDownLink class file.
 * WDropDownLink is a widget displaying a drop down link -
 * link with a drop down menu.
 */
class WDropDownLink extends CWidget
{
    /**
     * @var array of the drop down links.
     */
    public $links;

    /**
     * @var array of the link html options.
     */
    public $options;

    /**
     * @var string the link body.
     */
    public $text;

    /**
     * @var mixed an URL (string) or an action route (array).
     */
    public $url;

    /**
     * When widget is called, following function is run.
     */
    public function run()
    {
        // set the default values and validate the data
        if(!is_array($this->options))
            $this->options=array();
        $this->text=(string)$this->text;
        if(!is_string($this->url) && !is_array($this->url))
            $this->url='javascript:void(0)';
        // the new array of the links is a validated one
        $links=array();
        if(is_array($this->links))
        {
            foreach($this->links as $link)
            {
                if(is_array($link) && (isset($link['text']) || isset($link['url']) || isset($link['active']) || isset($link['options'])))
                {
                    $links[]=array(
                        'text'=>isset($link['text']) ? (string)$link['text'] : '',
                        'url'=>(isset($link['url']) && (is_array($link['url']) || is_string($link['url']))) ? $link['url'] : '#',
                        'active'=>isset($link['active']) ? (boolean)$link['active'] : false,
                        'options'=>(isset($link['options']) && is_array($link['options'])) ? $link['options'] : array()
                    );
                }
            }
        }
        // data for the renderer
        $data=array(
            'c'=>count($links),
            'options'=>$this->options,
            'links'=>$links,
            'text'=>$this->text,
            'url'=>$this->url,
        );
        // render the view file
        $this->render('wDropDownLink',$data);
    }
}
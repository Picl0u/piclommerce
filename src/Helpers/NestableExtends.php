<?php
namespace Piclou\Piclommerce\Helpers;

use Nestable\Services\NestableService;

class NestableExtends extends NestableService
{

    protected $liAttr;

    protected $liSortable = false;

    public function renderAsHtml($data = false, $parent = 0, $first = true)
    {
        $args = $this->setParameters(func_get_args());

        // open the ul tag if function is first run
        $tree = $first ? $this->ul(null, $parent, true) : '';

        $args['data']->each(function ($child_item) use (&$tree, $args) {

            $childItems = '';

            if (intval($child_item[$this->parent]) == intval($args['parent'])) {
                $path = $child_item[$this->config['html']['href']];
                $label = $child_item[$this->config['html']['label']];
                // find parent element
                $parentNode = $args['data']->where('id', (int)$child_item[$this->config['parent']])->first();

                $currentData = [
                    'label' => $label,
                    'href'  => $this->customUrl ? $this->makeUrl($path) : $this->urlCustom($path, $label, $parentNode, $child_item['id']),
                    'image' => (isset($child_item['image']))?$child_item['image']:null
                ];

                // Check the active item
                $activeItem = $this->doActive($path, $label);
                // open the li tag
                $liAttr = "";
                if (!empty($this->liAttr)){
                    foreach ($this->liAttr as $keyAttr =>$valueAttr){
                        if (isset($child_item[$valueAttr]) && !empty($child_item[$valueAttr])){
                            $liAttr = ' '.$keyAttr.'="'.$child_item[$valueAttr].'"';
                        } else {
                            $liAttr = ' '.$keyAttr.'="'.$valueAttr.'"';
                        }
                    }
                }
                if ($this->liSortable == true) {
                    $liAttr = ' id="menuItem_'.$child_item['id'].'"';
                }
                $childItems .= $this->openLiCustom($currentData, $activeItem, $liAttr);
                // Get the primary key name
                $item_id = $child_item[$this->config['primary_key']];

                // check the child element
                if ($this->hasChild($this->parent, $item_id, $args['data'])) {

                    // function call again for child elements
                    $html = $this->renderAsHtml($args['data'], $item_id, false);

                    if (!empty($html)) {
                        $childItems .= $this->ul($html, $item_id);
                    }
                }

                // close the li tag
                $childItems = $this->closeLi($childItems);
            }

            // current data contact to the parent variable
            $tree = $tree.$childItems;

        });

        // close the ul tag
        $tree = $first ? $this->closeUl($tree) : $tree;

        return $tree;
    }

    /**
     * @return $this
     */
    public function liSortable()
    {
        $this->liSortable = true;
        return $this;
    }

    /**
     * Generate open li tag.
     *
     * @param array $li
     *
     * @return string
     */
    public function openLiCustom(array $li, $extra = '', string $liAttr)
    {
        $html = "\n".'<li '.$extra.' '.$liAttr.'><a href="'.$li['href'].'">';
        if(!empty($li['image']) && !is_null($li['image'])) {
            $html .='<img src="'. resizeImage($li['image'], false ,145) . '" alt="' . $li['label'] .'">';

        }
        $html .= $li['label'];
        $html .= '</a>';

        return $html;

    }

    /**
     * URL Generator.
     *
     * @param string $path
     *
     * @return string
     */
    protected function urlCustom($path, $label, $parent = null, $id)
    {
        if ($this->config['generate_url']) {
            if ($this->route) {
                if (array_has($this->route, 'callback')) {
                    if(is_array($parent)) $parent = (object)$parent;
                    return call_user_func_array($this->route['callback'], [$path, $label, $parent]);
                } else {
                    end($this->route);
                    $param = current($this->route);
                    $name = key($this->route);
                    return url()->route($name, [$param => $path, 'id' => $id]);
                }
            }

            return url()->to($path);
        }

        return '/'.$path;
    }


}
<?php
namespace Piclou\Piclommerce\Helpers;

class TreeCheckboxes
{

    /**
     * @var string
     */
    private $name;
    /**
     * @var
     */
    private $datas;
    /**
     * @var
     */
    private $data;
    /**
     * @var string
     */
    private $valueName;

    /**
     * TreeCheckboxes constructor.
     * @param string $name
     * @param string $valueName
     * @param $datas
     */
    public function __construct(string $name, string $valueName, $datas)
    {

        $this->name = $name;
        $this->datas = $datas;
        $this->valueName = $valueName;
    }

    /**
     * @param int $id
     * @param $parent
     * @param string $label
     */
    public function addRow(int $id, $parent, string $label)
    {
        $this->data[$parent][] = [
            'id' => $id,
            'label' =>$label,
        ];
    }

    /**
     * @param null $attr
     * @return bool|string
     */
    public function generateList($attr = null)
    {
        return $this->ul(null);
    }

    /**
     * @param int $parent
     * @return bool|string
     */
    private function ul($parent = 0)
    {
        static $i = 1;
        if (isset($this->data[$parent])) {

            $html = '<ul>';
            $i++;
            foreach ($this->data[$parent] as $row) {
                $child = $this->ul($row['id']);
                $html .= '<li>';
                $html .= '<label>';
                $checked = "";
                if($this->datas) {
                    foreach($this->datas as $d) {
                        if($d->{$this->valueName} == $row['id']) {
                            $checked = 'checked="checked"';
                        }
                    }
                }
                $html .= '<input 
                            type="checkbox" 
                            name="' . $this->name .'[]" 
                            value="' . $row['id'] . '" 
                            '.$checked.'
                        >';
                $html .= $row['label'];
                if($child){
                    $html .= $child;
                }
                $html .= '</label'>
                    $html .= '</li>';
            }
            $html .= '</ul>';
            return $html;
        } else {
            return false;
        }
    }

}
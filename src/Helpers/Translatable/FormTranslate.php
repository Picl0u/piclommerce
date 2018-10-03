<?php
namespace Piclou\Piclommerce\Helpers\Translatable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class FormTranslate{
    private $model;

    private $modelname;

    private $translatableInputs = [];

    private $data;

    private $key;

    private $action;

    private $lang = "";
    /**
     * FormTranslate constructor.
     * @param $modelName
     * @param $data
     */
    public function __construct($modelName, $data = null)
    {
        $this->model = new $modelName();
        $model = explode("\\",$modelName);
        $this->modelName = $model[count($model)-1];
        $this->data = $data;
        $this->key = config('piclommerce.translateKey');
        if(!is_null($this->model->translatable())){
            $this->translatableInputs = $this->model->translatable();
        }
    }

    /**
     * @param string $route
     * @return FormTranslate
     */
    public function action(string $route): self
    {
        $this->action =  route($route,[
            'id' => $this->data->id
        ]);
        return $this;
    }
    /**
     *
     * @return string
     */
    public function render(): string
    {
        if(!empty($this->translatableInputs) && !is_null($this->data)){
            $langs = array_diff(config('piclommerce.languages'),[config('app.locale')]);
            if(!empty($langs)) {
                $html = $this->buttons($langs);
                $html .= $this->modal();
                return $html;
            }
        }
        return '';
    }

    public function formRequest(Request $request)
    {
        $translateFields = $this->model->translatable();
        if($request->method() == 'GET') {
            $id = $request->id;
            $lang = $request->lang;
            $data = $this->model->where('id', $id)->firstorFail();
            $translate = [];
            foreach($translateFields as $key => $field){
                $translate[] = [
                    $this->key.'_'.$key => $data->translate($key, $lang)
                ];
            }
            return response()->json($translate);
        }
        if($request->method() == 'POST') {
            if(!config('piclommerce.demo')) {
                return response(__("piclommerce::admin.demo_error"), 401)->header('Content-Type', 'text/plain');
            }
            $id = Input::get('id');
            $lang = Input::get('lang');
            $content = $this->model->where('id', $id)->firstorFail();
            foreach ($translateFields as $field => $attr) {
                $key = $this->key.'_'.$field;
                if(empty($request->$key)){
                    $request->$key = null;
                }
                $content->setTranslation($field, $lang, $request->$key);
            }
            $content->update();
            return response()->json(["message" => __("piclommerce::admin.translate_success")]);
        }

        return response( __("piclommerce::admin.error"), 500)->header('Content-Type', 'text/plain');
    }

    /**
     * @param $key
     * @return FormTranslate
     */
    public function setKey($key): self
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Retoune les boutons pour appeler la modal-box
     * @param array $langs
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function buttons(array $langs)
    {
        return view('piclommerce::components.admin.translate.buttons',compact('langs'));
    }

    /**
     * Retourne la modal box avec le formulaire
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function modal()
    {
        $form = '';
        $action = $this->action;
        foreach ($this->translatableInputs as $field => $input) {
            $form .= $this->renderInput($field, $input);
        }
        return view('piclommerce::components.admin.translate.modal', compact('form','action'));
    }

    /**
     * Retourne les champs pour la traduction
     * @param string $field
     * @param array $input
     * @return string
     */
    private function renderInput(string $field, array $input): string
    {
        $html = '<div class="form-item">';
        $html .= '<label for="'.$this->key . "_" . $field.'">'.$input['label'].'</label>';
        if($input['type'] == "editor") {
            $html .='<textarea class="html-editor" cols="0" rows="0" name="'.$this->key . "_" . $field.'" id="'.$this->key . "_" . $field.'">'.$this->data->translate($field).'</textarea>';
        } else {
            if($input['type'] == "textarea")
            {
                $html .='<textarea cols="0" rows="0" name="'.$this->key . "_" . $field.'" id="'.$this->key . "_" . $field.'">'.$this->data->translate($field).'</textarea>';
            } else {
                $html .= ' <input type="text" name="'.$this->key . "_" . $field.'" id="'.$this->key . "_" . $field.'" value="'.$this->data->translate($field).'">';
            }
        }
        //$html .= '<div class="desc">' . $this->data->getTranslation($field, config('app.locale')) . '</div>';
        $html .= '</div>';

        return $html;
    }

}

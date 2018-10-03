<?php
namespace Piclou\Piclommerce\Helpers\Attributes;

trait HasAttributes{

    public function getAttr(string $key, string $attribute): string
    {
        if (!is_null($this->isAttributes($key))) {
            return $this->getValue($key, $attribute);
        }
        return $this->{$key};
    }

    public function setAttr(string $key, array $values)
    {
        if (!is_null($this->isAttributes($key))){
            $datas = $this->getValues($key);
            foreach ($values as $attribute => $value) {
                $datas[$attribute] = $value;
            }
            return json_encode($datas, JSON_UNESCAPED_UNICODE |JSON_UNESCAPED_SLASHES );

        }
        return null;
    }


    /**
     * Retourne les valeurs du champs
     * @param string $key
     * @return array|null
     */
    public function getValues(string $key)
    {
        $data = str_replace("\\","/",$this->{$key});
        return json_decode($data, true);
    }

    /**
     * Retourne la valeur de l'attribut si c'est un JSON
     * Sinon retourne la valeur par défaut
     * @param string $key
     * @param string $attribute
     * @return mixed
     */
    private function getValue(string $key, string $attribute)
    {
        $data = str_replace("\\","/",$this->{$key});
        $json = json_decode($data, true);
        if (!is_null($json)) {
            if (isset($json[$attribute])) {
                return $json[$attribute];
            }
            return "";
        }
        return $data;
    }

    /**
     * @param string $key
     * @return bool|null
     */
    private function isAttributes(string $key)
    {
        if (in_array($key, $this->attr)) {
            return true;
        }
        return null;
    }

}
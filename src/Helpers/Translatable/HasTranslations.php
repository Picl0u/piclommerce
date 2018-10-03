<?php
namespace Piclou\Piclommerce\Helpers\Translatable;

trait HasTranslations
{
    /**
     * @param $key
     * @return string
     */
    public function getAttributeValue($key)
    {
        if (!$this->isTranslatableAttribute($key)) {
            return parent::getAttributeValue($key);
        }
        return $this->translate($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isTranslatableAttribute(string $key) : bool
    {
        foreach($this->getTranslatableAttributes() as $field => $attr) {
            if($field == $key) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function getTranslatableAttributes() : array
    {
        return is_array($this->translatable())
            ? $this->translatable()
            : [];
    }

    /**
     * @param string $key
     * @param string $locale
     * @return string|array
     */
    public function translate(string $key, $locale = null)
    {
        $translations = $this->getTranslations($key);
        if(!$locale) {
            $locale = config('app.locale');
        }
        if(is_array($translations))
        {
            if(isset($translations[$locale])){
                return $translations[$locale];
            } else {
                if(!empty($translations)){
                    return array_values($translations)[0];
                } else {
                    return $this->getAttributes()[$key];
                }
            }
        }
        return $translations;
    }

    /**
     * @param null $key
     * @return array
     */
    public function getTranslations($key = null)
    {
       $json =  json_decode($this->getAttributes()[$key] ?? '' ?: '{}', true) ?: [];
       if(!is_array($json)){
           return parent::getAttributeValue($key);
       }
       return $json;

    }

    /**
     * @param string $key
     * @param string $locale
     * @param $value
     * @return self
     */
    public function setTranslation(string $key, string $locale, $value): self
    {
        $translations = $this->getTranslations($key);
        $translations[$locale] = $value;
        $this->attributes[$key] = json_encode($translations);
        return $this;
    }

}

<?php

namespace Piclou\Piclommerce\Helpers;

class DataTable
{
    public function yesOrNot($key):string
    {
        if(!$key || empty($key) ) {
            return '<span class="label error">' . __("piclommerce::admin.no") . '</span>';
        }
        return '<span class="label success">' . __("piclommerce::admin.yes") . '</span>';
    }

    public function image(string $imageResize, string $image, $alt = null)
    {
        return '<img src="' . $imageResize . '" alt="' . $alt . '" class="remodalImg" data-src="/' . $image . '">';
    }

    public function date($date)
    {
        return date('d/m/Y H:i',strtotime($date));
    }

    public function email($email)
    {
        return '<a href="mailto:'.$email.'">'.$email.'</a>';
    }
}
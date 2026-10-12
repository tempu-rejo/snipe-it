<?php

namespace App\Helpers;

class CategoryImageHelper
{
    /**
     * Return the shared default asset image.
     *
     * @param string $categoryName
     * @return string
     */
    public static function getDefaultImageForCategory($categoryName)
    {
        return asset('img/asset-no-image.svg');
    }

    /**
     * Generate the default image configuration for the frontend.
     *
     * @return string
     */
    public static function getCategoryDefaultImagesJson()
    {
        return json_encode(['default' => asset('img/asset-no-image.svg')]);
    }
}
<?php

namespace App\Helpers;

class CategoryImageHelper
{
    /**
     * Mapping kategori hardware ke default image URL
     */
    public static function getCategoryDefaultImages()
    {
        return [
            'Harddisk' => 'https://uitrackin.ultid.com/uploads/accessories/accessory-image--Ki5TZDyrwS.jpg',
            'Storage Disk' => 'https://uitrackin.ultid.com/uploads/accessories/accessory-image--Ki5TZDyrwS.jpg',
            'ViConf' => 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1741668846.jpg',
            'GPS' => 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1747119928.jpg',
            'Raspberry' => 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1742530358.jpg',
            'Checkpoint' => 'https://uitrackin.ultid.com/uploads/assets/asset-image--3WtuuExFmD.jpg',
            'UPS Server' => 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1745820756.jpg',
            'Proyektor' => 'https://uitrackin.ultid.com/uploads/assets/asset-image-4623-VF9Vn6ef1R.jpg',
            'DVR' => 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1742530956.jpg',
            'Server' => 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1742536188.png',
            'Weight Scale' => 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1742528190.jpg',
            'Switch Managed' => 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1742961430.jpg',
            'Handphone' => 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1741576949.jpg',
            'Tablet' => 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1742438431.jpg',
            'Mesin Finger' => 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1741593252.jpg',
            'Access Point' => 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1741837676.jpg',
            'Monitor/TV' => 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1741594391.jpg',
            'PC Desktop' => 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1742960723.jpg',
            'Printer' => 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1741668586.jpg',
            'Laptop' => 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1741658688.jpg',
        ];
    }

    /**
     * Get default image untuk kategori tertentu
     * 
     * @param string $categoryName
     * @return string
     */
    public static function getDefaultImageForCategory($categoryName)
    {
        $defaultImages = self::getCategoryDefaultImages();
        
        // Cari exact match dulu
        if (isset($defaultImages[$categoryName])) {
            return $defaultImages[$categoryName];
        }
        
        // Cari partial match (case-insensitive)
        foreach ($defaultImages as $category => $imageUrl) {
            if (stripos($categoryName, $category) !== false || stripos($category, $categoryName) !== false) {
                return $imageUrl;
            }
        }
        
        // Fallback ke default image
        return asset('uploads/assets/default.png');
    }

    /**
     * Generate JavaScript object untuk frontend
     * 
     * @return string
     */
    public static function getCategoryDefaultImagesJson()
    {
        $defaultImages = self::getCategoryDefaultImages();
        $defaultImages['default'] = asset('uploads/assets/default.png');
        
        return json_encode($defaultImages);
    }
}
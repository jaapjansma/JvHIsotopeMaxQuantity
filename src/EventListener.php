<?php
/**
 * Copyright (C) 2023  Jaap Jansma (jaap.jansma@civicoop.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace JvH\IsotopeMaxQuantityBundle;

use Isotope\Interfaces\IsotopeProduct;
use Isotope\Message;
use Isotope\Model\ProductCollection;
use Isotope\Model\ProductCollectionItem;

class EventListener {

  public static function itemIsAvailable(ProductCollectionItem $item) {
    $objProduct = $item->getProduct();
    if ($objProduct && !empty($objProduct->jvh_max_quantity)) {
      if ($item->quantity > $objProduct->jvh_max_quantity) {
        return false;
      }
    }
    return true;
  }

  public static function addProductToCollection(IsotopeProduct $objProduct, $intQuantity, ProductCollection $collection, $arrConfig) {
    if ($objProduct && !empty($objProduct->jvh_max_quantity)) {
      $count = $intQuantity;
      foreach($collection->getItems() as $objItem) {
        if ($objItem->getProduct()->id == $objProduct->getId()) {
          $count += $objItem->quantity;
        }
      }
      if ($count > $objProduct->jvh_max_quantity) {
        Message::addInfo(sprintf($GLOBALS['TL_LANG']['tl_iso_product']['jvh_max_quantity_limit_reached'], $objProduct->jvh_max_quantity));
        return $objProduct->jvh_max_quantity;
      }
    }
    return $intQuantity;
  }

  public function updateItemInCollection(ProductCollectionItem $item, $arrSet, ProductCollection $collection) {
    $objProduct = $item->getProduct();
    if ($objProduct && !empty($objProduct->jvh_max_quantity)) {
      $count = $item->quantity;
      foreach($collection->getItems() as $objItem) {
        if ($objItem->getProduct()->id == $objProduct->getId()) {
          $count += $objItem->quantity;
        }
      }
      if ($count > $objProduct->jvh_max_quantity) {
        Message::addInfo(sprintf($GLOBALS['TL_LANG']['tl_iso_product']['jvh_max_quantity_limit_reached'], $objProduct->jvh_max_quantity));
        $arrSet['quantity'] = $objProduct->jvh_max_quantity;
      }
    }
    return $arrSet;
  }

}
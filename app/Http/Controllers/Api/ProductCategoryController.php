<?php
/**
 * @package ProductCategoryController
 * @author tehcvillage <support@techvill.org>
 * @contributor Md. Nobeul Islam <[nobeul.techvill@gmail.com]>
 * @created 31-07-2021
 */
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;

class ProductCategoryController extends Controller
{
    /**
     * index function
     *
     * @return void
     */
    public function index()
    {
        $categories = ProductCategory::getAll();

        if (count($categories) == 0) {
            return response()->json(['status' => 200, 'message' => __('No category has been added yet.')]);
        } else {
            return response()->json(['status' => 200, 'categories' => $categories]);
        }
    }
}

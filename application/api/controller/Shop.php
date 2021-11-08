<?php

namespace app\api\controller;
use Exception;
use app\common\controller\ApiController;
use app\common\model\Product as ProductModel;
use app\common\model\ProductCategory as ProductCategoryModel;
use app\common\model\ProductEvaluate as ProductEvaluateModel;
use app\common\model\ProductCollection as ProductCollectionModel;

/**
 * 商城 API
 */
class Shop extends ApiController
{
    //-------------------------------------------------- 商品分类

    /**
     * 商品分类列表接口
     * @return void
     * @throws Exception
     */
    public function product_category_list()
    {
        $pid = $this->get_param('pid', 0);

        $list = ProductCategoryModel::category_list($pid, $this->member_id);
        output_success('', ['list' => $list]);
    }

    /**
     * 所有直接商品分类列表接口
     * @return void
     * @throws Exception
     */
    public function all_product_category_list()
    {
        $list = ProductCategoryModel::all_category_list();
        output_success('', ['list' => $list]);
    }

    //-------------------------------------------------- 商品

    /**
     * 商品列表接口
     * @return void
     * @throws Exception
     */
    public function product_list()
    {
        $category_id    = $this->get_param('category_id', 0);
        $category_level = $this->get_param('category_level', 0);
        $keyword        = $this->get_param('keyword', '');
        $sort_type      = $this->get_param('sort_type', ProductModel::SORT_DEFAULT);

        $list = ProductModel::product_list($keyword, $sort_type, $category_id, $this->member_id, $category_level);

        output_success('', $list);
    }

    /**
     * 商品详情接口
     * @return void
     * @throws Exception
     */
    public function product_detail()
    {
        $product_id = $this->get_param('product_id');

        $detail = ProductModel::product_detail($product_id, $this->member_id);
        empty($detail) AND output_error('商品已下架！');
        output_success('', $detail);
    }


    //-------------------------------------------------- 商品评价

    /**
     * 商品评论列表接口
     * 分页
     * @return void
     * @throws Exception
     */
    public function product_evaluate_list()
    {
        $product_id = $this->get_param('product_id');

        $list = ProductEvaluateModel::evaluate_list($product_id);
        output_success('', $list);
    }

    //-------------------------------------------------- 商品收藏

    /**
     * 商品收藏列表接口
     * 分页
     * @return void
     * @throws Exception
     */
    public function product_collection_list()
    {
        $this->check_login();

        $list = ProductCollectionModel::collection_list($this->member_id);

        output_success('', $list);
    }

    /**
     * 商品收藏添加接口
     * @return void
     * @throws Exception
     */
    public function product_collection_insert()
    {
        $product_id = $this->get_param('product_id');
        $this->check_login();

        $result = ProductCollectionModel::check_exists($this->member_id, $product_id);
        if (!$result) {
            $product = ProductModel::get($product_id);
            empty($product) AND output_error('商品已下架！');

            $result = ProductCollectionModel::collection_insert($this->member_id, $product_id);
            $result OR output_error('商品收藏失败！');
        }
        output_success('收藏成功！');
    }

    /**
     * 商品收藏取消接口
     * product_id 格式 1,2,3,4
     * @return void
     * @throws Exception
     */
    public function product_collection_cancel()
    {
        $product_id = $this->get_param('product_id');
        $this->check_login();

        $result = ProductCollectionModel::check_exists($this->member_id, $product_id);
        $result OR output_error('您未收藏该商品！');

        $result = ProductCollectionModel::collection_delete($this->member_id, $product_id);
        $result OR output_error('商品收藏取消失败！');
        output_success('收藏取消成功！');
    }
}

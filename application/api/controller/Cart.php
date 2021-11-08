<?php

namespace app\api\controller;

use Exception;
use app\common\controller\ApiController;
use app\common\model\Product as ProductModel;
use app\common\model\CartShop as CartShopModel;

/**
 * 购物车 API
 */
class Cart extends ApiController
{
    /**
     * 单商城购物车商品列表接口
     * @return void
     * @throws Exception
     */
    public function single_product_list()
    {
        $list = CartShopModel::single_product_list($this->member_id);
        output_success('', ['list' => $list]);
    }

    /**
     * 购物车商品数量接口
     * @return void
     */
    public function product_number()
    {
        $is_login = !empty($this->member_id);

        $number = $is_login ? CartShopModel::product_number($this->member_id) : 0;
        output_success('', ['number' => $number, 'is_login' => $is_login]);
    }

    /**
     * 购物车商品种类数量接口
     * @return void
     * @throws Exception
     */
    public function product_count()
    {
        $is_login = !empty($this->member_id);

        $count = $is_login ? CartShopModel::product_count($this->member_id) : 0;
        output_success('', ['count' => $count, 'is_login' => $is_login]);
    }

    /**
     * 购物车商品数量添加接口
     * @return void
     * @throws Exception
     */
    public function product_add()
    {
        $product_id = $this->get_param('product_id');
        $number      = $this->get_param('number', 0);

        $this->check_login();

        $number = intval(max($number, 1));

        $product = ProductModel::get($product_id);
        empty($product) AND output_error('商品已下架！');
        $product->getAttr('sell') OR output_error('商品已下架！');

        $cart = CartShopModel::get(['member_id' => $this->member_id, 'product_id' => $product_id]);
        if (!empty($cart)) {
            output_error('该商品已加入购物车，请勿重复添加！');
        } else {
            $cart = new CartShopModel(['member_id' => $this->member_id, 'product_id' => $product_id]);
        }
        if ($number > $product->getAttr('stock')) {
            output_error('购物车商品数量达到库存限制！');
        }
        $cart->save(['number' => $number, 'time' => time()]);
        output_success('', ['number' => $number]);
    }

    /**
     * 购物车商品数量减少接口
     * @return void
     * @throws Exception
     */
    public function product_reduce()
    {
        $product_id = $this->get_param('product_id');
        $number      = $this->get_param('number', 0);
        $this->check_login();

        $number = intval(max($number, 1));

        $result = CartShopModel::check_product_contain($this->member_id, $product_id);
        $result OR output_error('该商品未加入购物车！');

        $cart = CartShopModel::get(['member_id' => $this->member_id, 'product_id' => $product_id]);
        empty($cart) AND output_error('该商品未加入购物车！');
        $now_number = $cart->getAttr('number');
        $now_number == 1 AND output_error('商品数量无法再减少！');
        $number = max($now_number - $number, 1);
        $cart->setAttr('number', $number)->save();
        output_success('', ['number' => $number]);
    }

    /**
     * 购物车商品编辑接口
     * product 商品数据 json格式 [{"product_id":1,"number":2}] product_id 商品id, number 商品数量
     * @return void
     */
    public function product_edit()
    {
        $product = $this->get_param('product');
        $this->check_login();

        $product = @json_encode($product);
        if (!is_array($product) || empty($products)) {
            output_success();
        }

        foreach ($product as $k => $v) {
            if (isset($v['product_id']) && isset($v['number'])) {
                $where = ['member_id' => $this->member_id, 'product_id' => $v['product_id']];
                CartShopModel::update(['number' => $v['number']], $where);
            }
        }

        output_success();
    }

    /**
     * 购物车商品删除接口
     * product_id 商品规格id 多个以，间隔 例：1,2,3
     * @return void
     * @throws Exception
     */
    public function product_delete()
    {
        $product_id = $this->get_param('product_id');
        $this->check_login();

        $result = CartShopModel::check_product_contain($this->member_id, $product_id);
        $result OR output_error('该商品未加入购物车！');

        $result = CartShopModel::product_delete($this->member_id, $product_id);
        $result OR output_error('商品删除失败！');

        output_success();
    }
}
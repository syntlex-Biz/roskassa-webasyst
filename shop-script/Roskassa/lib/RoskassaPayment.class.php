<?php


class RoskassaPayment extends waPayment implements waIPayment
{
    const VERSION = '0.1';

    protected function initControls()
    {
    }

    public function allowedCurrency()
    {
        return array(
            'RUB',
            'RUR',
            'USD',
            'EUR',
        );
    }

    public function payment($payment_form_data, $order_data, $auto_submit = false)
    {
        $order = waOrder::factory($order_data);
        $url = $this->url;
        $shop_id = $this->shop_id;
        $key1 = $this->key1;
        $order_id = $order->id;

        $amount = number_format($order->total, 2, '.', '');
        $curr = $order->currency == 'RUR' ? 'RUB' : $order->currency;
        $desc = base64_encode('Оплата заказа №' . $order_id . ' (' . $this->app_id . '-' . $this->merchant_id . ')');



        $data = array(
            'shop_id'=> $shop_id,
            'amount'=>$amount,
            'currency'=>$curr,
            'order_id'=>$order_id,

        );
        if ($this->test_mode)
        {
            $data['test'] = $this->test_mode;
        }



        ksort($data);
        $str = http_build_query($data);
        $sign = md5($str .$key1 );

        $view = wa()->getView();
        $view->assign('url', $url);
        $view->assign('shop_id', $shop_id);
        $view->assign('order_id', $order_id);
        $view->assign('amount', $amount);
        $view->assign('sign', $sign);
        $view->assign('curr', $curr);
        if ($this->test_mode) {
            $view->assign('test', $this->test_mode);
        }
  //      $view->assign('desc', $desc);


        return $view->fetch($this->path . '/templates/payment.html');
    }

    protected function callbackInit($request)
    {
        $desc = base64_decode($request['us_desc']);
        preg_match_all("/\(+.+\-+[0-9]+\)+/", $desc, $matches);
        preg_match('/\((.+)\)/', $matches[0][0], $m);
        $opt = explode('-', $m[1]);

        $this->app_id = $opt[0];
        $this->merchant_id = $opt[1];
        return parent::callbackInit($request);
    }

    public function callbackHandler($request)
    {
        $transaction_data = $this->formalizeData($request);
        $action = $request['type'];
        $url = null;

        switch ($action) {

            case 'success':
                $url = $this->getAdapter()->getBackUrl(waAppPayment::URL_SUCCESS, $transaction_data);
                return array('redirect' => $url);
                break;

            default:
                $url = $this->getAdapter()->getBackUrl(waAppPayment::URL_FAIL, $transaction_data);
                return array('redirect' => $url);
                break;
        }
    }

    protected function formalizeData($transaction_raw_data)
    {
        $transaction_data = parent::formalizeData($transaction_raw_data);
        $transaction_data['native_id'] = $transaction_raw_data['intid'];
        $transaction_data['amount'] = $transaction_raw_data['AMOUNT'];
        $transaction_data['currency_id'] = $transaction_raw_data['us_curr'];
        $transaction_data['order_id'] = $transaction_raw_data['MERCHANT_ORDER_ID'];

        switch ($transaction_raw_data['action']) {
            case 'success':
                $transaction_data['state'] = self::STATE_CAPTURED;
                $transaction_data['type'] = self::OPERATION_AUTH_CAPTURE;
                $transaction_data['result'] = 1;
                break;

            case 'fail':
                $transaction_data['state'] = self::STATE_DECLINED;
                $transaction_data['type'] = self::OPERATION_CANCEL;
                $transaction_data['result'] = 1;
                break;
        }

        return $transaction_data;
    }

}
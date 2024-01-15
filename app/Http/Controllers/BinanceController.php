<?php

namespace App\Http\Controllers;

use Binance;
use Illuminate\Support\Facades\Log;

class BinanceController extends Controller
{

    private $binanceApi;

    public function __construct()
    {
        $this->binanceApi = new Binance\API(config('app.binance_key'), config('app.binance_secret'));;
    }

    public function index()
    {
        return view('binance.index');
    }

    public function account(){
        $account =   $this->binanceApi->account();
        dd($account);
    }

    public function trade()
    {
        try {

            $btcPrice = $this->binanceApi->price("BTCUSDT");

            $priceChangePercent = ($btcPrice['0'] * 100);
            Log::info("Price change: $priceChangePercent");

            if ((int)$priceChangePercent <= -5) {
                // Sell all coins
                $this->sellAllCoins();
            } elseif ((int)$priceChangePercent >= 5) {
                // Buy LTC and ETH
                $this->buyCoins(['BTC', 'BMB'], 10);
            }

            return back()->with('success', 'success');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function sellAllCoins()
    {
        $account = $this->binanceApi->account();

        foreach ($account['balances'] as $balance) {
            if ($balance['free'] > 0 && $balance['asset'] !== 'USDT') {
                $this->binanceApi->marketSell($balance['asset'] . 'USDT', $balance['free']);
            }
        }
    }

    public function buyCoins($coinsToBuy, $amountPerCoin)
    {
//        try {
            foreach ($coinsToBuy as $coin) {
                $this->binanceApi->marketBuy($coin . 'USDT', $amountPerCoin);
            }
//        } catch (\Exception $exception) {
//            Log::error($exception->getMessage());
//        }
    }

    public function price()
    {
        $ticker = $this->binanceApi->balances();
        dd($ticker);
    }

}

<?php

namespace App\Admin\Controllers\Manager;

use App\Models\App;
use App\Admin\AppKey;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;
use App\Admin\Extensions\ZaihangyunApiTester;

class ZaihangyunApiTesterController extends Controller
{
    use AppKey;

    protected $tester;

    protected $api_url;

    public function __construct()
    {
        $this->tester = new ZaihangyunApiTester();
        $this->api_url = config('app.api_url');
    }

    public function index(Content $content)
    {
        return $content
            ->title('接口调试')
            ->body(view('api_tester', [
                'form_url' => admin_url('app/manager/'.$this->getAppKey().'/api_tester/handle'),
                'routes' => $this->tester->getRoutes(),
            ]));
    }

    public function handle(Request $request)
    {
        $app_key = $this->getAppKey();
        $method = $request->get('method');
        $uri = $request->get('uri');
        $token = $request->get('token');
        $all = $request->all();

        $keys = Arr::get($all, 'key', []);
        $vals = Arr::get($all, 'val', []);

        ksort($keys);
        ksort($vals);

        $parameters = [];

        $app_info = App::where('app_key', $app_key)->first();

        $current_time = time();
        foreach ($keys as $index => $key) {
            $value = Arr::get($vals, $index);
            if($key == 'appkey') {
                $value = $app_info->app_key;
            }else if($key == 'timestamp') {
                $value = $current_time;
            }else if($key == 'sign') {
                $value = md5($app_info->app_key.$current_time.$app_info->app_secret);
            }
            $parameters[$key] = $value;
        }

        $parameters = array_filter($parameters, function ($key) {
            return $key !== '';
        }, ARRAY_FILTER_USE_KEY);

        list($response, $requestInfo) = $this->tester->call($method, $this->api_url.$uri, $parameters, $token);

        $ret_data = [
            'headers'    => json_encode(collect($response->getHeaders())->except('Server'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'content'    => json_encode(json_decode($response->getBody()->getContents(), true), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'request_params' => json_encode($requestInfo, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'language'   => 'json',
            'status'     => [
                'code'  => $response->getStatusCode(),
                'text'  => $response->getReasonPhrase(),
            ],
        ];

        return [
            'status'    => true,
            'message'   => 'success',
            'data'      => $ret_data,
        ];
    }
}
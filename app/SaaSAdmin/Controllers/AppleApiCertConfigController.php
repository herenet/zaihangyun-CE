<?php

namespace App\SaaSAdmin\Controllers;

use App\Libs\Helpers;
use Firebase\JWT\JWT;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Models\AppleDevS2SConfig;
use Illuminate\Support\Facades\Log;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Illuminate\Support\Facades\Http;
use Encore\Admin\Controllers\AdminController;

class AppleApiCertConfigController extends AdminController
{
    public function index(Content $content)
    {
        return $content
        ->title('苹果服务端API请求证书设置')
        ->body($this->grid());
    }

    public function grid()
    {
        $grid = new Grid(new AppleDevS2SConfig());
        $grid->model()
        ->where('tenant_id', SaaSAdmin::user()->id)
        ->orderBy('id', 'desc');

        $grid->column('id', 'ID')->hide();
        $grid->column('dev_account_name', '开发者账户名称');
        $grid->column('issuer_id', 'Issuer ID');
        $grid->column('key_id', 'Key ID')->password('*', 8);
        $grid->column('p8_cert_content', 'p8证书内容')->display(function ($value) {
            return substr($value, 0, 10) . '...';
        });
        $grid->column('interface_check', '接口验证')->using([0 => '未验证', 1 => '已验证'])->label(['success' => '已验证', 'danger' => '未验证']);
        $grid->column('updated_at', '更新时间')->sortable();
        
        $grid->actions(function ($actions) {
            $actions->disableView();
        });
        
        $grid->disableFilter();
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();

        return $grid;
    }

    public function title()
    {
        return '苹果服务端API请求证书设置';
    }

    public function form()
    {
        $form = new Form(new AppleDevS2SConfig());
        $form->html('<div class="alert alert-info">苹果服务端API请求证书为同一苹果开发者账户下的所有App共享，请确保配置正确。</div>');
        $form->text('dev_account_name', '开发者账户名称')
            ->required()
            ->rules(['required', 'string', 'max:128'])
            ->help('填写开发者账户名称用于区分不同开发者账户的配置');
        $form->text('issuer_id', 'Issuer ID')
            ->required()
            ->rules(['required', 'string', 'max:128'])
            ->help('以下参数需要在苹果App Store Connect中获取。<button type="button" class="btn btn-xs btn-info" id="p8-help-btn"><i class="fa fa-question-circle"></i> 如何获取</button>');
        $form->text('key_id', 'Key ID')
            ->required()
            ->rules(['required', 'string', 'max:128'])
            ->help('在苹果密钥（Keys） 页签里，创建一个 App Store Connect API 密钥（API Key），成功后会生成一个 Key ID（例如 ABC123XYZ）和一个 .p8 文件供下载。下载后不会再显示，请妥善保存');
        $form->textarea('p8_cert_content', 'p8证书内容')
            ->required()
            ->rules(['required', 'string', 'max:2048'])
            ->help('请复制Store Server API密钥（.p8）内容，粘贴到此处。');
        $form->interfaceCheck('interface_check', '验证配置')
            ->buttonText('验证配置是否正确')
            ->dependentOn(['issuer_id', 'key_id', 'p8_cert_content'])
            ->default(0)
            ->testUrl(admin_url('global/config/apple/apicert/verify'))
            ->help('通过调用苹果IAP接口的方式来验证配置是否正确');
        
        $form->tools(function ($tools) {
            $tools->disableDelete();
            $tools->disableView();
        });

        $form->saving(function (Form $form) {
            $form->model()->tenant_id = SaaSAdmin::user()->id;
            $interface_check = $form->input('interface_check');
            if ($interface_check == 0) {
                admin_error('请先验证配置是否正确');
                return back()->withInput();
            }
        });

        $form->footer(function ($footer) {
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
            $footer->disableReset();
        });

        Admin::html(<<<HTML
<div class="modal fade" id="p8-help-modal" tabindex="-1" role="dialog" aria-labelledby="p8-help-modal-label">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="p8-help-modal-label">如何获取Store Server API密钥</h4>
                </div>
                <div class="modal-body" style="padding: 30px;">
                    <ol>
                        <li>登录<a href="https://appstoreconnect.apple.com" target="_blank">App Store Connect</a></li>
                        <li>点击"用户和访问"</li>
                        <li>点击"集成"Tab<a href="https://appstoreconnect.apple.com/access/integrations/api" target="_blank">点击直达</a></li>
                        <li>选择"App Store Connect API"</li>
                        <li>选择"创建密钥"</li>
                        <li>点击“有效”旁边的“+”图标</li>
                        <li>在弹出窗中填写名称，在“访问”选择“开发者”、“管理”和"App管理"三项任一项</li>
                        <li>点击“保存”按钮</li>
                        <li>生成成功后，就可以看到页面中显示密钥ID和Issuer ID</li>
                        <li>点击“下载”按钮，下载刚生成的.p8文件（只允许下载一次，请保存好）</li>
                        <li>复制文件中的全部内容（包括BEGIN和END标记）</li>
                        <li>粘贴到文本到p8证书内容框中</li>
                    </ol>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 提示：生成密钥后请保存好密钥ID和Issuer ID，这些将在其他地方用到。
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
    </div>
HTML);
        Admin::script(<<<JS
$(function () {
    // 创建modal HTML
    
    // 点击帮助按钮时显示modal
    $('#p8-help-btn').on('click', function(e) {
        e.preventDefault();
        $('#p8-help-modal').modal('show');
    });
});
JS);

        return $form;
    }

    public function verify()
    {
        $issuerId = request()->input('issuer_id');
        $keyId = request()->input('key_id');
        $privateKey = request()->input('p8_cert_content');

        if(empty($issuerId)) {
            return response()->json(['status' => false, 'message' => 'Issuer ID不能为空']);
        }
        if(empty($keyId)) {
            return response()->json(['status' => false, 'message' => 'Key ID不能为空']);
        }
        if(empty($privateKey)) {
            return response()->json(['status' => false, 'message' => 'p8证书内容不能为空']);
        }

        $now = time();
        $expiration = $now + 600; // 10 分钟有效期

        $token = JWT::encode([
            'iss' => $issuerId,
            'iat' => $now,
            'exp' => $expiration,
            'aud' => 'appstoreconnect-v1'
        ], $privateKey, 'ES256', $keyId);

        $response = Http::withToken($token)->get('https://api.appstoreconnect.apple.com/v1/apps');

        if ($response->successful()) {
            return response()->json(['status' => true]);
        }

        $error = $response->json('errors.0');

        return response()->json([
            'status' => false, 
            'message' => '配置错误[' . $error['code'].'] : '.$error['title']]);
    }

    public function verifyNotify($params)
    {
        $params = Helpers::simpleDecode($params);
        $headers = request()->header();
        $body = request()->getContent();
        $data = request()->all();
        //dd($params);
        Log::channel('callback')->info('苹果IAP回调验证', ['params' => $params, 'headers' => $headers, 'body' => $body, 'data' => $data]);
    }
}
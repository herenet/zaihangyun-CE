<?php

namespace App\SaaSAdmin\Controllers\Manager;

use App\Models\User;
use App\Libs\Helpers;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\SaaSAdmin\AppKey;
use Illuminate\Support\Str;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Encore\Admin\Controllers\AdminController;

class UserController extends AdminController
{
    use AppKey;

    public function index(Content $content)
    {
        
        return $content
        ->title('用户列表')
        ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new User());
        $grid->model()->where('app_key', $this->getAppKey())->orderBy('created_at', 'desc');
        $grid->fixColumns(2, -2);

        // 基础信息
        $grid->column('uid', 'UID');
        $grid->column('nickname', '昵称');
        $grid->column('avatar', '头像')->image('', 50, 50);
        $grid->column('username', '用户名');
        $grid->column('gender', '性别');
        $grid->column('birthday', '生日')->date();
        // 联系方式
        $grid->column('mobile', '手机号')->display(function ($mobile) {
            /** @var User $this */
            if(empty($mobile)){
                return '';
            }
            return $this->mcode . ' ' . $mobile;
        })->prependIcon('phone');

        // 第三方账号
        $grid->column('wechat_openid', '微信OpenID')->limit(15);
        $grid->column('wechat_unionid', '微信UnionID')->limit(15);
        $grid->column('apple_userid', '苹果ID')->limit(15);

        // 地理位置
        $grid->column('country', '国家');
        $grid->column('province', '省份');
        $grid->column('city', '城市');

        // 会员信息
        $grid->column('is_forever_vip', '永久会员')->using(User::$isForeverVipMap)->label([
            '1'  => 'success',
            '0' => 'default'
        ]);
        $grid->column('vip_expired_at', 'VIP到期时间');

        $grid->column('enter_pass', '启动密码')->password('*', 6);
        $grid->column('ext_data', '扩展数据')->limit(30);

        // 注册信息
        $grid->column('reg_from', '注册来源')->using(User::$regFromMap);
        $grid->column('channel', '渠道');
        $grid->column('reg_ip', '注册IP');

        // 其他信息
        $grid->column('version_number', 'APP版本');
        $grid->column('updated_at', '更新时间')->sortable();
        $grid->column('created_at', '注册时间')->sortable();

        // 筛选器
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('nickname', '昵称');
            $filter->like('mobile', '手机号');
            $filter->equal('reg_from', '注册来源')->select(User::$regFromMap);
            $filter->equal('is_forever_vip', '永久会员')->select(User::$isForeverVipMap);
            $filter->between('created_at', '注册时间')->datetime();
        });

        // 配置导出
        $grid->export(function ($export) {
            $export->filename('用户数据-'.date('Y-m-d H:i:s').'-'.$this->getAppKey().'.csv');
            
            // 自定义导出字段
            $export->except(['avatar']); // 排除不需要导出的字段
            $export->originalValue(['birthday', 'wechat_openid', 'wechat_unionid', 'apple_userid', 'ext_data', 'enter_pass']);
            
            $export->column('is_forever_vip', function ($value) {
                return $value == 1 ? '是' : '否';
            });
            
            $export->column('mobile', function ($value, $original) {
                return strip_tags($value);
            });
        });

        // 禁用批量删除
        $grid->disableBatchActions();

        return $grid;
    }

    public function form()
    {
        // if(request()->is('*/edit')){
            
        //     $form = new Form(User::find(request()->route('user')));
        // }else{
            $form = new Form(new User());
        // }

        if($form->isEditing()){
            $uid = request()->route('user');
            $form->setResourceId($uid);
        }
        
        $form->setTitle('用户信息');
        $form->setWidth(6, 3);
        $form->text('nickname', '昵称')->rules(['required', 'string', 'max:64']);
        $form->text('username', '用户名')->rules(['nullable', 'string', 'max:64']);
        $form->mobile('mobile', '手机号')->prepend('+86')->rules(['nullable', 'integer', 'digits:11']);
        if($form->isCreating()) {
            $form->radio('gender', '性别')->options([
                '男' => '男',
                '女' => '女',
                '未知' => '未知',
            ])->default('未知');
        } else {
            $form->text('gender', '性别')->rules(['nullable', 'string', 'max:6']);
        }
        $form->date('birthday', '生日')->rules(['nullable', 'date']);
        $form->text('country', '国家')->rules(['nullable', 'string', 'max:64']);
        $form->text('province', '省份')->rules(['nullable', 'string', 'max:64']);
        $form->text('city', '城市')->rules(['nullable', 'string', 'max:64']);

        $form->text('wechat_openid', '微信OpenID')->rules(['nullable', 'string', 'max:128']);
        $form->text('wechat_unionid', '微信UnionID')->rules(['nullable', 'string', 'max:128']);
        $form->text('apple_userid', '苹果ID')->rules(['nullable', 'string', 'max:128']);
        $form->number('version_number', 'APP版本')->default(1);
        $form->json('ext_data', '扩展数据')->rules(['nullable', 'string', 'max:128']);
        $form->select('reg_from', '注册来源')->options(User::$regFromMap)->default(99);
        $form->text('channel', '渠道')->default(User::DEFAULT_CHANNEL)->rules(['nullable', 'string', 'max:32']);
        $form->ip('reg_ip', '注册IP')->default('127.0.0.1')->rules(['nullable', 'ip']);
        $form->password('enter_pass', '启动密码')
            ->attribute('minlength', 6)
            ->attribute('maxlength', 12)
            ->prepend('<i class="fa fa-lock"></i>')
            ->append('<a class="fa fa-eye" onclick="togglePasswordVisibility(this)" style="cursor:pointer;"></a>')
            ->rules(['nullable', 'string', 'min:6', 'max:12']);
        $form->switch('is_forever_vip', '永久会员')
            ->default(0)
            ->states([
                'on'  => ['value' => 1, 'text' => '是'],
                'off' => ['value' => 0, 'text' => '否']
            ]);
        $form->datetime('vip_expired_at', 'VIP到期时间')->rules(['nullable', 'date']);

        $form->saving(function (Form $form) {
            $form->model()->uid = Helpers::generateUserId();
            $form->model()->app_key = $this->getAppKey();
            $form->model()->tenant_id = SaaSAdmin::user()->id;
        });

        $form->saved(function (Form $form) {
            admin_toastr('添加成功', 'success');
        });
        
        return $form;
    }

    public function detail()
    {
        $uid = request()->route('user');
        $show = new Show(User::find($uid));
        $show->avatar('头像')->image();
        $show->field('uid', 'UID');
        $show->field('wechat_openid', '微信OpenID');
        $show->field('wechat_unionid', '微信UnionID');
        $show->field('apple_userid', '苹果ID');
        $show->field('nickname', '昵称');
        $show->field('username', '用户名');
        $show->field('gender', '性别');
        $show->field('birthday', '生日');
        $show->field('mobile', '手机号')->as(function ($value) {
            /** @var User $this */
            return $value ? $this->mcode . ' ' . $value : '';
        });
        $show->field('is_forever_vip', '永久会员')->using(User::$isForeverVipMap);
        $show->field('vip_expired_at', 'VIP到期时间');
        $show->field('ext_data', '扩展数据')->json();
        $show->field('enter_pass', '启动密码')->password();
        $show->field('country', '国家');
        $show->field('province', '省份');
        $show->field('city', '城市');
        $show->field('version_number', 'APP版本');
        $show->field('reg_from', '注册来源')->using(User::$regFromMap);
        $show->field('channel', '渠道');
        $show->field('reg_ip', '注册IP');
        $show->field('updated_at', '更新时间');
        $show->field('created_at', '注册时间');
        return $show;
    }
}
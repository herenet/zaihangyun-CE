<?php

namespace App\SaaSAdmin\Controllers\Manager;

use App\Models\User;
use App\Libs\Helpers;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\SaaSAdmin\AppKey;
use Encore\Admin\Layout\Content;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Illuminate\Support\Facades\Cache;
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
        $grid->column('avatar', '头像')->image(config('app.url').'/storage/mch/avatar/', 30, 30);
        $grid->column('username', '用户名');
        $grid->column('gender', '性别')->using(User::$genderMap);
        $grid->column('birthday', '生日')->date();
        // 联系方式
        $grid->column('mobile', '手机号')->display(function ($mobile) {
            /** @var User $this */
            if(empty($mobile)){
                return '';
            }
            return $this->mcode . ' ' . $mobile;
        })->prependIcon('phone');
        $grid->column('password', '登录密码')->password('*', 6);
        $grid->column('email', '邮箱');

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
        $grid->column('canceled_at', '申请注销时间');
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

    public function edit($id, Content $content)
    {
        $id = request()->route('list');
        return parent::edit($id, $content)->title('用户信息')->description('编辑');
    }

    public function update($id)
    {
        $id = request()->route('list');
        $cache_key = 'user_info|'.$id;
        Cache::store('api_cache')->forget($cache_key);
        return parent::update($id);
    }

    public function destroy($id)
    {
        $cache_key = 'user_info|'.$id;
        Cache::store('api_cache')->forget($cache_key);
        return parent::destroy($id);
    }

    public function form()
    {
        $form = new Form(new User());

        $form->setWidth(6, 3);
        $form->text('nickname', '昵称')->rules(['required', 'string', 'max:64']);
        $form->text('username', '用户名')->rules(['nullable', 'string', 'max:64']);
        $form->mobile('mobile', '手机号')->prepend('+86')->rules(['nullable', 'integer', 'digits:11']);

        $form->email('email', '邮箱')->rules(['nullable', 'email', 'max:64']);
       
        if($form->isCreating()) {
            $form->password('password', '登录密码')
                ->attribute('minlength', 6)
                ->attribute('maxlength', 32)
                ->rules(['nullable', 'string', 'min:6', 'max:32'])
                ->append('<a class="fa fa-eye" onclick="togglePasswordVisibility(this)" style="cursor:pointer;"></a>')
                ->help('密码长度为6-32位, 采用MD5加密');
        } else {
            $form->password('password', '登录密码')
                ->attribute('value', '')
                ->attribute('minlength', 6)
                ->attribute('maxlength', 32)
                ->rules(['nullable', 'string', 'min:6', 'max:32'])
                ->append('<a class="fa fa-eye" onclick="togglePasswordVisibility(this)" style="cursor:pointer;"></a>')
                ->help('如果留空，则不修改密码, 密码长度为6-32位, 采用MD5签名');
        }

        if($form->isCreating()) {
            $form->radio('gender', '性别')->options(User::$genderMap)->default(0);
        } else {
            $form->text('gender', '性别')->rules(['nullable', 'integer', 'in:0,1,2']);
        }
        $form->date('birthday', '生日')->rules(['nullable', 'date']);
        $form->text('country', '国家')->rules(['nullable', 'string', 'max:32']);
        $form->text('province', '省份')->rules(['nullable', 'string', 'max:32']);
        $form->text('city', '城市')->rules(['nullable', 'string', 'max:32']);

        $form->text('wechat_openid', '微信OpenID')->rules(['nullable', 'string', 'max:128']);
        $form->text('wechat_unionid', '微信UnionID')->rules(['nullable', 'string', 'max:128']);
        $form->text('apple_userid', '苹果ID')->rules(['nullable', 'string', 'max:128']);
        $form->number('version_number', 'APP版本')->default(1)->rules(['integer', 'min:1', 'max:9999']);
        $form->json('ext_data', '扩展数据')->rules(['nullable', 'string', 'max:128']);
        $form->select('reg_from', '注册来源')->options(User::$regFromMap)->default(99);
        $form->text('channel', '渠道')->default(User::DEFAULT_CHANNEL)->rules(['nullable', 'string', 'max:32']);
        $form->ip('reg_ip', '注册IP')->default('127.0.0.1')->rules(['nullable', 'ip']);
        $form->password('enter_pass', '启动密码')
            ->attribute('minlength', 4)
            ->attribute('maxlength', 8)
            ->prepend('<i class="fa fa-lock"></i>')
            ->append('<a class="fa fa-eye" onclick="togglePasswordVisibility(this)" style="cursor:pointer;"></a>')
            ->rules(['nullable', 'string', 'min:4', 'max:8']);
        $form->switch('is_forever_vip', '永久会员')
            ->default(0)
            ->states([
                'on'  => ['value' => 1, 'text' => '是'],
                'off' => ['value' => 0, 'text' => '否']
            ]);
        $form->datetime('vip_expired_at', 'VIP到期时间')->rules(['nullable', 'date']);

        if($form->isCreating()) {
            $form->saving(function (Form $form) {
                $form->model()->uid = Helpers::generateUserId();
                $form->model()->app_key = $this->getAppKey();
                $form->model()->tenant_id = SaaSAdmin::user()->id;
                
                // 如果设置了密码，则进行 MD5 加密
                if ($form->password && $form->password !== '') {
                    $form->password = md5($form->password);
                }
            });
        } else {
            $form->saving(function (Form $form) {
                // 更新时，如果设置了密码，则进行 MD5 加密
                if ($form->password && $form->password !== '') {
                    $form->password = md5($form->password);
                }else{
                    $form->input('password', $form->model()->getOriginal('password'));
                }
            });
        }

        $form->saved(function (Form $form) {
            admin_toastr('添加成功', 'success');
        });

        $form->tools(function(Form\Tools $tools){
            $tools->disableView();
            $tools->disableDelete();
        });

        $form->footer(function(Form\Footer $footer){
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
            $footer->disableReset();
        });
        
        return $form;
    }

    public function title()
    {
        return '用户信息';
    }

    public function detail()
    {
        $uid = request()->route('list');
        $show = new Show(User::find($uid));
        $show->avatar('头像')->image(config('app.url').'/storage/mch/avatar/');
        $show->field('uid', 'UID');
        $show->field('wechat_openid', '微信OpenID');
        $show->field('wechat_unionid', '微信UnionID');
        $show->field('apple_userid', '苹果ID');
        $show->field('nickname', '昵称');
        $show->field('username', '用户名');
        $show->field('gender', '性别')->using(User::$genderMap);
        $show->field('birthday', '生日');
        $show->field('mobile', '手机号')->as(function ($value) {
            /** @var User $this */
            return $value ? $this->mcode . ' ' . $value : '';
        });
        $show->field('password', '登录密码')->password();
        $show->field('email', '邮箱');
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

        $show->panel()
        ->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });
        return $show;
    }
}
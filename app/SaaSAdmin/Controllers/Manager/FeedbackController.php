<?php

namespace App\SaaSAdmin\Controllers\Manager;

use Encore\Admin\Grid;
use App\Models\Feedback;
use App\SaaSAdmin\AppKey;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Form;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Encore\Admin\Controllers\AdminController;

class FeedbackController extends AdminController
{
    use AppKey;
    public function index(Content $content) 
    {
        return $content
            ->title('反馈列表')
            ->body($this->grid());
    }

    protected function grid()
    {
        $app_key = $this->getAppKey();
        $grid = new Grid(new Feedback());
        $grid->model()->where('tenant_id', SaaSAdmin::user()->id)->where('app_key', $app_key);
        $grid->model()->orderBy('created_at', 'desc');
        $grid->fixColumns(2, -2);

        $grid->tools(function ($tools) {
            $tools->append('<a href="/docs/1.x/apis/feedback_list" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-book"></i> 查看接口文档</a>');
        });
        $grid->column('id', 'ID')->hide();
        $grid->column('user.nickname', '昵称');
        $grid->column('user.avatar', '头像')->image(config('app.url').'/storage/mch/avatar/', 30, 30);
        $grid->column('type', '类型')->display(function ($value) {
            return Feedback::$type[$value];
        })->label([1 => 'primary', 2 => 'warning', 99 => 'default']);
        $grid->column('content', '反馈内容') ->display(function ($value) {
            $shortReply = \Illuminate\Support\Str::limit($value, 30);
            return $shortReply;
        })->modal('反馈内容', function ($model) {
            return "<pre>{$model->content}</pre>";
        });
        $grid->column('contact', '联系方式')->limit(30);

        $grid->column('reply', '回复')
        ->display(function ($value) {
            $shortReply = \Illuminate\Support\Str::limit($value, 30);
            return $shortReply;
        })->modal('回复', function ($model) {
            return "<pre>{$model->reply}</pre>";
        });

        $grid->column('updated_at', '更新时间');
        $grid->column('created_at', '创建时间');
        
        // 添加自定义操作列
        $grid->actions(function ($actions) use ($app_key) {
            // 移除默认的编辑按钮
            $actions->disableEdit();
            
            // 添加回复按钮
            $actions->prepend('<a href="'.admin_url('app/manager/'.$app_key.'/feedback/'.$actions->getKey().'/reply').'"><i class="fa fa-reply"></i> </a>');
        });

        $grid->filter(function ($filter) {
            $filter->equal('type', '类型')->select(Feedback::$type)
            ->config('allowClear', false)
            ->config('minimumResultsForSearch', 'Infinity');
            
            $filter->scope('功能建议')->where('type', 1);
            $filter->scope('问题反馈')->where('type', 2);
            $filter->scope('其他')->where('type', 99);
        });
        
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableCreateButton();
        $grid->disableColumnSelector();
        return $grid;
    }

    public function detail()
    {
        $id = request()->route('list');
        $show = new Show(Feedback::find($id));
        $show->field('type', '类型')->display(function ($value) {
            return Feedback::$type[$value];
        }); 
        $show->field('content', '反馈内容')->as(function ($value) {
            return "<pre>{$value}</pre>";
        })->unescape();
        $show->field('contact', '联系方式');
        $show->field('reply', '回复')->as(function ($value) {
            return "<pre>{$value}</pre>";
        })->unescape();
        $show->field('updated_at', '更新时间');
        $show->field('created_at', '创建时间');
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });
        return $show;
    }

    public function form()
    {
        $form = new Form(new Feedback());
        return $form;
    }

    public function destroy($id)
    {
        $id = request()->route('list');
        $app_key = $this->getAppKey();
        $feedback = Feedback::where('id', $id)->where('app_key', $app_key)->first();
        if(!$feedback) {
            return response()->json(['status' => false, 'message' => '反馈不存在']);
        }
        $feedback->delete();
        return response()->json(['status' => true, 'message' => '删除成功']);
    }
    
    // 添加回复页面
    public function reply($app_key, $id, Content $content)
    {
        $feedback = Feedback::find($id);
        
        return $content
            ->title('回复反馈')
            ->description('ID: ' . $id)
            ->body(view('saas.form.feedback_reply', compact('feedback')));
    }
    
    // 保存回复内容
    public function saveReply($app_key, $id)
    {
        $feedback = Feedback::find($id);
        $reply = request()->input('reply');

        if (empty($reply)) {
            return response()->json(['status' => false, 'message' => '回复内容不能为空']);
        }

        if(mb_strlen($reply) > 200) {
            return response()->json(['status' => false, 'message' => '回复内容不能超过200个字符']);
        }
        
        $feedback->reply = $reply;
        $feedback->save();
        
        return response()->json(['status' => true, 'message' => '回复成功']);
    }
}
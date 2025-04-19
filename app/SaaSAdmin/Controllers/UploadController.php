<?php

namespace App\SaaSAdmin\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        // 手动验证文件，而不是使用 validate 方法，这样可以捕获错误
        $validator = Validator::make($request->all(), [
            'editormd-image-file' => 'required|file|max:512|mimes:jpg,jpeg,gif,png,bmp,webp',
        ], [
            'editormd-image-file.required' => '请选择要上传的图片',
            'editormd-image-file.file' => '上传的文件无效',
            'editormd-image-file.max' => '图片大小不能超过512KB',
            'editormd-image-file.mimes' => '只支持jpg、jpeg、gif、png、bmp、webp格式的图片',
        ]);

        // 检查验证是否通过
        if ($validator->fails()) {
            // 返回符合 Editor.md 预期的错误格式
            return response()->json([
                'success' => 0,
                'message' => $validator->errors()->first(),
            ]);
        }

        try {
            $file = $request->file('editormd-image-file');
            
            // 获取文件内容的MD5值作为文件名
            $md5 = md5_file($file->getPathname());
            
            // 获取文件扩展名
            $extension = $file->getClientOriginalExtension();
            
            // 构建新的文件名：md5值.扩展名
            $newFilename = $md5 . '.' . $extension;
            
            // 获取tenant_id
            $tenant_id = SaaSAdmin::user()->id;
            
            // 设置存储路径
            $path = $tenant_id . '/article';
            $disk = 'SaaSAdmin-mch';
            
            // 检查文件是否已存在（避免重复上传）
            $fullPath = $path . '/' . $newFilename;
            if (!Storage::disk($disk)->exists($fullPath)) {
                // 如果文件不存在，则保存文件
                Storage::disk($disk)->putFileAs($path, $file, $newFilename);
            }
            
            // 返回上传成功的响应
            return response()->json([
                'success' => 1,
                'message' => '上传成功',
                'url' => Storage::disk($disk)->url($fullPath)
            ]);
        } catch (\Exception $e) {
            // 捕获所有可能的异常并返回错误信息
            return response()->json([
                'success' => 0,
                'message' => '上传失败: ' . $e->getMessage(),
            ]);
        }
    }
}
